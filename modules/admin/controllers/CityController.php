<?php

namespace app\modules\admin\controllers;

use app\models\db\Admin;
use app\models\db\AdminCity;
use app\models\db\City;
use app\models\db\District;
use app\models\db\Report;
use app\models\db\Rule;
use app\models\db\RuleContact;
use app\models\db\Street;
use app\models\db\StreetGroup;
use app\modules\admin\models\CitySearch;
use app\modules\admin\models\StreetGroupSearch;
use app\modules\admin\models\StreetSearch;

use Yii;

use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Handles City related actions.
 *
 * @package app\modules\admin\controllers
 */
class CityController extends Controller
{
    const TAB_RULES = 'rules';
    const TAB_STREETS = 'streets';
    const TAB_STREETGROUPS = 'streetgroups';
    const TAB_DISTRICTS = 'districts';

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
     * Displays the list of Cities.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_VIEW)) {
            return $this->goHome();
        }

        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $count = array_reduce(
            Report::find()
                ->select(['COUNT(*) as cnt, city_id'])
                ->where(
                    [
                        'city_id' => array_reduce(
                            $dataProvider->getModels(),
                            function (array $carry, City $model) {
                                $carry[] = $model->id;
                                return $carry;
                            },
                            []
                        ),
                    ]
                )
                ->groupBy('city_id')
                ->asArray()
                ->all(),
            function (array $carry, array $input) {
                $carry[$input['city_id']] = $input['cnt'];
                return $carry;
            },
            []
        );

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'count' => $count,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Handles the creation of a new City.
     *
     * @return string|Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_ADD)) {
            return $this->redirect(['city/index']);
        }

        $model = new City();

        $model->setScenario(City::SCENARIO_CREATE);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->save()) {
                // assign newly created city to the current admin
                $adminCity = new AdminCity();
                $adminCity->admin_id = Yii::$app->user->identity->id;
                $adminCity->city_id = $model->id;
                $adminCity->save();

                return $this->redirect(['index']);
            }

            Yii::error('Unable to create City! Errors: ' . print_r($model->getErrors(), true));
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

        return $this->redirect(['city/index']);
    }

    /**
     * Handles the view of a City.
     *
     * @param int $id the City's id
     * @param string $tab the displayed tab
     * @return string|Response
     * @throws \Exception
     */
    public function actionView($id = null, $tab = self::TAB_RULES)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            Yii::$app->session->addFlash('danger', Yii::t('admin', 'you-dont-have-permission-to-this-page'));
            return $this->redirect(['city/index']);
        }

        /* @var null|\app\models\db\City $model */
        $model = null;
        if ($id !== null) {
            if (Yii::$app->user->identity->isSuperAdmin()) {
                $model = City::find()
                    ->where([
                        'city.id' => $id,
                    ])
                    ->joinWith('districts')
                    ->one();
            } else {
                $model = City::find()
                    ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `city`.`id`')
                    ->where([
                        'city.id' => $id,
                        'admin_city.admin_id' => Yii::$app->user->id,
                    ])
                    ->with('districts')
                    ->one();
            }
        }

        if ($model === null) {
            Yii::$app->session->addFlash('danger', Yii::t('admin', 'this-model-does-not-exists-or-you-dont-have-permission'));

            return $this->redirect(['city/index']);
        }

        $model->setScenario(City::SCENARIO_UPDATE);

        if ($model->has_districts) {
            $districtProvider = new ActiveDataProvider([
                'query' => District::find()->where(['city_id' => $model->id])->with('streets'),
                'sort' => [
                    'defaultOrder' => [
                        'name' => SORT_ASC,
                    ],
                    'attributes' => [
                        'name',
                    ],
                ],
            ]);
        } else {
            $districtProvider = null;
        }

        $streetProvider = new ActiveDataProvider([
            'query' => Street::find()->where(['city_id' => $model->id])->with('district'),
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
                'attributes' => [
                    'name',
                ],
            ],
        ]);

        $streetGroupProvider = new ActiveDataProvider([
            'query' => StreetGroup::find()->andWhere(['city_id' => $model->id]),
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
                'attributes' => [
                    'name',
                ],
            ],
        ]);

        $ruleProvider = new ActiveDataProvider([
            'query' => Rule::find()
                ->where(['rule.city_id' => $model->id])
                ->joinWith(['institution'])
                ->with(['streetGroup', 'district', 'ruleContacts'])
                ->orderBy(['institution.name' => SORT_ASC]),
            'sort' => [
                'attributes' => [
                    'id',
                ],
            ],
        ]);

        $data = [
            'model' => $model,
            'tab' => $tab,
            'districtProvider' => $districtProvider,
            'streetProvider' => $streetProvider,
            'streetGroupProvider' => $streetGroupProvider,
            'ruleProvider' => $ruleProvider,
        ];

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('update', $data);
        }

        return $this->render('view', $data);
    }

    /**
     * @param null $id [optional]
     *
     * @return string
     */
    public function actionUpdate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            Yii::$app->session->addFlash('danger', Yii::t('admin', 'you-dont-have-permission-to-this-page'));

            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $model */
        $model = null;

        if ($id !== null) {
            if (Yii::$app->user->identity->isSuperAdmin()) {
                $model = City::find()
                ->where([
                    'city.id' => $id,
                ])
                ->with('districts')
                ->one();
            } else {
                $model = City::find()
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `city`.`id`')
                ->where([
                    'city.id' => $id,
                    'admin_city.admin_id' => Yii::$app->user->id,
                ])
                ->with('districts')
                ->one();
            }
        }

        if ($model === null) {
            Yii::$app->session->addFlash('danger', Yii::t('admin', 'this-model-does-not-exists-or-you-dont-have-permission'));
            return $this->redirect(['city/index']);
        }

        $model->setScenario(City::SCENARIO_UPDATE);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->has_districts == 0 && count($model->districts) > 0) {
                $model->has_districts = 1;
            }

            if ($model->save()) {
                return $this->redirect(['index']);
            }

            Yii::error('Unable to update City! Errors: ' . print_r($model->getErrors(), true));
        }

        if (Yii::$app->request->isGet && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('update', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Handles creation or editing of a District.
     *
     * @param int $id the City's id
     * @param int $did the District's id
     * @return array|Response
     */
    public function actionDistrict($id = null, $did = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $city */
        $city = $id === null ? null : City::findOne(['id' => $id]);
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        if (!$city->has_districts) {
            return $this->redirect(['city/update', 'id' => $id, 'tab' => self::TAB_DISTRICTS]);
        }

        /* @var \app\models\db\District $model */
        $model = $did === null ? null : District::findOne(['id' => $did]);
        if ($model === null) {
            $model = new District();
            $model->city_id = $city->id;
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->city_id = $city->id;

            $success = $model->save();

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('_districts', [
                    'city' => $city,
                    'dataProvider' => !$city->has_districts ?
                        null :
                        new ActiveDataProvider([
                            'query' => District::find()->where(['city_id' => $city->id])->with('streets'),
                            'sort' => [
                                'attributes' => [
                                    'name',
                                ],
                            ],
                        ]),
                ]);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_DISTRICTS]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('_district', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_DISTRICTS]);
    }

    /**
     * Deletes a District from the City.
     *
     * @param int $id the City's id
     * @param int $did the District's id
     * @return array|Response
     * @throws \Exception
     */
    public function actionDeleteDistrict($id = null, $did = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $city */
        $city = $id === null ? null : City::findOne(['id' => $id]);
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\District $model */
        $model = $did === null ? null : District::findOne(['id' => $did]);
        if ($model === null) {
            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_DISTRICTS]);
        }

        if (Yii::$app->request->isPost) {
            $success = $model->delete();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_DISTRICTS]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('district-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_DISTRICTS]);
    }

    /**
     * Renders the Street list partially for the Street search pjax.
     *
     * @param integer $id the City's id
     * @return string|Response
     */
    public function actionStreets($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $city */
        $city = $id === null ? null : City::find()->where(['id' => $id])->with('districts')->one();
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        if (!Yii::$app->request->isPjax) {
            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_STREETS]);
        }

        $searchModel = new StreetSearch(['city' => $city]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderPartial('_streets', [
            'city' => $city,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Handles the creation or editing of a Street.
     *
     * @param int $id the City's id
     * @param int $sid the Street's id
     * @return array|Response
     */
    public function actionStreet($id = null, $sid = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $city */
        $city = $id === null ? null : City::find()->where(['id' => $id])->with('districts')->one();
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\Street $model */
        $model = $sid === null ? null : Street::findOne(['id' => $sid]);
        if ($model === null) {
            $model = new Street();
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->city_id = $city->id;

            $success = $model->save();

            $searchModel = new StreetSearch(['city' => $city]);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('_streets', [
                    'city' => $city,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                ]);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_STREETS]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('_street', [
                    'city' => $city,
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_STREETS]);
    }

    /**
     * Deletes a Street from the City.
     *
     * @param int $id the City's id
     * @param int $sid the Street's id
     * @return array|Response
     * @throws \Exception
     */
    public function actionDeleteStreet($id = null, $sid = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $city */
        $city = $id === null ? null : City::findOne(['id' => $id]);
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\Street $model */
        $model = $sid === null ? null : Street::findOne(['id' => $sid]);
        if ($model === null) {
            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_STREETS]);
        }

        if (Yii::$app->request->isPost) {
            $success = $model->delete();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_STREETS]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('street-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_STREETS]);
    }

    /**
     * Handles the creation or editing of a Rule.
     *
     * @param int $id the City's id
     * @param int $rid the Rule's id
     * @return array|Response
     */
    public function actionRule($id = null, $rid = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $city */
        $city = $id === null ? null : City::find()->where(['id' => $id])->with(['districts', 'institutions'])->one();
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        $selectedContacts = [];

        /* @var \app\models\db\Rule $model */
        $model = $rid === null ? null : Rule::find()->where(['id' => $rid])->with(['ruleContacts', 'institution', 'institution.contacts'])->one();
        if ($model === null) {
            $model = new Rule();
            $model->city_id = $city->id;
            $model->status = Rule::STATUS_ACTIVE;
        } else {
            foreach ($model->ruleContacts as $ruleContact) {
                $selectedContacts[] = $ruleContact->contact_id;
            }
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->city_id = $city->id;

            $needDelete = !$model->isNewRecord;

            $success = $model->save();
            if ($success) {
                if ($needDelete) {
                    RuleContact::deleteAll(['rule_id' => $model->id]);
                }

                $contactIds = Yii::$app->request->post('RuleContact');
                if (is_array($contactIds)) {
                    foreach ($contactIds as $contactId => $value) {
                        if (!$value) {
                            continue;
                        }

                        $ruleContact = new RuleContact();
                        $ruleContact->contact_id = $contactId;
                        $ruleContact->rule_id = $model->id;
                        $ruleContact->save();
                    }
                }
            }

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('_rules', [
                    'city' => $city,
                    'dataProvider' => new ActiveDataProvider([
                        'query' => Rule::find()->where(['city_id' => $city->id])->with('institution'),
                        'sort' => [
                            'attributes' => [
                                'institution.name',
                            ],
                        ],
                    ]),
                ]);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_RULES]);
        }

        if (Yii::$app->request->isGet && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('_rule', [
                    'city' => $city,
                    'model' => $model,
                    'selectedContacts' => $selectedContacts,
                ]),
            ];
        }

        return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_RULES]);
    }

    /**
     * Deletes a Rule from the City.
     *
     * @param int $id the City's id
     * @param int $rid the Rule's id
     * @return array|Response
     * @throws \Exception
     */
    public function actionDeleteRule($id = null, $rid = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $city */
        $city = $id === null ? null : City::findOne(['id' => $id]);
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\Rule $model */
        $model = $rid === null ? null : Rule::findOne(['id' => $rid]);
        if ($model === null) {
            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_RULES]);
        }

        if (Yii::$app->request->isPost) {
            $success = $model->delete();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_RULES]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('rule-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_RULES]);
    }

    /**
     * Renders the Street list partially for the Street search pjax.
     *
     * @param int $id the City's id
     * @param int $cityId
     * @return string|Response
     */
    public function actionStreetgroups($id = null, $cityId = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        $city = $cityId === null ? null : City::find()
            ->where(['id' => $cityId])
            ->with('districts')
            ->one();

        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        if (!Yii::$app->request->isPjax) {
            return $this->redirect(['city/update', 'id' => $cityId, 'tab' => self::TAB_STREETS]);
        }

        $searchModel = new StreetGroupSearch([]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderPartial('_streetgroups', [
            'city' => $city,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Handles the creation or editing of a Street.
     *
     * @param int $id [optional] the StreetGroup's id
     * @param int $cityId [optional] the City's id
     * @return array|Response
     */
    public function actionStreetgroup($id = null, $cityId = null)
    {
        $model = $id === null ? null : StreetGroup::findOne(['id' => $id]);
        if ($model === null) {
            $model = new StreetGroup();
        } else {
            $model->connectedStreets = $model->getStreets()->select('id')->column();
        }

        $city = $cityId === null ? null : City::findOne(['id' => $cityId]);
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $success = $model->save();

            $model->unlinkAll('streets', true);

            /* saving StreetGroup Street connection */
            if (is_array($model->connectedStreets) && count($model->connectedStreets) > 0) {
                foreach ($model->connectedStreets as $street) {
                    $streetModel = Street::findOne(['id' => $street]);
                    //$model->unlink('streets', $streetModel, true);
                    $model->link('streets', $streetModel);
                }
            }

            $searchModel = new StreetGroupSearch([]);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('_streetgroups', [
                    'city' => $city,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                ]);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['city/update', 'id' => $cityId, 'tab' => self::TAB_STREETS]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('_streetgroup', [
                    'model' => $model,
                    'city' => $city,
                ]),
            ];
        }

        return $this->redirect(['city/update', 'id' => $cityId, 'city' => $city, 'tab' => self::TAB_STREETGROUPS]);
    }

    /**
     * Deletes a Street from the City.
     *
     * @param int $id [optional] the StreetGroup's id
     * @param int $cityId [optional] the City's id
     * @return array|Response
     * @throws \Exception
     */
    public function actionDeleteStreetgroup($id = null, $cityId = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\City $city */
        $city = $cityId === null ? null : City::findOne(['id' => $cityId]);
        if ($city === null) {
            return $this->redirect(['city/index']);
        }

        /* @var \app\models\db\Street $model */
        $model = $id === null ? null : StreetGroup::findOne(['id' => $id]);
        if ($model === null) {
            return $this->redirect(['city/update', 'id' => $cityId, 'tab' => self::TAB_STREETGROUPS]);
        }

        if (Yii::$app->request->isPost) {
            $success = $model->delete();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_STREETGROUPS]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('streetgroup-delete-confirm', [
                    'model' => $model,
                    'city' => $city,
                ]),
            ];
        }

        return $this->redirect(['city/update', 'id' => $city->id, 'tab' => self::TAB_STREETGROUPS]);
    }
}
