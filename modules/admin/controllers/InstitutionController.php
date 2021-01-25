<?php

namespace app\modules\admin\controllers;

use app\models\db\Admin;
use app\models\db\AdminCity;
use app\models\db\Contact;
use app\models\db\Institution;
use app\models\db\Rule;
use app\modules\admin\models\InstitutionSearch;
use yii\base\Exception;
use Yii;

use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\Response;

/**
 * Handles Institution related actions.
 *
 * @package app\modules\admin\controllers
 */
class InstitutionController extends Controller
{
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
     * Displays the list of Institutions.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_VIEW)) {
            return $this->goHome();
        }

        $searchModel = new InstitutionSearch();

        $viewData = [
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
            'searchModel' => $searchModel,
        ];

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('index', $viewData);
        }

        return $this->render('index', $viewData);
    }

    /**
     * Handles the creation of a new Institution.
     *
     * @return string|Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_ADD)) {
            return $this->redirect(['institution/index']);
        }

        $model = new Institution();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)) {
                    return $this->redirect(['institution/index']);
                }

                return $this->redirect(['index']);
            }

            Yii::error('Unable to create Institution! Errors: ' . print_r($model->getErrors(), true));
        }

        if (Yii::$app->request->isGet && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('create', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['institution/index']);
    }

    /**
     * Handles the editing of a Institution.
     *
     * @param integer $id The Institution's id
     * @return string|Response
     * @throws \Exception
     */
    public function actionUpdate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)) {
            return $this->redirect(['institution/index']);
        }

        /* @var \app\models\db\Institution $model */
        $model = $id === null ?
            null :
            Institution::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `institution`.`city_id`')
                ->where([
                    'id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($model === null) {
            return $this->redirect(['institution/index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }

            Yii::error('Unable to update Institution! Errors: ' . print_r($model->getErrors(), true));
        }

        $viewData = [
            'model' => $model,
            'contactProvider' => new ActiveDataProvider([
                'query' => Contact::find()->where(['institution_id' => $model->id]),
                'sort' => [
                    'defaultOrder' => [
                        'name' => SORT_ASC,
                    ],
                    'attributes' => [
                        'name',
                        'email',
                    ],
                ],
            ]),
        ];

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('update', $viewData);
        }

        return $this->render('update', $viewData);
    }

    /**
     * Handles the deletion of an Institution.
     *
     * @param integer $id the about to be deleted Institution's id
     * @return Response
     * @throws \Exception
     */
    public function actionDelete($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_DELETE)) {
            return $this->redirect(['institution/index']);
        }

        $transaction = Yii::$app->db->beginTransaction();

        /* @var \app\models\db\Institution $model */
        $model = $id === null ?
            null :
            Institution::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `institution`.`city_id`')
                ->where([
                    'id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($model === null) {
            return $this->redirect(['institution/index']);
        }

        try {
            if (Yii::$app->request->isPost) { // TODO: soft delete? where? when?...
                $success = $model->delete();

                $transaction->commit();

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return [
                        'success' => $success,
                    ];
                }

                return $this->redirect(['institution/index']);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            if ($e instanceof \yii\db\Exception) {
                Yii::$app->session->setFlash('error', Yii::t('error', Inflector::slug($e->getName())));
            } else {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('institution-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['institution/index']);
    }

    /**
     * Renders a modal for creating/editing a Contact.
     *
     * @param int $id the Institution's id
     * @param int $cid the Contact's id
     * @return array|string|Response
     */
    public function actionContact($id = null, $cid = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)) {
            return $this->redirect(['institution/index']);
        }

        /* @var \app\models\db\Institution $institution */
        $institution = $id === null ?
            null :
            Institution::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `institution`.`city_id`')
                ->where([
                    'id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($institution === null) {
            return $this->redirect(['institution/index']);
        }

        $model = $cid === null ? null : Contact::findOne(['id' => $cid]);
        if ($model === null) {
            $model = new Contact();
            $model->institution_id = $institution->id;
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->institution_id = $institution->id;

            $success = $model->save();

            if (Yii::$app->request->isPjax) {
                $contactProvider = new ActiveDataProvider([
                    'query' => Contact::find()->where(['institution_id' => $institution->id]),
                    'sort' => [
                        'attributes' => [
                            'name',
                            'email',
                        ],
                    ],
                ]);

                return $this->renderPartial('update', [
                    'model' => $institution,
                    'contactProvider' => $contactProvider,
                ]);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            if ($success) {
                return $this->redirect(['institution/update', 'id' => $institution->id]);
            }
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('_contact', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['institution/update', 'id' => $institution->id]);
    }

    /**
     * Handles the delete confirm modal and the actual deletion.
     *
     * @param int $id The Institution's id
     * @param int $cid The Contact's id
     * @return array|string|Response
     * @throws \Exception
     */
    public function actionDeleteContact($id = null, $cid = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)) {
            return $this->redirect(['institution/index']);
        }

        /* @var \app\models\db\Institution $institution */
        $institution = $id === null ?
            null :
            Institution::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `institution`.`city_id`')
                ->where([
                    'id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($institution === null) {
            return $this->redirect(['institution/index']);
        }

        /* @var \app\models\db\Contact $model */
        $model = $cid === null ? null : Contact::findOne(['id' => $cid]);
        if ($model === null) {
            return $this->redirect(['institution/update', 'id' => $institution->id]);
        }

        if (Yii::$app->request->isPost) {
            $success = $model->delete();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['institution/update', 'id' => $institution->id]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('contact-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['institution/update', 'id' => $institution->id]);
    }

    /**
     * Returns the note of the selected Institution.
     *
     * @param integer $id the Institution's id
     * @return array|Response
     */
    public function actionNote($id = null)
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['institution/index']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_VIEW)) {
            return [
                'success' => false,
            ];
        }

        /* @var \app\models\db\Institution $institution */
        $institution = $id === null ?
            null :
            Institution::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `institution`.`city_id`')
                ->where([
                    'id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->one();

        if ($institution === null) {
            return [
                'success' => false,
            ];
        }

        return [
            'success' => true,
            'note' => $institution->note,
        ];
    }

    /**
     * Returns the note, the contact list of the selected Institution and the RuleContact entries for the selected Rule.
     *
     * @param integer $id the Institution's id
     * @param integer $rid the Rule's id
     * @param boolean $radioList if true, a radio button list will be rendered, instead of a checkbox list
     * @return array|Response
     */
    public function actionContactList($id = null, $rid = null, $radioList = false)
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['institution/index']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var \app\models\db\Institution $institution */
        $institution = $id === null ?
            null :
            Institution::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `institution`.`city_id`')
                ->where([
                    'id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->with('contacts')
                ->one();

        if ($institution === null) {
            return [
                'success' => false,
            ];
        }

        $selectedContacts = [];
        $selectIfEmpty = false;

        if (!$radioList) {
            /* @var \app\models\db\Rule $rule */
            $rule = $rid === null ? null : Rule::find()->where(['id' => $rid, 'institution_id' => $id])->with('ruleContacts')->one();
            if ($rule !== null) {
                foreach ($rule->ruleContacts as $ruleContact) {
                    $selectedContacts[] = $ruleContact->contact_id;
                }
            } else {
                $selectIfEmpty = true;
            }
        }

        return [
            'success' => true,
            'html' => $this->renderAjax($radioList ? '_contact_radio_list' : '_contact_list', [
                'contacts' => $institution->contacts,
                'selectedContacts' => $selectedContacts,
                'selectIfEmpty' => $selectIfEmpty,
            ]),
            'note' => $institution->note,
        ];
    }
}
