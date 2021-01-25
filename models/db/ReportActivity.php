<?php

namespace app\models\db;

use app\components\EmailHelper;
use app\components\helpers\Html;
use app\components\helpers\Link;
use app\components\helpers\S3;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "report_activity".
 *
 * @property integer $id
 * @property integer $report_id
 * @property integer $admin_id
 * @property integer $user_id
 * @property integer $institution_id
 * @property integer $notification_id
 * @property integer $email_id
 * @property string $type
 * @property string $comment
 * @property integer $visible
 * @property string $original_value
 * @property string $new_value
 * @property boolean $is_latest
 * @property boolean $is_hidden
 * @property boolean $is_active_task
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Admin $admin
 * @property ReportAttachment[] $reportAttachments
 * @property Email $email
 * @property Institution $institution
 * @property Notification $notification
 * @property Report $report
 * @property User $user
 */
class ReportActivity extends ActiveRecord
{
    // Activity Types
    const TYPE_OPEN = 'open';
    const TYPE_EDITING = 'editing';
    const TYPE_DELETE = 'delete';
    const TYPE_RESOLVE = 'resolve';
    const TYPE_CLOSE = 'close';
    const TYPE_ANSWER = 'answer';
    const TYPE_NO_ANSWER = 'no_answer';
    const TYPE_COMMENT = 'comment';
    const TYPE_RESPONSE = 'response';
    const TYPE_NO_RESPONSE = 'no_response';
    const TYPE_SEND_TO_AUTHORITY = 'send_to_authority';
    const TYPE_GET_USER_RESPONSE = 'get_user_response';
    const TYPE_INCOMING_EMAIL = 'incoming_email';
    const TYPE_NEW_INFO = 'new_info';
    const TYPE_NO_NEW_INFO = 'no_new_info';
    const TYPE_MOD_NAME = 'mod_name';
    const TYPE_MOD_CATEGORY = 'mod_category';
    const TYPE_MOD_DESCRIPTION = 'mod_description';
    const TYPE_MOD_INSTITUTION = 'mod_institution';
    const TYPE_MOD_LOCATION = 'mod_location';
    const TYPE_MOD_STATUS = 'mod_status';
    const TYPE_GET_NEW_INFO = 'get_new_info';

    // Picture Types
    const PICTURE_NONE = 'none';
    const PICTURE_USER = 'user';
    const PICTURE_USER_NO_ANONYMOUS = 'user_no_anonymous';
    const PICTURE_ADMIN = 'admin';
    const PICTURE_INSTITUTION = 'institution';

