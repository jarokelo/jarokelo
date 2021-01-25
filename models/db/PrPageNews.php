<?php
/**
 * Created by PhpStorm.
 * User: laci
 * Date: 2018.05.10.
 * Time: 16:12
 */

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;
use app\components\helpers\Html;

/**
 * This is the model class for table "pr_page_news".
 *
 * @property integer $id
 * @property integer $pr_page_id
 * @property string $title
 * @property string $image_file_name
 * @property string $text
 * @property integer $status
 * @property integer $highlighted
 * @property integer $published_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property PrPage $pr_page
 */
class PrPageNews extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const HIGHLIGHTED_FALSE = 0;
    const HIGHLIGHTED_TRUE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pr_page_news';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['highlighted', 'pr_page_id', 'published_at', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'image_file_name', 'text'], 'string'],
            [['title', 'text'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('pr_page_news', 'update.title'),
            'text' => Yii::t('pr_page_news', 'update.text'),
            'highlighted' => Yii::t('pr_page_news', 'update.highlighted'),
            'image_file_name' => Yii::t('pr_page_news', 'update.image_file_name'),
            'published_at' => Yii::t('pr_page_news', 'update.published_at'),
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = $scenarios[self::SCENARIO_DEFAULT];
        $scenarios[self::SCENARIO_UPDATE] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    /**
     * Returns the statuses available to this current Pr page news.
     *
     * @return string[]
     */
    public static function statuses()
    {
        $statuses = [
            self::STATUS_ACTIVE   => Yii::t('rule', 'status.active'),
            self::STATUS_INACTIVE => Yii::t('rule', 'status.inactive'),
        ];

        return $statuses;
    }

    /**
     * Return True, if the News is highlighted.
     *
     * @return bool
     */
    public function isHighlighted()
    {
        return $this->highlighted == self::HIGHLIGHTED_TRUE;
    }

    /**
     * Returns the Highlighted News by Pr page id.
     *
     * @param $id
     * @return array|null|ActiveRecord
     */
    public function getHighlightedNewsByPrPageId($id)
    {
        $highlightedNews = static::find()
        ->where(['highlighted' => self::HIGHLIGHTED_TRUE, 'pr_page_id' => $id])
        ->andWhere('published_at <= ' . strtotime(date('Y-m-d') . ' 00:00:00'))
        ->one();

        return $highlightedNews;
    }

    /**
     * Sets the Highlighted status of current News to True and sets all other Highlighted status of Highlighted News to false.
     *
     * @param integer $id The current News Id.
     * @return bool True, if the changed successfully.
     */
    public function setHighlightedNews($id)
    {
        $newHighlightedNews = static::findOne(['id' => $id]);

        if ($newHighlightedNews->isHighlighted()) {
            $newHighlightedNews->highlighted = self::HIGHLIGHTED_FALSE;
        } else {
            $prevHighligtedNews = static::findOne(['highlighted' => self::HIGHLIGHTED_TRUE]);
            if ($prevHighligtedNews) {
                $prevHighligtedNews->highlighted = self::HIGHLIGHTED_FALSE;
                if (!$prevHighligtedNews->save()) {
                    return false;
                }
            }
            $newHighlightedNews->highlighted = self::HIGHLIGHTED_TRUE;
        }

        return $newHighlightedNews->save();
    }

    /**
     * The Pr page relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrPage()
    {
        return $this->hasOne(PrPage::className(), ['id' => 'pr_page_id']);
    }

    /**
     * Returns the Pr page relation as array.
     *
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function getPrPageAsArray()
    {
        $array = $this->getPrPage()
            ->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();

        return array_pop($array);
    }

    /**
     * Returns all News by Pr page Id.
     *
     * @param integer $id Pr page id.
     * @return ActiveDataProvider
     */
    public function getAllNewsByPrPageId($id)
    {
        $query = static::find()
            ->where(['pr_page_id' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    /**
     * Returns all published News by Pr page id.
     *
     * @param $id integer Pr page id.
     * @return array
     */
    public function getPublishedByPrPageId($id)
    {
        $model = $this->getHighlightedNewsByPrPageId($id);

        $result = [];
        $limit = 4;
        if ($model) {
            $limit = 3;
            $result[] = $model;
        }

        $query = static::find()
            ->where(['pr_page_id' => $id, 'status' => self::STATUS_ACTIVE, 'highlighted' => self::HIGHLIGHTED_FALSE])
            ->andWhere('published_at <= ' . strtotime(date('Y-m-d') . ' 00:00:00'))
            ->orderBy(['published_at' => SORT_DESC])
            ->limit($limit)
            ->all();

        foreach ($query as $item) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Set the Status to Active
     */
    public function activate()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->save(false);
    }

    /**
     * Set the Status to Inactive
     */
    public function inactivate()
    {
        $this->status = self::STATUS_INACTIVE;
        $this->save(false);
    }

    /**
     * Returns an URL to the specified Pr page news's image.
     *
     * @param \app\models\db\PrPageNews $model The Pr page news instance
     * @return string The URL to the Pr page news's image.
     */
    public static function getImageUrl($model = null)
    {
        $imgFileName = null;

        if ($model !== null && $model instanceof static) {
            $imgFileName = $model->image_file_name;
        }

        $path = '@app/web/files/pr-page-news';

        $imgPath = Yii::getAlias("{$path}/{$imgFileName}");
        if (!file_exists($imgPath) || !is_file($imgPath)) {
            return null;
        }

        return Yii::getAlias("@web/files/pr-page-news/{$imgFileName}");
    }

    /**
     * 0-200: render the whole comment
     * 200-500: render to 200 character, then show the rest
     * 500+: render to 200 then show the rest in overlay
     *
     * @return string
     */
    public function renderText()
    {
        $text = $this->text;

        $cutLength = null;
        if ($this->image_file_name) {
            $cutLength = ArrayHelper::getValue(Yii::$app->params, 'pr_page_news.cutAfterCharacterLength.short', 150) - mb_strlen($this->title);
        } else {
            $cutLength = ArrayHelper::getValue(Yii::$app->params, 'pr_page_news.cutAfterCharacterLength.long', 400) - mb_strlen($this->title);
        }

        $textFirstPart = StringHelper::truncate($text, $cutLength, null, 'UTF-8');

        $textLength = mb_strlen($text);

        if ($textLength < $cutLength) {
            return Html::formatText($text, 'link--default link--info link--cutted');
        }

        $firstPart = Html::tag('span', $textFirstPart);
        $moreSign = Html::tag('span', '...', ['class' => 'more-sign', 'data-id' => $this->id]);

        return Html::tag('div', Html::tag('span', Html::formatText($firstPart, 'link--default link--info link link--cutted')) . $moreSign);
    }
}
