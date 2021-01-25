<?php

namespace app\controllers;

use app\components\Header;
use app\components\helpers\Link;
use app\models\forms\ContactForm;
use Yii;
use yii\web\Controller;
use yii\helpers\Url;

/**
 * Handles Report related actions, like listing, creating a new Report and commenting on them.
 *
 * @package app\controllers
 */
class AboutController extends Controller
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Header::setAll([]);

            return true;
        }

        return false;
    }

    /**
     * @return \yii\web\Response
     */
    public function actionMiddleware()
    {
        return $this->redirect(
            Link::to(
                [
                    Link::ABOUT,
                    Link::POSTFIX_ABOUT_SUPPORT,
                ]
            )
        );
    }

    /**
     * Renders the About front page.
     *
     * @return string
     */
    public function actionIndex()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.about'));
        return $this->render('index');
    }

    /**
     * Renders the join us page.
     *
     * @return string
     */
    public function actionVolunteer()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.volunteer'));
        return $this->render('volunteer');
    }

    /**
     * Renders the join us page.
     *
     * @return string
     */
    public function actionHowItWorks()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.howitworks'));
        return $this->render('howitworks');
    }

    /**
     * Renders the join us page.
     *
     * @return string
     */
    public function actionTos()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.tos'));
        return $this->render('tos');
    }

    /**
     * Renders the join us page.
     *
     * @return string
     */
    public function actionTeam()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.team'));
        return $this->render('team');
    }

    /**
     * Renders the contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.contact'));

        $model = new ContactForm();
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->handleContact()) {
                Yii::$app->session->addFlash('success', Yii::t('about', 'contact.success_message'));

                return $this->redirect('/about/contact');
            }
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Renders the bureau page.
     *
     * @return string
     */
    public function actionBureau()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.bureau'));
        return $this->render('bureau');
    }

    /**
     * Renders the support page.
     *
     * @return string
     */
    public function actionSupport()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.support'));
        Header::registerTag(Header::TYPE_FB_IMAGE, Url::to(Header::SHARE_DONATION, true));
        return $this->render('support');
    }

    /**
     * Renders the annual reports page.
     *
     * @return string
     */
    public function actionAnnualReports()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.annual_reports'));
        return $this->render('annual_reports');
    }

    /**
     * Renders the partners page.
     *
     * @return string
     */
    public function actionPartners()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.partners'));
        return $this->render('partners');
    }
}
