<?php

namespace app\controllers;

use Yii;
use app\assets\PdfAsset;
use app\components\ActiveForm;
use app\components\UserComponent;
use app\controllers\dropzone\RemoveAction;
use app\controllers\dropzone\UploadAction;
use app\models\db\PrPage;
use kartik\mpdf\Pdf;
use app\components\helpers\Link;
use app\models\db\ReportActivityRatings;
use app\components\Header;
use app\components\Preload;
use app\models\db\City;
use app\models\db\District;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\ReportAttachment;
use app\models\db\search\ReportSearch;
use app\models\forms\CommentForm;
use app\models\forms\ReportForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

/**
 * Handles Report related actions, like listing, creating a new Report and commenting on them.
 *
 * @package app\controllers
 */
class ReportController extends Controller
{
    const SLUG_BUDAPEST_SECOND_DISTRICT = 'budapest-ii-kerulet';

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Header::setAll([]);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['comment'],
                'rules' => [
                    [
                        'actions' => ['comment'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'parse-video-url' => ['post'],
                    'delete-attachment' => ['post'],
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
            'dropzone.report' => [
                'class' => UploadAction::className(),
                'uploadDest' => '@runtime/upload-tmp/report/',
                'uploadThumbDest' => '@runtime/upload-tmp/report/thumb/',
            ],
            'dropzone.comment' => [
                'class' => UploadAction::className(),
                'uploadDest' => '@runtime/upload-tmp/report/',
                'uploadThumbDest' => '@runtime/upload-tmp/report/thumb/',
            ],
            'dropzone.remove' => [
                'class' => RemoveAction::className(),
                'uploadDest' => '@runtime/upload-tmp/report/',
                'uploadThumbDest' => '@runtime/upload-tmp/report/thumb/',
            ],
        ];
    }

    /**
     * Renders the Report dashboard.
     *
     * @return string
     */
    public function actionIndex()
    {
        $conditions = Yii::$app->request->get('ReportSearch', []);
        $conditions['city_id'] = ArrayHelper::getValue($conditions, 'city_id', Yii::$app->params['defaultCityId']);

        $highlightedReports = Report::getHighlighted(4, $conditions);
        $latestReports = Report::getLatest(4, $conditions);
        $myLastReports = Report::getMyLastReports(2);
        $searchModel = new ReportSearch($conditions);

        $citySlug = City::getSlugById($conditions['city_id']);

        return $this->render('index', [
            'highlighted' => $highlightedReports,
            'myLastReports' => $myLastReports,
            'latest' => $latestReports,
            'searchModel' => $searchModel,
            'citySlug' => $citySlug,
        ]);
    }

    /**
     * Renders a form for creating Reports.
     *
     * @param int $from_id [optional]
     * @param string $citySlug [optional]
     * @param int $confirmedAnonymous [optional]]
     * @param int $institutionId [optional]]
     *
     * @return string
     * @throws \Exception
     */
    public function actionCreate($from_id = null, $citySlug = null, $confirmedAnonymous = 0, $prPageId = null)
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('menu', 'new_report'));

        /** @var ReportForm $model */
        $model = null;
        if (!empty($from_id)) {
            $model = ReportForm::findOne([
                'id' => $from_id,
                'status' => Report::STATUS_DRAFT,
                'user_id' => ArrayHelper::getValue(Yii::$app->user, 'identity.id'),
            ]);

            if ($model === null) {
                Yii::$app->session->setFlash('danger', Yii::t('report', 'report-not-found'));
                return $this->redirect(Link::to(Link::CREATE_REPORT));
            }

            $model->setPicturesFromDraft();
        }

        /** @var PrPage $prPageModel */
        $prPageModel = null;
        if (!empty($prPageId)) {
            $prPageModel = PrPage::findOne($prPageId);
        }

