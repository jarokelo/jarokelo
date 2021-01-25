<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\db\ActivityCache;
use app\models\db\Admin;
use app\modules\admin\models\CommentForm;
use app\modules\admin\models\CommentSearch;
use app\modules\admin\models\ReportActivityTaskSearch;
use app\models\db\AdminCity;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\ReportAttachment;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Handles task related actions.
 *
 * @package app\modules\admin\controllers
 */
class TaskController extends Controller
{
    const TAB_ACTIVE = 'active';
    const TAB_NEW    = 'new';
    const TAB_COMMENT    = 'comment';

    const PICTURE_INSTITUTION = 'institution';
    const PICTURE_USER        = 'user';
    const PICTURE_CLOCK       = 'clock';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns the data for displaying the ReportActivity blocks on the task view.
     *
     * @return array[]
     */
    public static function taskActivityData()
    {
        return [
            ReportActivity::TYPE_ANSWER => [
                'picture' => self::PICTURE_INSTITUTION,
                'message' => [
                    'category' => 'task',
                    'key'      => 'activity.answer',
                    'params'   => [
                        'institution' => ReportActivity::PARAM_INSTITUTION,
                    ],
                ],
            ],
            ReportActivity::TYPE_NO_ANSWER => [
                'picture' => self::PICTURE_CLOCK,
                'message' => [
                    'category' => 'task',
                    'key'      => 'activity.no_answer',
                    'params'   => [
                        'institution' => ReportActivity::PARAM_INSTITUTION,
                        'days'        => ReportActivity::PARAM_ANSWER_WAIT_DAYS,
                    ],
                ],
            ],
            ReportActivity::TYPE_RESPONSE => [
                'picture' => self::PICTURE_USER,
                'message' => [
                    'category' => 'task',
                    'key'      => 'activity.response',
                    'params'   => [
                        'user' => ReportActivity::PARAM_USER_FULL_NAME_LINK,
                    ],
                ],
            ],
            ReportActivity::TYPE_NO_RESPONSE => [
                'picture' => self::PICTURE_CLOCK,
                'message' => [
                    'category' => 'task',
                    'key'      => 'activity.no_response',
                    'params'   => [
                        'user' => ReportActivity::PARAM_USER_FULL_NAME_LINK,
                        'days' => ReportActivity::PARAM_RESPONSE_WAIT_DAYS,
                    ],
                ],
            ],
            ReportActivity::TYPE_NEW_INFO => [
                'picture' => self::PICTURE_USER,
                'message' => [
                    'category' => 'task',
                    'key'      => 'activity.new_info',
                    'params'   => [
                        'user' => ReportActivity::PARAM_USER_FULL_NAME_LINK,
                    ],
                ],
            ],
            ReportActivity::TYPE_INCOMING_EMAIL => [
                'picture' => self::PICTURE_INSTITUTION,
                'custom'  => true,
            ],
        ];
    }

    /**
     * Returns the data for displaying the ReportActivity blocks on the comment view.
     *
     * @return array[]
     */
    public static function commentData()
    {
        return [
            ReportActivity::TYPE_COMMENT => [
                'picture'      => self::PICTURE_USER,
                'show_comment' => true,
                'edit'         => true,
                'hide'         => true,
                'visible'      => true,
                'message'      => [
                    'category' => 'task',
                    'key'      => 'activity.comment',
                    'params'   => [
                        'user' => ReportActivity::PARAM_USER_FULL_NAME_LINK,
                        'report' => ReportActivity::PARAM_REPORT_NAME_LINK,
                    ],
                ],
            ],
        ];
    }