    // Message Parameters
    const PARAM_INSTITUTION = 'institution';
    const PARAM_USER_FULL_NAME_LINK = 'user_full_name_link';
    const PARAM_USER_FULL_NAME_LINK_NO_ANONYMOUS = 'user_full_name_link_no_anonymous';
    const PARAM_ADMIN_FULL_NAME_LINK = 'admin_full_name_link';
    const PARAM_REPORT_NAME_LINK = 'report_full_name_link';
    const PARAM_ANSWER_WAIT_DAYS = 'answer_wait_days';
    const PARAM_RESPONSE_WAIT_DAYS = 'response_wait_days';
    const PARAM_NEW_INFO_WAIT_DAYS = 'new_info_wait_days';
    const PARAM_OLD_VALUE = 'old_value';
    const PARAM_NEW_VALUE = 'new_value';
    const PARAM_OLD_VALUE_CATEGORY = 'old_value_category';
    const PARAM_NEW_VALUE_CATEGORY = 'new_value_category';
    const PARAM_OLD_VALUE_INSTITUTION = 'old_value_institution';
    const PARAM_OLD_VALUE_STATUS = 'old_value_status';
    const PARAM_NEW_VALUE_STATUS = 'new_value_status';
    const PARAM_NEW_VALUE_REASON = 'new_value_reason';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_activity';
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
            [['comment', 'new_value'], 'filter', 'filter' => '\app\components\helpers\Html::replaceMultipleLineBreaks'],
            [['comment', 'new_value'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['type'], 'required', 'except' => ['assign']],
            [['report_id', 'admin_id', 'user_id', 'institution_id', 'notification_id', 'email_id', 'visible', 'created_at', 'updated_at'], 'integer', 'except' => ['assign']],
            [['comment', 'original_value', 'new_value'], 'string', 'except' => ['assign']],
            [['type'], 'string', 'max' => 255, 'except' => ['assign']],
            [['report_id'], 'required', 'on' => ['assign']],
            [['report_id', 'is_active_task'], 'integer', 'on' => ['assign']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'report_activity.id'),
            'report_id' => Yii::t('data', 'report_activity.report_id'),
            'admin_id' => Yii::t('data', 'report_activity.admin_id'),
            'user_id' => Yii::t('data', 'report_activity.user_id'),
            'institution_id' => Yii::t('data', 'report_activity.institution_id'),
            'attachment_id' => Yii::t('data', 'report_activity.attachment_id'),
            'notification_id' => Yii::t('data', 'report_activity.notification_id'),
            'email_id' => Yii::t('data', 'report_activity.email_id'),
            'type' => Yii::t('data', 'report_activity.type'),
            'comment' => Yii::t('data', 'report_activity.comment'),
            'visible' => Yii::t('data', 'report_activity.visible'),
            'original_value' => Yii::t('data', 'report_activity.original_value'),
            'new_value' => Yii::t('data', 'report_activity.new_value'),
            'created_at' => Yii::t('data', 'report_activity.created_at'),
            'updated_at' => Yii::t('data', 'report_activity.updated_at'),
        ];
    }

    /**
     * Returns the type display data for every activity type.
     *
     * @return array[]
     */
    public static function typeDisplayData()
    {
        return [
            self::TYPE_OPEN => [
                'picture' => self::PICTURE_USER,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.open',
                    'params' => [
                        'user' => self::PARAM_USER_FULL_NAME_LINK,
                    ],
                ],
            ],
            self::TYPE_EDITING => [
                'picture' => self::PICTURE_ADMIN,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.editing',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                    ],
                ],
            ],
            self::TYPE_DELETE => [
                'picture' => self::PICTURE_ADMIN,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.delete',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                    ],
                ],
            ],
            self::TYPE_RESOLVE => [
                'picture' => self::PICTURE_ADMIN,
                'show_comment' => true,
                'edit' => true,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.resolve',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                    ],
                ],
            ],
            self::TYPE_CLOSE => [
                'picture' => self::PICTURE_ADMIN,
                'show_comment' => true,
                'edit' => true,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.close',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                        'reason' => self::PARAM_NEW_VALUE_REASON,
                    ],
                ],
            ],
            self::TYPE_ANSWER => [
                'picture' => self::PICTURE_INSTITUTION,
                'show_comment' => true,
                'edit' => true,
                'hide' => true,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.answer',
                    'params' => [
                        'institution' => self::PARAM_INSTITUTION,
                    ],
                ],
            ],
            self::TYPE_NO_ANSWER => [
                'picture' => self::PICTURE_INSTITUTION,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.no_answer',
                    'params' => [
                        'institution' => self::PARAM_INSTITUTION,
                        'days' => self::PARAM_ANSWER_WAIT_DAYS,
                    ],
                ],
            ],
            self::TYPE_COMMENT => [
                'picture' => self::PICTURE_USER_NO_ANONYMOUS,
                'show_comment' => true,
                'edit' => true,
                'hide' => true,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.comment',
                    'params' => [
                        'user' => self::PARAM_USER_FULL_NAME_LINK_NO_ANONYMOUS,
                    ],
                ],
            ],
            self::TYPE_RESPONSE => [
                'picture' => self::PICTURE_USER,
                'show_comment' => true,
                'edit' => true,
                'hide' => true,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.response',
                    'params' => [
                        'user' => self::PARAM_USER_FULL_NAME_LINK,
                    ],
                ],
            ],
            self::TYPE_NO_RESPONSE => [
                'picture' => self::PICTURE_USER,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.no_response',
                    'params' => [
                        'user' => self::PARAM_USER_FULL_NAME_LINK,
                        'days' => self::PARAM_RESPONSE_WAIT_DAYS,
                    ],
                ],
            ],
            self::TYPE_SEND_TO_AUTHORITY => [
                'picture' => self::PICTURE_ADMIN,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.send_to_authority',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                        'institution' => self::PARAM_INSTITUTION,
                    ],
                ],
            ],
            self::TYPE_GET_USER_RESPONSE => [
                'picture' => self::PICTURE_USER,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.get_user_response',
                    'params' => [
                        'user' => self::PARAM_USER_FULL_NAME_LINK,
                    ],
                ],
            ],
            self::TYPE_NEW_INFO => [
                'picture' => self::PICTURE_USER,
                'show_comment' => true,
                'edit' => true,
                'visible' => true,
                'hide' => true,
            ],
            self::TYPE_NO_NEW_INFO => [
                'picture' => self::PICTURE_USER,
            ],
            self::TYPE_MOD_NAME => [
                'picture' => self::PICTURE_ADMIN,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.mod_name',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                        'oldValue' => self::PARAM_OLD_VALUE,
                        'newValue' => self::PARAM_NEW_VALUE,
                    ],
                ],
            ],
            self::TYPE_MOD_CATEGORY => [
                'picture' => self::PICTURE_ADMIN,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.mod_category',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                        'oldValue' => self::PARAM_OLD_VALUE_CATEGORY,
                        'newValue' => self::PARAM_NEW_VALUE_CATEGORY,
                    ],
                ],
            ],
            self::TYPE_MOD_DESCRIPTION => [
                'picture' => self::PICTURE_ADMIN,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.mod_description',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                        'oldValue' => self::PARAM_OLD_VALUE,
                        'newValue' => self::PARAM_NEW_VALUE,
                    ],
                ],
            ],
            self::TYPE_MOD_INSTITUTION => [
                'picture' => self::PICTURE_ADMIN,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.mod_institution',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                        'oldValue' => self::PARAM_OLD_VALUE_INSTITUTION,
                        'newValue' => self::PARAM_INSTITUTION,
                    ],
                ],
            ],
            self::TYPE_MOD_LOCATION => [
                'picture' => self::PICTURE_ADMIN,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.mod_location',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                        'oldValue' => self::PARAM_OLD_VALUE,
                        'newValue' => self::PARAM_NEW_VALUE,
                    ],
                ],
            ],
            self::TYPE_MOD_STATUS => [
                'picture' => self::PICTURE_ADMIN,
                'visible' => true,
                'message' => [
                    'category' => 'report',
                    'key' => 'activity.mod_status',
                    'params' => [
                        'admin' => self::PARAM_ADMIN_FULL_NAME_LINK,
                        'oldValue' => self::PARAM_OLD_VALUE_STATUS,
                        'newValue' => self::PARAM_NEW_VALUE_STATUS,
                    ],
                ],
            ],
            self::TYPE_GET_NEW_INFO => [
                'picture' => self::PICTURE_USER,
                'hide' => true,
            ],
        ];
    }

    /**
     * Calculates the required translate parameters for the current ReportActivity.
     *
     * @param boolean $frontend if true, the User's and Admin's names won't be links
     * @return string[] the calculated parameters
     */
    public function calculateParameters($frontend)
    {
        $typeDisplayData = static::typeDisplayData();

        $displayData = isset($typeDisplayData[$this->type]) ? $typeDisplayData[$this->type] : null;
        if ($displayData === null) {
            return [];
        }

        return static::resolveParameters($frontend, $this, isset($displayData['message']) ? $displayData['message'] : null);
    }

    /**
     * Resolves the parameters for the specified message.
     *
     * @param boolean $frontend if true, the User's and Admin's names won't be links
     * @param \app\models\db\ReportActivity $model The ReportActivity instance
     * @param array $messageData The message's data
     * @return string[] the calculated parameters
     */
    public static function resolveParameters($frontend, $model, $messageData)
    {
        if ($messageData === null || !is_array($messageData) || !isset($messageData['params']) || !is_array($messageData['params'])) {
            return [];
        }

        $statuses = Report::statuses();
        $categories = ReportCategory::getList();
        $reasons = Report::closeReasons();

        $institutionCache = [];
        $params = [];

        if ($model->institution_id !== null && $model->institution !== null) {
            $institutionCache[$model->institution_id] = $model->institution;
        }

        $userLink = !$frontend && Yii::$app->user->identity->hasPermission(Admin::PERM_USER_EDIT);
        $adminLink = !$frontend && Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_EDIT);
        $reportLink = !$frontend && Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT);

        foreach ($messageData['params'] as $key => $param) {
            if (!is_string($key)) {
                $key = $param;
            }

            switch ($param) {
                case self::PARAM_INSTITUTION:
                    $params[$key] = ($model->institution_id === null || $model->institution === null) ? Yii::t('report', 'no-institution-name') : $model->institution->name;
                    break;
                case self::PARAM_USER_FULL_NAME_LINK:
                case self::PARAM_USER_FULL_NAME_LINK_NO_ANONYMOUS:
                    $isOwner = ArrayHelper::getValue($model, 'report.user_id') == $model->user_id;
                    $isAnonymous = ArrayHelper::getValue($model, 'report.anonymous', false);
                    $params[$key] = ($model->user_id === null || empty($model->user) || ($isOwner && $isAnonymous)) ? Yii::t('report', 'report.anonymous') : ($userLink ? Html::a($model->user->getFullName(), ['user/update', 'id' => $model->user_id], ['target' => '_blank']) : Html::a($model->user->getFullName(), Link::to([Link::PROFILES, $model->user->id]), ['target' => '_blank', 'class' => 'link link--black']));
                    break;

                case self::PARAM_ADMIN_FULL_NAME_LINK:
                    $params[$key] = ($model->admin_id === null || $model->admin === null) ? Yii::t('report', 'no-administrator-name') : ($adminLink ? Html::a($model->admin->getFullName(), ['admin/view', 'id' => $model->admin_id], ['target' => '_blank']) : $model->admin->getFullName());
                    break;

                case self::PARAM_REPORT_NAME_LINK:
                    $params[$key] = ($model->report_id === null || $model->report === null) ? Yii::t('report', 'no-report-name') : ($reportLink ? Html::a($model->report->name, ['report/view', 'id' => $model->report_id], ['target' => '_blank']) : $model->report->name);
                    break;

                case self::PARAM_ANSWER_WAIT_DAYS:
                    $params[$key] = Yii::$app->params['answerWaitDays'];
                    break;

                case self::PARAM_RESPONSE_WAIT_DAYS:
                    $params[$key] = Yii::$app->params['responseWaitDays'];
                    break;

                case self::PARAM_NEW_INFO_WAIT_DAYS:
                    $params[$key] = Yii::$app->params['newInfoWaitDays'];
                    break;

                case self::PARAM_OLD_VALUE:
                    $params[$key] = $model->original_value;
                    break;

                case self::PARAM_NEW_VALUE:
                    $params[$key] = $model->new_value;
                    break;

                case self::PARAM_OLD_VALUE_CATEGORY:
                    $params[$key] = isset($categories[$model->original_value]) ? $categories[$model->original_value] : '';
                    break;

                case self::PARAM_NEW_VALUE_CATEGORY:
                    $params[$key] = isset($categories[$model->new_value]) ? $categories[$model->new_value] : '';
                    break;

                case self::PARAM_OLD_VALUE_INSTITUTION:
                    /* @var \app\models\db\Institution $institution */
                    $institution = null;

                    if (!empty($model->original_value)) {
                        $id = intval($model->original_value);

                        if (array_key_exists($id, $institutionCache)) {
                            $institution = $institutionCache[$id];
                        } else {
                            $institution = $institutionCache[$id] = Institution::findOne(['id' => $id]);
                        }
                    }

                    $params[$key] = $institution === null ? Yii::t('report', 'no-institution-name') : $institution->name;
                    break;

                case self::PARAM_OLD_VALUE_STATUS:
                    $params[$key] = $statuses[$model->original_value];
                    break;

                case self::PARAM_NEW_VALUE_STATUS:
                    $params[$key] = $statuses[$model->new_value];
                    break;

                case self::PARAM_NEW_VALUE_REASON:
                    $params[$key] = $reasons[$model->new_value];
                    break;
            }
        }
        return $params;
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

    /**
     * The Email relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmail()
    {
        return $this->hasOne(Email::className(), ['id' => 'email_id']);
    }

    /**
     * The Institution relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institution_id']);
    }

    /**
     * The Notification relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notification::className(), ['id' => 'notification_id']);
    }

    /**
     * The Report relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Report::className(), ['id' => 'report_id']);
    }

    /**
     * The ReportAttachments relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportAttachments()
    {
        return $this->hasMany(ReportAttachment::className(), ['report_activity_id' => 'id']);
    }

    /**
     * The User relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function isExist($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Returns the picture's URL for rendering this ReportActivity's block.
     *
     * @return array|string the picture's URL or an empty string
     */
    public function getPictureUrl($isRelativeUrl = true)
    {
        $typeData = static::typeDisplayData();
        $type = '';

        if (isset($typeData[$this->type]['picture'])) {
            $type = $typeData[$this->type]['picture'];
        }

        $relativeUrl = '';

        switch ($type) {
            case self::PICTURE_ADMIN:
                $relativeUrl = Admin::getPictureUrl($this->admin_id);
                break;
            case self::PICTURE_INSTITUTION:
                $relativeUrl = Yii::getAlias('@web/images/institution/placeholder.png');
                break;
            case self::PICTURE_USER:
            case self::PICTURE_USER_NO_ANONYMOUS:
            default:
                $isOwner = ArrayHelper::getValue($this, 'report.user_id') == $this->user_id;
                $isAnonymous = ArrayHelper::getValue($this, 'report.anonymous', false);
                $relativeUrl = User::getPictureUrl($isOwner && $isAnonymous ? null : $this->user_id);
                break;
        }

        if ($isRelativeUrl) {
            return $relativeUrl;
        }

        return Url::to($relativeUrl, true);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert && $this->type != self::TYPE_COMMENT) {
            if ($this->report_id !== null) {
                static::updateAll(['is_latest' => 0], ['report_id' => $this->report_id]);
            }

            $this->is_latest = 1;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // skipping email trigger if prev action was approving the task (after seen)
        // or pathInfo doesn't exists (for example cron controller timeout check process)
        if (
            !isset(Yii::$app->request->pathInfo)
            || Yii::$app->request->pathInfo != 'citizen/task/approve'
        ) {
            EmailHelper::trigger($this);
        }
    }

    public function isNotVisible()
    {
        $typeDisplayData = static::typeDisplayData();
        return $this->is_hidden || !isset($typeDisplayData[$this->type]) || !isset($typeDisplayData[$this->type]['visible']) || $typeDisplayData[$this->type]['visible'] !== true || $this->visible == 0;
    }

    public function showMoreButton()
    {
        $cutLength = ArrayHelper::getValue(Yii::$app->params, 'comments.cutAfterCharacterLength', 200);
        $commentFirstPart = StringHelper::truncate($this->comment, $cutLength, null, 'UTF-8');

        return mb_strlen($this->comment) > $cutLength
            && StringHelper::countWords($this->comment) != StringHelper::countWords($commentFirstPart);
    }

    public function showOverlay()
    {
        return mb_strlen($this->comment) > ArrayHelper::getValue(Yii::$app->params, 'comments.overlayAfterCharacterLength', 500);
    }

    /**
     * 0-200: render the whole comment
     * 200-500: render to 200 character, then show the rest
     * 500+: render to 200 then show the rest in overlay
     *
     * @param bool $preview
     * @return string
     */
    public function renderComment($preview = true)
    {
        $comment = $this->getCommentContent();

        if (!$preview) {
            return Html::formatText($comment, 'link--default link--info link--cutted');
        }

        $cutLength = ArrayHelper::getValue(Yii::$app->params, 'comments.cutAfterCharacterLength', 200);
        $overlayLength = ArrayHelper::getValue(Yii::$app->params, 'comments.overlayAfterCharacterLength', 500);

        $commentFirstPart = StringHelper::truncate($comment, $cutLength, null, 'UTF-8');

        $wordCountComment = StringHelper::countWords($comment);
        $wordCountCommentFirstPart = StringHelper::countWords($commentFirstPart);

        $commentLength = mb_strlen($comment);

        if ($commentLength < $cutLength || $wordCountComment == $wordCountCommentFirstPart) {
            return Html::formatText($comment, 'link--default link--info link--cutted');
        }

        $firstPart = Html::tag('span', $commentFirstPart);
        $moreSign = Html::tag('span', '...', ['class' => 'more-sign', 'data-id' => $this->id]);

        if ($commentLength < $overlayLength) {
            $restPart = mb_substr($comment, mb_strlen($commentFirstPart), null, 'UTF-8');

            return Html::tag(
                'div',
                Html::formatText($firstPart, 'link--default link--info') . $moreSign . Html::tag('span', Html::formatText($restPart, 'link--default link--info'), [
                    'style' => 'display:none;',
                    'class' => 'comment__rest-part',
                    'data-id' => $this->id,
                ])
            );
        }

        return Html::tag('div', Html::tag('span', Html::formatText($firstPart, 'link--default link--info link link--cutted')) . $moreSign);
    }

    public function getMessage()
    {
        $displayData = ArrayHelper::getValue(static::typeDisplayData(), $this->type);

        if ($displayData === null) {
            return false;
        }

        $messageData = isset($displayData['message']) ? $displayData['message'] : null;

        $message = '';

        if ($messageData !== null && isset($messageData['category']) && isset($messageData['key'])) {
            $message = Yii::t($messageData['category'], $messageData['key'], $this->calculateParameters(true));
        }

        return $message;
    }

    public function showComment()
    {
        $displayData = ArrayHelper::getValue(static::typeDisplayData(), $this->type, []);

        return ArrayHelper::getValue($displayData, 'show_comment', false) && !empty($this->getCommentContent());
    }

    public function getCommentContent()
    {
        if (!empty($this->comment)) {
            return $this->comment;
        }

        if ($this->email !== null) {
            return $this->email->body;
        }

        return null;
    }

    public function getIncomingEmailPictureUrl($isRelativeUrl = true)
    {
        if ($this->type !== self::TYPE_INCOMING_EMAIL) {
            return $this->getPictureUrl($isRelativeUrl);
        }

        $relativeUrl = null;

        if ($this->admin_id) {
            $relativeUrl = Admin::getPictureUrl($this->admin_id);
        } elseif ($this->user_id) {
            $isOwner = ArrayHelper::getValue($this, 'report.user_id') == $this->user_id;
            $isAnonymous = ArrayHelper::getValue($this, 'report.anonymous', false);
            $relativeUrl = User::getPictureUrl($isOwner && $isAnonymous ? null : $this->user_id);
        } elseif ($this->institution_id) {
            $relativeUrl = Yii::getAlias('@web/images/institution/placeholder.png');
        } else {
            $relativeUrl = User::getPictureUrl(null);
        }

        if ($isRelativeUrl) {
            return $relativeUrl;
        }

        return Url::to($relativeUrl, true);
    }

    /**
     * @param $attachments ReportAttachment[]
     */
    public function assignAttachments($attachments)
    {
        foreach ($attachments as $attachment) {
            // Capture file url from temp folder
            $from = $attachment->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_ORIGINAL, true);
            $attachment->report_activity_id = $this->id;
            $attachment->report_id = $this->report_id;

            if ($attachment->save()) {
                $sourcePath = Yii::getAlias('@app/web/files/report/temp');

                if ($attachment->isStorageS3()) {
                    $s3 = new S3();
                    $to = $attachment->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_ORIGINAL, true);
                    $s3->copy($from, $to, true);
                    $s3->delete($from);
                } else {
                    $destinationPath = $attachment->getAttachmentUrl();
                    FileHelper::createDirectory($destinationPath);
                    rename($sourcePath . "/{$attachment->name}", $destinationPath . "/{$attachment->name}");
                }
            }
        }
    }

    public function getAllFollowersAndReportOwnerWithoutActivityOwner()
    {
        // add followers if notification settings are set so
        // and the follower is not the activity owner
        $users = $this->report->getFollowers()
            ->where(['not', ['id' => [$this->report->user_id, $this->user_id]]])
            ->andWhere(['notification' => User::NOTIFICATION_IMMEDIATE, 'notification_followed' => 1])
            ->all();
        // add owner if notification settings are set so
        if ($this->user_id != $this->report->user_id && $this->report->user->notification == User::NOTIFICATION_IMMEDIATE && $this->report->user->notification_owned == 1) {
            array_unshift($users, $this->report->user);
        }

        return $users;
    }

    /**
     * @return string
     */
    public function getOwnerName()
    {
        // only users can have comment type
        if ($this->type == self::TYPE_COMMENT) {
            // hiding users name
            return Yii::t('report', 'report.pdf.comments.name.user');
        }

        if ($this->institution) {
            return $this->institution->name;
        }

        // by default displaying placeholder office response text
        return Yii::t('report', 'response_by_institution');
    }
}