        if (empty($model)) {
            if ($secondDistrict = self::SLUG_BUDAPEST_SECOND_DISTRICT == $citySlug) {
                $citySlug = 'budapest';
            }

            $cityId = City::getIdBySlug($citySlug);
            $userCity = ArrayHelper::getValue(Yii::$app->user, 'identity.city_id');
            $defaultValues = [
                'city_id' => $cityId !== false ? $cityId : ($userCity ?: Yii::$app->params['defaultCityId']),
            ];
            $model = new ReportForm($defaultValues);

            // Adding custom coordinates
            if ($secondDistrict) {
                // Budapest II. kerület Bimbói út
                $model->latitude = 47.518850;
                $model->longitude = 19.013090;
            }
        }

        $draftRequest = array_key_exists('draft', Yii::$app->request->post());
        if ($draftRequest) {
            $model->scenario = $model::SCENARIO_DRAFT;
        }

        $view = 'create';

        if ($citySlug && in_array($citySlug, $project = array_flip(Report::$projects))) {
            // Configuring specific view when we've got a project for it..
            if (isset($project[$citySlug])) {
                $model->project = $project[$citySlug];
                $view .= '_project';
            }
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->handleAnonymousSession($model->anonymous);
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setUserId();
            $model->status = ($draftRequest ? Report::STATUS_DRAFT : Report::STATUS_NEW);
            $model->setDistrict();

            if ($anonymous = $this->getStoredAnonymousSession()) {
                $model->anonymous = $anonymous;
            }

            if ($model->save()) {
                foreach ((array)$model->pictures as $picture) {
                    $model->addAttachment(ReportAttachment::TYPE_PICTURE, [
                        'name' => $picture,
                    ]);
                }

                if ($model->videos !== null && is_array($model->videos)) {
                    foreach ($model->videos as $video) {
                        if (empty($video)) {
                            continue;
                        }

                        $data = ReportAttachment::extractVideoData($video);
                        if ($data === null || empty($data)) {
                            continue;
                        }

                        $model->addAttachment(ReportAttachment::TYPE_VIDEO, [
                            'name' => $video,
                            'url' => $video,
                        ]);
                    }
                }

                return $this->redirect(Link::to([Link::CREATE_REPORT, Link::CREATE_REPORT_SUCCESS], ['scenario' => $model->scenario]));
            } else {
                Yii::trace('Unable to save Report! Errors: ' . print_r($model->errors, true));
            }
        }

        Preload::setUploaderWidgetLabels();

