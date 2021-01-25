<?php

namespace app\modules\admin\controllers;

use app\components\ActiveForm;
use app\models\db\ReportAttachmentOriginal;
use app\models\ReportMapLayer;
use yii\base\Model;
use yii\helpers\Url;
use app\models\db\Admin;
use app\models\db\AdminCity;
use app\models\db\Institution;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\ReportAttachment;
use app\models\db\Rule;
use app\models\db\User;
use app\modules\admin\models\AnswerForm;
use app\modules\admin\models\CommentForm;
use app\modules\admin\models\ReportSearch;
use app\modules\admin\models\SendForm;
use app\modules\admin\models\StatisticsDistrictSearch;
use app\modules\admin\models\StatisticsInstitutionSearch;
use app\modules\admin\models\StatisticsSearch;
use app\modules\admin\models\StatusChange;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * Handles Report related actions.
 *
 * @package app\modules\admin\controllers
 */
class ReportController extends Controller
{
    const TAB_INSTITUTIONS = 'institutions';
    const TAB_DISTRICTS = 'districts';
    const TAB_REPORTS = 'reports';

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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'au.upload.comment.attachment' => [
                'class' => 'app\components\jqueryupload\UploadAction',
                'paramName' => Html::getInputName(new CommentForm(), 'attachments'),
                'createDirs' => true,
                'maxSize' => 10 * 1024 * 1024,
                'uploadDest' => '@runtime/upload-tmp/report',
                'thumbnailCallback' => false,
            ],
            'au.upload.answer.attachment' => [
                'class' => 'app\components\jqueryupload\UploadAction',
                'paramName' => Html::getInputName(new AnswerForm(), 'attachments'),
                'createDirs' => true,
                'maxSize' => 10 * 1024 * 1024,
                'uploadDest' => '@runtime/upload-tmp/report',
                'thumbnailCallback' => false,
            ],
            'au.thumb' => [
                'class' => 'app\components\jqueryupload\UploadActionThumbAction',
                'uploadDest' => '@runtime/upload-tmp/report',
            ],
            'au.fullthumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/report',
                'useThumbs' => false,
            ],
            'au.delete' => [
                'class' => 'app\components\jqueryupload\DeleteAction',
                'uploadDest' => '@runtime/upload-tmp/report',
            ],
        ];
    }

    /**
     * Helper function for rendering a list of Reports.
     *
     * @param string $view The view to use for rendering
     * @param array $fields Extra fields for the search form
     * @param array $extraParams Extra parameters for the search form
     * @return string|Response
     */
    private function renderReportList($view = 'index', $fields = [], $extraParams = [])
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(ArrayHelper::merge($extraParams, Yii::$app->request->queryParams), $fields);

        $viewParams = ArrayHelper::merge([
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ], $fields);

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial($view, $viewParams);
        }

        return $this->render($view, $viewParams);
    }

    /**
     * Displays a list of Reports.
     *
     * @param string|null $q Search parameter
     * @return string|Response
     */
    public function actionIndex($q = null)
    {
        return $this->renderReportList('index', [], ['ReportSearch' => ['text' => $q]]);
    }

    public function actionUpdateAttachment()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack(['report']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();
        if (!isset($post['id']) || !isset($post['img'])) {
            throw new BadRequestHttpException('Missing id or image');
        }

        /* @var \app\models\db\ReportAttachment $model */
        $model = ReportAttachment::findOne($post['id']);
        if (empty($model)) {
            throw new BadRequestHttpException('Invalid report attachment id');
        }

        return $model->updatePicturesAfterEdit($post['img']);
    }

    /**
     * Renders the edit image modal window
     *
     * @param null $id [optional]
     *
     * @return array|Response
     */
    public function actionEditImage($id = null)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = ReportAttachment::findOne($id);
            $model->updated_at = time();
            $model->update();

            return [
                'success' => true,
                'html' => $this->renderAjax('_image_editor_form', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['report', ['id' => $id]]);
    }

    /**
     * Renders the delete confirm modal window.
     *
     * @param int $id [optional]
     *
     * @return array|Response
     */
    public function actionDeleteImage($id = null)
    {
        $model = ReportAttachment::findOne($id);

        if (Yii::$app->request->isPost) {
            if ($original = ReportAttachmentOriginal::findOne(['report_attachment_id' => $id])) {
                $original->delete();
            }

            if ($model) {
                $model->delete();
            }

            return $this->redirect(['report/update', 'id' => $model->report_id]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('_image-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }
    }


    /**
     * Renders the delete confirm modal window.
     *
     * @param int $id [optional]
     *
     * @return array|Response
     */
    public function actionDeleteVideo($id = null)
    {
        $model = ReportAttachment::findOne($id);

        if (Yii::$app->request->isPost) {
            if ($model) {
                $model->status = 0;
                $model->save();
            }

            return $this->redirect(['report/update', 'id' => $id]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('_video-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }
    }

    public function actionView($id = null)
    {
        $model = $id === null ?
            null :
            Report::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where([
                    'report.id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->with(['reportActivities', 'reportAttachments'])
                ->one();

        if ($model === null) {
            return $this->redirect(['reports/index']);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Handles the update of a Report.
     *
     * @param integer $id The Report's id
     * @return string|Response
     */
    public function actionUpdate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        /* @var \app\models\db\Report $model */
        $model = $id === null ?
            null :
            Report::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where([
                    'report.id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($model === null) {
            return $this->redirect(['reports/index']);
        }

        if ($model->status == Report::STATUS_DELETED) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        if (Yii::$app->request->isGet && $model->status == Report::STATUS_NEW) {
            $model->status = Report::STATUS_EDITING;
            if ($model->save()) {
                $model->addActivity(ReportActivity::TYPE_EDITING, ['admin_id' => true]);
            } else {
                Yii::error('Unable to save Report! Skipping editing state...');
            }
        }

        // guess the best matching institution if not set any
        if (empty($model->institution_id)) {
            $model->institution_id = Rule::getBestMatchingInstituteId($model);
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            $pictures = ArrayHelper::getValue(Yii::$app->request->post(), 'Report.pictures', []);

            foreach ($pictures as $picture) {
                $model->addAttachment(ReportAttachment::TYPE_PICTURE, [
                    'name' => $picture,
                ]);
            }

            $layers = Yii::$app->request->post('selected_map_layers', []);
            $reportLayers = $model->getMapLayers();
            $delete = [];

            if ($layers) {
                // Removing deleted items
                foreach ($reportLayers as $layer) {
                    if (!in_array($layer, $layers)) {
                        $delete[] = $layer;
                    }
                }

                foreach ($layers as $layer) {
                    $reportMapLayer = new ReportMapLayer();
                    $reportMapLayer->report_id = $model->id;
                    $reportMapLayer->map_layer_id = $layer;
                    $reportMapLayer->save();
                }
            } else {
                // Mass delete
                $delete = $reportLayers;
            }

            if ($delete) {
                ReportMapLayer::deleteAll(['report_id' => $model->id, 'map_layer_id' => $delete]);
            }

            return $this->redirect(['reports/view', 'id' => $id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Lists Reports by an Institution.
     *
     * @param integer $id The Institution's id
     * @return string|Response
     */
    public function actionInstitution($id = null)
    {
        $institution = $id === null ? null : Institution::find()->where(['id' => $id])->with('city')->one();
        if ($institution === null) {
            return $this->redirect(['institution/index']);
        }

        return $this->renderReportList('institution', [
            'institution' => $institution,
        ]);
    }

    /**
     * Exports Reports by an Institution.
     *
     * @param integer $id The Institution's id
     * @param string $type The export type
     */
    public function actionInstitutionExport($id = null, $type = Report::SOURCE_EXCEL)
    {
        // TODO
    }

    /**
     * Lists Reports by a User.
     *
     * @param integer $id The User's id
     * @return string|Response
     */
    public function actionUser($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_VIEW)) {
            return $this->redirect(['reports/index']);
        }

        $user = $id === null ? null : User::findOne(['id' => $id]);
        if ($user === null) {
            return $this->redirect(['user/index']);
        }

        return $this->renderReportList('user', [
            'user' => $user,
        ]);
    }

    /**
     * Exports Reports by a User.
     *
     * @param integer $id The User's id
     * @param string $type The export type
     */
    public function actionUserExport($id = null, $type = Report::SOURCE_EXCEL)
    {
        // TODO
    }

    /**
     * Handles the update of a comment on the Report.
     *
     * @param integer $id The comment's ReportActivity's id
     * @return array|Response
     */
    public function actionEditComment($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)) {
            return $this->redirect(['reports/view', 'id' => $id]);
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
                    'report_activity.type' => [
                        ReportActivity::TYPE_CLOSE,
                        ReportActivity::TYPE_RESOLVE,
                        ReportActivity::TYPE_COMMENT,
                        ReportActivity::TYPE_RESPONSE,
                        ReportActivity::TYPE_ANSWER,
                    ],
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

        if ($activity->type == ReportActivity::TYPE_ANSWER) {
            $modelClass = 'app\modules\admin\models\AnswerForm';

            $data['institution'] = $activity->institution;
        } else {
            $modelClass = 'app\modules\admin\models\CommentForm';
        }

        $originalAttachments = [];

        /* @var \app\modules\admin\models\CommentForm|\app\modules\admin\models\AnswerForm $model */
        $model = new $modelClass($data);

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


            return $this->redirect(['reports/view', 'id' => $activity->report_id]);
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
     * Uploads an answer for the Report.
     *
     * @param integer $id The Report's id
     * @return array|Response
     */
    public function actionAnswer($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        /* @var \app\models\db\Report $report */
        $report = $id === null ? null : Report::find()
            ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
            ->where([
                'report.id' => $id,
                'admin_city.admin_id' => Yii::$app->user->id,
            ])
            ->one();

        if ($report === null) {
            return $this->redirect(['reports/index']);
        }

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['reports/view', 'id' => $report->id]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new AnswerForm([
            'report' => $report,
            'institutionId' => $report->institution_id,
        ]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $activity = $report->constructActivity(ReportActivity::TYPE_ANSWER, [
                'admin_id'       => true,
                'comment'        => $model->comment,
                'original_value' => $model->comment,
                'institution_id' => $model->institutionId,
                'is_active_task' => 0,
            ]);

            if (!empty($activity->admin_id) && $report->admin_id != $activity->admin_id) {
                $report->admin_id = $activity->admin_id;
                $report->save();
            }

            if (($success = $activity->save())) {
                if (!empty($model->attachments) && is_array($model->attachments)) {
                    foreach ($model->attachments as $name) {
                        $report->addAttachment(ReportAttachment::getTypeGuess('@runtime/upload-tmp/report/' . $name), [
                            'report_activity_id' => $activity->id,
                            'name' => $name,
                        ]);
                    }
                }
            }

            return [
                'success' => $success,
            ];
        }

        return [
            'success' => true,
            'html' => $this->renderAjax('_answer', [
                'model' => $model,
            ]),
        ];
    }

    /**
     * Toggles the comment for the specified ReportActivity.
     *
     * @param int $id The ReportActivity's id
     * @return array|Response
     */
    public function actionToggleComment($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        /* @var \app\models\db\ReportActivity $model */
        $model = $id === null ?
            null :
            ReportActivity::find()
                ->leftJoin(Report::tableName(), '`report_activity`.`report_id` = `report`.`id`')
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where([
                    'report_activity.id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($model === null) {
            return $this->redirect(['reports/index']);
        }

        $model->visible = $model->visible == 0 ? 1 : 0;
        $model->is_active_task = false;

        $success = $model->save(true, ['is_active_task', 'visible', 'updated_at']);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => $success,
                'label' => Yii::t('report', $model->visible == 1 ? 'report.comment.hide' : 'report.comment.show'),
            ];
        }

        return $this->redirect(['reports/view', 'id' => $model->report_id]);
    }

    /**
     * Handles the AJAX quick search of the Reports from the navigation bar.
     *
     * @return array|Response
     */
    public function actionSearch()
    {
        $searchModel = new ReportSearch();

        if (!Yii::$app->request->isAjax) {
            if ($searchModel->load(Yii::$app->request->queryParams) && $searchModel->validate() && !empty($searchModel->text)) {
                return $this->redirect(['reports/index', 'q' => $searchModel->text]);
            }

            return $this->redirect(['reports/index']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => true,
            'html' => $this->renderAjax('_quick_search', [
                'reports' => $searchModel->search(Yii::$app->request->queryParams, [], Yii::$app->params['quickSearchDisplayCount'])->getModels(),
            ]),
        ];
    }

    public function actionHighlight($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        /* @var \app\models\db\Report $report */
        $report = $id === null ? null : Report::find()
            ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
            ->where([
                'report.id' => $id,
                'admin_city.admin_id' => Yii::$app->user->id,
            ])
            ->one();

        if ($report === null) {
            return $this->redirect(['reports/index']);
        }

        $report->highlighted = (int)!$report->highlighted;
        $report->save();

        return $this->redirect(['reports/view', 'id' => $id]);
    }

    /**
     * Updates the status of a Report.
     *
     * @param integer $id The Report's id
     * @return array|Response
     */
    public function actionStatus($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        /* @var \app\models\db\Report $report */
        $report = $id === null ?
            null :
            Report::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where([
                    'report.id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($report === null) {
            return $this->redirect(['reports/index']);
        }

        $model = new StatusChange([
            'report' => $report,
            'status' => $report->status,
        ]);

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                return ActiveForm::validate($model);
            }

            if ($model->updateStatus()) {
                Yii::$app->session->addFlash('success', Yii::t('app', 'successful_report_status_change'));

                return $this->redirect(['reports/view', 'id' => $report->id]);
            }
        }

        return [
            'success' => true,
            'html' => $this->renderAjax('_status_change', [
                'model' => $model,
            ]),
        ];
    }

    /**
     * @param int $id
     * @param int $reportId
     * @return \app\components\Response|\yii\console\Response|Response
     */
    public function actionDeleteComment($id, $reportId)
    {
        if (Yii::$app->request->isPost && ($attachment = ReportAttachment::findOne($id))) {
            $attachment->delete();
        }

        return Yii::$app->response->redirect(Url::to(['report/view', 'id' => $reportId]));
    }

    /**
     * Soft deletes (sets the status to deleted) a Report.
     *
     * @param integer $id the Report's id
     * @return Response
     * @throws \Exception
     */
    public function actionDelete($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_DELETE)) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        /* @var \app\models\db\Report $model */
        $model = $id === null ?
            null :
            Report::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where([
                    'report.id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($model === null) {
            return $this->redirect(['reports/index']);
        }

        if (Yii::$app->request->isPost) {
            if ($model->softDelete()) {
                return $this->redirect(['index']);
            }

            return $this->redirect(['reports/view', 'id' => $model->id]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('_delete_confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['reports/view', 'id' => $model->id]);
    }

    /**
     * Sends a Report to the authority.
     *
     * @param integer $id the Report's id
     * @return array|Response
     */
    public function actionSend($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        /* @var \app\models\db\Report $report */
        $report = $id === null ?
            null :
            Report::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where([
                    'report.id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($report === null) {
            return $this->redirect(['reports/index']);
        }

        $model = new SendForm(['report' => $report, 'institution_id' => $report->institution_id]);
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->handleSend()) {
                $report->status = Report::STATUS_WAITING_FOR_ANSWER;
                $report->institution_id = $model->institution_id;
                $report->sent_email_count += 1;

                if ($report->save()) {
                    $report->addActivity(ReportActivity::TYPE_SEND_TO_AUTHORITY, [
                        'admin_id' => true,
                        'institution_id' => $model->institution_id,
                    ]);
                }
            }

            return $this->redirect(['reports/view', 'id' => $report->id]);
        }

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['reports/view', 'id' => $report->id]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => true,
            'html' => $this->renderAjax('_send', [
                'model' => $model,
            ]),
        ];
    }

    /**
     * Renders a new ExtraContact field for a SendForm.
     *
     * @return array|Response
     */
    public function actionSendField()
    {
        $test = Yii::$app->request->get('test', 0);

        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT) || !Yii::$app->request->isAjax) {
            return $this->redirect(['reports/index']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => true,
            'fieldHtml' => $this->renderAjax('_send_field', ['test' => $test]),
        ];
    }

    /**
     * Renders a compare modal, where you can compare the Report's current data, with the original data, when it was reported.
     *
     * @param integer $id the Report's id
     * @return array|Response
     */
    public function actionCompare($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)) {
            return $this->redirect(['reports/view', 'id' => $id]);
        }

        /* @var \app\models\db\Report $model */
        $model = $id === null ?
            null :
            Report::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where([
                    'report.id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->with(['institution', 'reportOriginal', 'reportAttachmentOriginals'])
                ->one();

        if ($model === null) {
            return 'Error! Unable to load the report.';
        }

        return $this->renderAjax('_compare', [
            'model' => $model,
        ]);
    }

    /**
     * Renders the statistics page.
     *
     * @param string $tab
     *
     * @return string
     */
    public function actionStatistics($tab = self::TAB_INSTITUTIONS)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_STATISTICS)) {
            return $this->goHome();
        }

        $reportSearchModel = new StatisticsSearch();
        $institutionSearchModel = new StatisticsInstitutionSearch();
        $districtSearchModel = new StatisticsDistrictSearch();

        $viewData = [
            'tab' => $tab,
            'reportSearchModel' => $reportSearchModel,
            'institutionSearchModel' => $institutionSearchModel,
            'districtSearchModel' => $districtSearchModel,
            'reportStatisticsDataProvider' => $reportSearchModel->search(Yii::$app->request->queryParams),
            'institutionDataProvider' => $institutionSearchModel->search(Yii::$app->request->queryParams),
            'districtDataProvider' => $districtSearchModel->search(Yii::$app->request->queryParams),
        ];

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('statistics', $viewData);
        }

        return $this->render('statistics', $viewData);
    }

    /**
     * @param string $q
     * @param int $page
     * @return Report[]
     */
    public function actionListReport($q = null, $page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$q) {
            throw new \RuntimeException('Missing query');
        }

        return Report::getAvailableReports(ltrim($q, '0'), $page);
    }
}