    /**
     * Handles the update of a comment on the Report.
     *
     * @param integer $id The comment's ReportActivity's id
     * @return array|Response
     */
    public function actionEditComment($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT) || !Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var \app\models\db\ReportActivity $activity */
        $activity = $id === null ?
            null :
            ReportActivity::find()
                ->leftJoin(Report::tableName(), '`report_activity`.`report_id` = `report`.`id`')
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where([
                    'report_activity.id' => $id,
                    'report_activity.type' => ReportActivity::TYPE_COMMENT,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->with(['report', 'reportAttachments', 'institution'])
                ->one();

        if ($activity === null) {
            return [
                'success' => false,
            ];
        }

        $data = [
            'reportActivity' => $activity,
            'report'         => $activity->report,
            'comment'        => $activity->comment,
            'attachments'    => [],
        ];

        $originalAttachments = [];

        /* @var \app\modules\admin\models\CommentForm */
        $model = new CommentForm($data);

        foreach ($activity->reportAttachments as $attachment) {
            $originalAttachments[] = $attachment->name;
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (!is_array($model->attachments)) {
                $model->attachments = [];
            }

            $toSave   = [];
            $toDelete = [];

            foreach ($model->attachments as $attachment) {
                if (empty($attachment)) {
                    continue;
                }

                if (!in_array($attachment, $originalAttachments)) {
                    $toSave[] = $attachment;
                }
            }

            foreach ($originalAttachments as $attachment) {
                if (!in_array($attachment, $model->attachments)) {
                    $toDelete[] = $attachment;
                }
            }

            foreach ($toDelete as $name) {
                foreach ($activity->reportAttachments as $attachment) {
                    if ($attachment->name == $name) {
                        $attachment->delete(); // TODO: soft delete?
                    }
                }
            }

            foreach ($toSave as $name) {
                $activity->report->addAttachment(ReportAttachment::TYPE_ATTACHMENT, [
                    'report_activity_id' => $activity->id,
                    'name' => $name,
                ]);
            }

            $activity->comment = $model->comment;
            if (($success = $activity->update(true, ['comment', 'updated_at'])) === false) {
                Yii::error('Unable to update ReportActivity! Errors: ' . print_r($activity->getErrors(), true));
            }

            return [
                'success' => $success !== false,
            ];
        } else {
            $model->attachments = $originalAttachments;
        }

        return [
            'success' => true,
            'html' => $this->renderAjax('_comment_edit', [
                'model' => $model,
            ]),
        ];
    }

    /**
     * Displays the list of tasks for the current Admin.
     *
     * @param string $tab The active tab's id
     * @return string
     */
    public function actionIndex($tab = self::TAB_ACTIVE)
    {
        if (!Yii::$app->user->identity->isSuperAdmin()) {
            if (Yii::$app->user->identity->hasPermissionsOnly([
                Admin::PERM_PR_PAGE_EDIT,
                Admin::PERM_PR_PAGE_DELETE,
            ])) {
                return $this->redirect(['pr-page/index']);
            } elseif (Yii::$app->user->identity->hasPermissionsOnly([
                Admin::PERM_INSTITUTION_VIEW,
                Admin::PERM_INSTITUTION_DELETE,
                Admin::PERM_INSTITUTION_EDIT,
                Admin::PERM_INSTITUTION_ADD,
                Admin::PERM_PR_PAGE_EDIT,
                Admin::PERM_PR_PAGE_DELETE,
            ])) {
                return $this->redirect(['institution/index']);
            }
        }

        $searchModel = new ReportActivityTaskSearch();
        $activeReportsDataProvider = $searchModel->search();

        $newReports = new ActiveDataProvider([
            'query' => Report::find()
                ->filterNew()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->andWhere(['admin_city.admin_id' => Yii::$app->user->id])
                ->with(
                    [
                        'user',
                        'admin',
                        'district',
                        'city',
                        'institution',
                        'reportCategory',
                        'reportAttachments',
                    ]
                )
                ->orderBy(['created_at' => SORT_ASC]),
        ]);

        $searchModel = new CommentSearch();
        $commentsDataProvider = $searchModel->search();

        return $this->render('index', [
            'activeReports' => $activeReportsDataProvider,
            'newReports' => $newReports,
            'comments' => $commentsDataProvider,
            'tab' => $tab,
        ]);
    }

    /**
     * Assigns an incoming email to a Report.
     *
     * @param integer $id The ReportActivity's id
     * @return array|Response
     */
    public function actionAssign($id)
    {
        /* @var \app\models\db\ReportActivity $model */
        $model = ReportActivity::find()->where([
            'id' => $id,
            'type' => ReportActivity::TYPE_INCOMING_EMAIL,
            'report_id' => null,
        ])->one();

        if ($model === null) {
            return $this->redirect(['task/index']);
        }

        $model->setScenario('assign');

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save(true, ['report_id', 'updated_at', 'is_active_task'])) {
                $report = Report::findOne(['id' => $model->report_id]);

                if ($report === null) {
                    Yii::$app->session->setFlash('danger', 'Unable to find report: ' . $model->report_id);
                    return $this->redirect(['task/index']);
                }

                $activity = null;
                $data = [
                    'email_id' => $model->email_id,
                    'comment' => $model->getCommentContent(),
                    'original_value' => $model->getCommentContent(),
                    'institution_id' => $model->institution_id,
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => time(),
                ];
                $type = null;

                switch ($report->status) {
                    case Report::STATUS_WAITING_FOR_ANSWER:
                    case Report::STATUS_RESOLVED:
                    case Report::STATUS_UNRESOLVED:
                        $type = ReportActivity::TYPE_ANSWER;
                        break;

                    case Report::STATUS_WAITING_FOR_RESPONSE:
                        $type = ReportActivity::TYPE_RESPONSE;
                        break;

                    case Report::STATUS_WAITING_FOR_SOLUTION:
                        $type = ReportActivity::TYPE_NEW_INFO;
                        break;
                }

                if ($type !== null) {
                    $activity = $report->constructActivity($type, $data);
                    $activity->detachBehavior('timestamp');
                }

                $attachments = ReportAttachment::findAll(['report_activity_id' => $model->id]);

                if ($activity !== null && $activity->save()) {
                    $activity->assignAttachments($attachments);
                }
            } else {
                Yii::error('Unable to assign ReportActivity to a Report! Errors: ' . print_r($model->getErrors(), true));
            }

            return $this->redirect(['task/index']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model->is_active_task = 0;
        return [
            'success' => true,
            'html' => $this->renderAjax('_assign', [
                'model' => $model,
            ]),
        ];
    }

    /**
     * Hides a ReportActivity from active task list
     *
     * @param integer $id ReportActivity id
     * @param string $redirectTab
     * @return array|Response
     */
    public function actionApprove($id, $redirectTab = null)
    {
        $model = ReportActivity::findOne($id);

        $model->is_active_task = 0;
        $model->save(false, ['is_active_task']);

        if ($model->type == ReportActivity::TYPE_NO_ANSWER) {
            // Invalidating activity cache.
            ActivityCache::deleteAll(['report_activity_id' => $model->id]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
            ];
        }

        if ($redirectTab === null) {
            return $this->redirect(['/admin/report/view', 'id' => $model->report_id]);
        }

        return $this->redirect(['/admin/task', 'tab' => $redirectTab]);
    }
}