        return $this->render(
            $view,
            [
                'model' => $model,
                'prPageModel' => $prPageModel,
            ]
        );
    }

    /**
     * Renders the report saved successfully page based on the scenario
     *
     * @return string
     */
    public function actionSuccess()
    {
        $scenario = Yii::$app->request->get('scenario', Report::SCENARIO_DEFAULT);

        if (!in_array($scenario, [Report::SCENARIO_DEFAULT, Report::SCENARIO_DRAFT])) {
            $scenario = Report::SCENARIO_DEFAULT;
        }

        return $this->render('success', [
            'scenario' => $scenario,
        ]);
    }

    /**
     * Renders the Report list view.
     *
     * @param string $citySlug [optional]
     * @param string $districtSlug [optional]
     * @param int $status [optional]
     * @param string $term [optional]
     * @param int $category [optional]
     * @param int $institution [optional]
     * @param float $lat [optional]
     * @param float $long [optional]
     *
     * @return string
     */
    public function actionList($citySlug = null, $districtSlug = null, $status = null, $term = null, $category = null, $institution = null, $lat = null, $long = null)
    {
        list($lat, $long) = $this->handleNearbyReports($status, $lat, $long);
        $this->redirectFilteredUrl(Link::REPORTS);

        $slugParams = [
            'city_id' => City::getIdBySlug($citySlug) ?: Yii::$app->params['defaultCityId'],
            'district_id' => $districtSlug ? (District::find()->select('id')->where(['slug' => $districtSlug])->scalar() ?: null) : null,
            'status' => $status,
            'name' => $term,
            'report_category_id' => $category,
            'institution_id' => $institution,
        ];

        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(['ReportSearch' => $slugParams], $status, $lat, $long);

        $dataProvider->pagination->pageSize = 8;
        $dataProvider->pagination->pageSizeParam = 'limit';

        Header::registerTag(Header::TYPE_TITLE, Yii::t('menu', 'report'));
        Header::registerTag(Header::TYPE_CANONICAL, Link::to([
            Link::REPORTS,
            ArrayHelper::getValue($searchModel, 'city.slug'),
            ArrayHelper::getValue($searchModel, 'district.slug'),
        ]));

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $redirectRoute
     */
    private function redirectFilteredUrl($redirectRoute)
    {
        $isMapRoute = $redirectRoute == Link::MAP;
        $queryParams = Yii::$app->request->get('ReportSearch', false);

        if (Yii::$app->user->isGuest && $queryParams === false) {
            return;
        }

        if (Yii::$app->request->get('citySlug', '') !== '') {
            return;
        }

        if (!Yii::$app->user->isGuest && $queryParams === false && !Yii::$app->user->identity->isAddressFilled()) {
            return;
        }

        $defaultCityFilter = ArrayHelper::getValue(Yii::$app->user, 'identity.city_id');
        $defaultDistrictFilter = ArrayHelper::getValue(Yii::$app->user, 'identity.district_id');

        $cityID = ArrayHelper::getValue($queryParams, 'city_id', $defaultCityFilter);
        $districtID = ArrayHelper::getValue($queryParams, 'district_id', $defaultDistrictFilter);
        $status = ArrayHelper::getValue($queryParams, 'status');
        $name = ArrayHelper::getValue($queryParams, 'name');
        $category = ArrayHelper::getValue($queryParams, 'report_category_id');
        $institution = ArrayHelper::getValue($queryParams, 'institution_id');

        $location = ArrayHelper::getValue($queryParams, 'location');
        $dateFrom = ArrayHelper::getValue($queryParams, 'date_from');
        $dateTo = ArrayHelper::getValue($queryParams, 'date_to');
        $waitingForAnswer = ArrayHelper::getValue($queryParams, 'waiting_for_answer');
        $resolved = ArrayHelper::getValue($queryParams, 'resolved');
        $waitingForSolution = ArrayHelper::getValue($queryParams, 'waiting_for_solution');
        $waitingForResponse = ArrayHelper::getValue($queryParams, 'waiting_for_response');
        $unresolved = ArrayHelper::getValue($queryParams, 'unresolved');
        $followed = ArrayHelper::getValue($queryParams, 'followed');
        $highlighted = ArrayHelper::getValue($queryParams, 'highlighted');
        $usersReports = ArrayHelper::getValue($queryParams, 'users_reports');

        $params = [];
        if ($status !== '' && $status !== null) {
            if (!$isMapRoute) {
                switch ($status) {
                    case Report::CUSTOM_FILTER_HIGHLIGHTED:
                        $redirectRoute = Link::REPORTS_HIGHLIGHTED;
                        break;
                    case Report::CUSTOM_FILTER_FRESH:
                        $redirectRoute = Link::REPORTS_FRESH;
                        break;
                    case Report::CUSTOM_FILTER_NEARBY:
                        $redirectRoute = Link::REPORTS_NEARBY;
                        break;
                    default:
                        $params['status'] = $status;
                        break;
                }
            } else {
                $params['status'] = $status;
            }
        }

        if ($location !== '' && $cityID !== '' && $location !== null) {
            if ($location !== City::find()->select('name')->where(['id' => $cityID])->scalar() . ', ') {
                $params['location'] = $location;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($location) . '&key=' . Yii::$app->params['google']['api_key_server']);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $response = curl_exec($ch);
                $data = json_decode($response);
                if ($data->status == 'OK') {
                    $params['lat'] = $data->results[0]->geometry->location->lat;
                    $params['lng'] = $data->results[0]->geometry->location->lng;
                }
            }
        }

        if ($name !== '' && $name !== null) {
            $params['term'] = $name;
        }
        if ($category !== '' && $category !== null) {
            $params['category'] = $category;
        }
        if ($institution !== '' && $institution !== null) {
            $params['institution'] = $institution;
        }
        if ($followed !== '0' && $followed !== null) {
            $params['followed'] = $followed;
        }
        if ($waitingForAnswer !== '0' && $waitingForAnswer !== null) {
            $params['waitingForAnswer'] = $waitingForAnswer;
        }
        if ($resolved !== '0' && $resolved !== null) {
            $params['resolved'] = $resolved;
        }
        if ($waitingForSolution !== '0' && $waitingForSolution !== null) {
            $params['waitingForSolution'] = $waitingForSolution;
        }
        if ($waitingForResponse !== '0' && $waitingForResponse !== null) {
            $params['waitingForResponse'] = $waitingForResponse;
        }
        if ($unresolved !== '0' && $unresolved !== null) {
            $params['unresolved'] = $unresolved;
        }
        if ($highlighted !== '0' && $highlighted !== null) {
            $params['highlighted'] = $highlighted;
        }
        if ($usersReports !== '0' && $usersReports !== null) {
            $params['usersReports'] = $usersReports;
        }
        if ($dateFrom !== '' && $dateFrom !== null) {
            $params['dateFrom'] = $dateFrom;
        }
        if ($dateTo !== '' && $dateTo !== null) {
            $params['dateTo'] = $dateTo;
        }

        if (empty($cityID)) {
            $this->redirect(Link::to($redirectRoute, $params));
            Yii::$app->end();
        }


        $citySlug = City::find()->select('slug')->where(['id' => $cityID])->scalar();

        $cityHasThisDistrict = District::find()->where(['city_id' => $cityID, 'id' => $districtID])->exists();
        $districtSlug = null;
        if (!empty($districtID) && $cityHasThisDistrict) {
            $districtSlug = District::find()->select('slug')->where(['id' => $districtID])->scalar();
        }

        $this->redirect(Link::to([$redirectRoute, $citySlug, $isMapRoute ? null : $districtSlug], $params));
        Yii::$app->end();
    }

    /**
     * @param null $citySlug
     * @param null $districtSlug
     * @param null $status
     * @param null $term
     * @param null $category
     * @param null $institution
     * @param null $lat
     * @param null $lng
     * @param null $waitingForAnswer
     * @param null $resolved
     * @param null $followed
     * @param null $waitingForSolution
     * @param null $waitingForResponse
     * @param null $unresolved
     * @param null $highlighted
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $location
     * @param null $usersReports
     *
     * @return string
     */
    public function actionMap(
        $citySlug = null,
        $districtSlug = null,
        $status = null,
        $term = null,
        $category = null,
        $institution = null,
        $lat = null,
        $lng = null,
        $waitingForAnswer = null,
        $resolved = null,
        $followed = null,
        $waitingForSolution = null,
        $waitingForResponse = null,
        $unresolved = null,
        $highlighted = null,
        $dateFrom = null,
        $dateTo = null,
        $location = null,
        $usersReports = null
    ) {
        $this->redirectFilteredUrl(Link::MAP);

        $slugParams = [
            'city_id' => City::getIdBySlug($citySlug) ?: Yii::$app->params['defaultCityId'],
            'district_id' => $districtSlug ? (District::find()->select('id')->where(['slug' => $districtSlug])->scalar() ?: null) : null,
            'status' => $status,
            'name' => $term,
            'report_category_id' => $category,
            'institution_id' => $institution,
            'waiting_for_answer' => $waitingForAnswer,
            'resolved' => $resolved,
            'waiting_for_solution' => $waitingForSolution,
            'waiting_for_response' => $waitingForResponse,
            'unresolved' => $unresolved,
            'highlighted' => $highlighted,
            'followed' => $followed,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'location' => $location,
            'lat' => $lat,
            'lng' => $lng,
            'users_reports' => $usersReports,
        ];

        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(['ReportSearch' => $slugParams], $status, null, null);

        /* @var $dataProvider->pagination yii\data\Pagination */
        $dataProvider->pagination->pageSize = 30;
        $request = Yii::$app->getRequest();
        $dataProvider->pagination->params = $request->getQueryParams();
        $dataProvider->pagination->params['#'] = 'map-report-list';
        $dataProvider->pagination->pageSizeParam = 'limit';

        return $this->render('mapbox', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Renders the Report's details view.
     *
     * @param string $citySlug [optional]
     * @param integer $id the Report's id [optional]
     * @param mixed $source [optional]
     *
     * @return string|Response
     */
    public function actionView($citySlug = null, $id = null, $source = null)
    {
        $report = Report::findAvailableReport($id);

        if ($report === null) {
            Yii::$app->session->addFlash('danger', Yii::t('report', 'report-not-found'));
            return $this->redirect(Link::to([Link::REPORTS, $citySlug]));
        }

        $report->checkUrlIsCorrect($source);

        if ($source == Report::SOURCE_PDF) {
            return $this->generatePdf($report);
        }

        $report->setTags();

        $comment = new CommentForm(['report' => $report]);
        if (Yii::$app->request->isPost && !Yii::$app->user->isGuest && $comment->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($comment);
            }

            if ($comment->handleComment()) {
                Yii::$app->session->setFlash('success', Yii::t('report', 'successful-comment'));
                return $this->redirect($report->getUrl());
            }

            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('_activity_list', [
                    'model' => $report,
                ]);
            }
        }

        return $this->render('view', [
            'model' => $report,
            'source' => $source,
        ]);
    }

    private function generatePdf(Report $report)
    {
        $asset = PdfAsset::register(Yii::$app->view);

        $content = $this->renderPartial('/pdf/view', [
            'model' => $report,
            'comments' => $report->getVisibleComments(),
        ]);

        $fontCacheDir = Yii::getAlias('@runtime') . '/mpdf-fontcache/';
        if (!file_exists($fontCacheDir)) {
            @mkdir($fontCacheDir);
        }
        define('_MPDF_TTFONTDATAPATH', $fontCacheDir);

        // az mPDF elég buta, és így nagyon nehezen lehet külső appként hivatkozva egyedi fontot
        // beállítani. ezért az assets/pdf/fonts könyvtárba másoltuk azokat a fontokat, amiket
        // az mPDF keres, és habár a nevük maradt a default, de valójában az egyedi Branding betűtípust
        // tartalmazzák. nem szép hack, de több órányi szenvedés után sem sikerült szép
        // (vagy egyáltalán) működő más megoldást találni.
        define('_MPDF_TTFONTPATH', $asset->sourcePath . '/../src/fonts/');

        $pdf = new Pdf([
            'filename' => $report->getUniqueName() . '.pdf',
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => $asset->sourcePath . '/css/style.css',
            'marginTop' => 25,
            'marginBottom' => 30,
            'options' => [
                'title' => $report->name,
            ],
            'methods' => [
                'SetFooter' => [$this->renderPartial('/pdf/_footer')],
            ],
        ]);

        return $pdf->render();
    }

    /**
     * Sets the user to follow this report.
     *
     * @param null $id [optional]
     *
     * @return Response
     */
    public function actionFollow($id = null)
    {
        /** @var Report $report */
        $report = Report::findAvailableReport($id);
        $user = Yii::$app->user;

        if ($report === null || $user->isGuest) {
            return $this->redirect(Link::to(Link::REPORTS));
        }

        $report->toggleFollower($user->identity);

        return $this->render('_follow_box', [
            'model' => $report,
        ]);
    }

    /**
     * Saves a like/dislike state on an activity by the user.
     *
     * @param null $id [optional]
     * @param $type
     *
     * @return string
     */
    public function actionRating($id = null, $type = ReportActivityRatings::FORM_TYPE_SIDEBAR)
    {
        $state = Yii::$app->request->post('state', 1);
        $userId = Yii::$app->getUser()->isGuest ? null : Yii::$app->getUser()->id;

        if ($userId !== null) {
            $ratings = ReportActivityRatings::findOne(['activity_id' => $id, 'user_id' => $userId]);

            if ($ratings !== null) {
                $ratings->state = $state;
            } else {
                $ratings = new ReportActivityRatings();
                $ratings->user_id = $userId;
                $ratings->activity_id = $id;
                $ratings->state = $state;
            }

            $ratings->save();
        }

        $activity = ReportActivity::findOne(['id' => $id]);

        $activity->report->setTags();

        return $this->renderAjax($type === ReportActivityRatings::FORM_TYPE_SIDEBAR ? '_activity_block' : '_show-comment', [
            'model' => $activity,
            'report' => $activity->report,
        ]);
    }

    /**
     * Parses the video url and returns the video information to can embed it on the site.
     *
     * @return array
     */
    public function actionParseVideoUrl()
    {
        $url = Yii::$app->request->post('url');

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['report/index']);
        }

        if ($url === null) {
            return $this->redirect(['report/index']);
        }

        $videoData = ReportAttachment::extractVideoData($url);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => $videoData !== null,
            'videoData' => $videoData,
        ];
    }

    /**
     * Returns all districts based on the city id.
     *
     * @param null $cityId [optional]
     *
     * @return array
     */
    public function actionGetDistricts($cityId = null)
    {
        $districts = District::getAll($cityId);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'districts' => $districts,
        ];
    }

    /**
     * @param mixed $status
     * @param float $lat
     * @param float $long
     *
     * @return array
     */
    private function handleNearbyReports($status, $lat, $long)
    {
        if ($status !== Report::CUSTOM_FILTER_NEARBY) {
            return [$lat, $long];
        }

        /** @var UserComponent $user */
        $user = Yii::$app->user;

        if ($lat !== null && $long !== null) {
            $user->setPosition($lat, $long);

            return [$lat, $long];
        }

        if (empty($user->getPosition())) {
            $this->view->registerJs('position.location.init();');
        } else {
            $lat = ArrayHelper::getValue($user->getPosition(), 'lat');
            $long = ArrayHelper::getValue($user->getPosition(), 'long');
        }

        return [$lat, $long];
    }

    public function actionShowComment($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            return false;
        }

        /** @var ReportActivity $comment */
        $comment = ReportActivity::findOne($id);

        if ($comment === null) {
            return false;
        }

        return [
            'success' => true,
            'html' => $this->renderAjax('@app/views/report/_show-comment', [
                'model' => $comment,
                'title' => $comment->report->name,
            ]),
        ];
    }

    public function actionDeleteAttachment($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = ReportAttachment::findOne($id);

        if (
            $model !== null &&
            $model->report->status == Report::STATUS_DRAFT &&
            $model->report->user_id == Yii::$app->user->id
        ) {
            $model->delete();
        }

        return [
            'success' => true,
            'html' => $this->renderPartial('_report-attachments', [
                'attachments' => $model->report->reportAttachments,
            ]),
        ];
    }

    /**
     * Handling checkbox tick to access 'anonymous' property's value
     *
     * @param bool $anonymous
     */
    private function handleAnonymousSession($anonymous)
    {
        if ($anonymous) {
            Yii::$app->session->set('anonymous', true);
        } else {
            if ($this->getStoredAnonymousSession()) {
                Yii::$app->session->remove('anonymous');
            }
        }
    }

    /**
     * @return bool
     */
    private function getStoredAnonymousSession()
    {
        return Yii::$app->session->get('anonymous') ?: false;
    }
}
