<?php
/**
 * Created by PhpStorm.
 * User: laci
 * Date: 2018.05.10.
 * Time: 16:11
 */

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "admin_pr_page".
 *
 * @property integer $pr_page_id
 * @property integer $admin_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property PrPage $prPage
 */
class AdminPrPage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_pr_page';
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
            [['pr_page_id', 'admin_id'], 'required'],
            [['pr_page_id', 'admin_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pr_page_id' => Yii::t('data', 'pr_page_admin.pr_page_id'),
            'admin_id'   => Yii::t('data', 'pr_page_admin.admin_id'),
            'created_at' => Yii::t('data', 'pr_page_admin.created_at'),
            'updated_at' => Yii::t('data', 'pr_page_admin.updated_at'),
        ];
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
     * The Admin relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }
}
