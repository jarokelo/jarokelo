<?php

namespace app\controllers;

use app\models\db\District;
use app\models\db\Report;
use app\models\db\ReportAttachment;
use app\models\forms\RssForm;
use Yii;
use app\components\Header;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use GuzzleHttp\Client;

/**
 * Handles the Rss related actions, like config, and listing the rss feed.
 *
 * @package app\controllers
 */
class RssController extends Controller
{
    public function behaviors()
    {
        return [
            'httpCache' => [
                'class' => 'yii\filters\HttpCache',
                'enabled' => true,
                'cacheControlHeader' => 'public, max-age=' . ArrayHelper::getValue(Yii::$app->params, 'rss.cacheTime', 1800),
                'only' => ['stream'],
                'lastModified' => function ($action, $params) {
                    return Report::find()->select(['updated_at'])->orderBy(['id' => SORT_DESC])->limit(1)->scalar();
                },
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Header::setAll([]);

            return true;
        }

        return false;
    }

    /**
     * Renders the About front page.
     *
     * @return string
     */
    public function actionIndex()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.rss'));

        $query = [
            'c' => null, // city
            'd' => null, // district
            'r' => null, // institute
            't' => null, // category
            's' => null, // status
        ];
        $model = new RssForm();
        $districts = [];

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $query['c'] = $model->city;
            $query['d'] = $model->district;
            $query['r'] = $model->institution;
            $query['t'] = $model->category;
            $query['s'] = $model->status;

            if (strlen(trim($query['c'])) > 0) {
                $districts = District::getAll($query['c']);
            } else {
                $query['c'] = null;
            }

            if (count($districts) == 0) {
                $query['d'] = null;
            }

            if (strlen(trim($query['r'])) == 0) {
                $query['r'] = null;
            }

            if (strlen(trim($query['t'])) == 0) {
                $query['t'] = null;
            }

            if (strlen(trim($query['s'])) == 0) {
                $query['s'] = null;
            }
        }

        return $this->render('index', [
            'model' => $model,
            'query' => $query,
            'districts' => $districts,
        ]);
    }

    /**
     * Renders the join us page.
     *
     * @return string
     */
    public function actionStream()
    {
        $filters = Yii::$app->request->get();

        $feed = Yii::$app->feed->writer();
        $feed->setTitle(Yii::$app->name);
        $feed->setLink(Url::base(true));
        $feed->setFeedLink(Url::base(true) . '/rss', 'rss');
        $feed->setDescription(Yii::t('app', 'Recent headlines'));
        $feed->setGenerator(Url::base(true) . '/rss');
        $feed->setDateModified(time());

        /** @var \app\models\db\Report $report */

        $reports = Report::filterForRss($filters);

        foreach ($reports->all() as $report) {
            /** @var \Zend\Feed\Writer\Entry $entry */
            $entry = $feed->createEntry();

            $entry->setTitle($report->name);
            $entry->setLink($report->getUrl());
            $entry->setDateModified(intval($report->updated_at));
            $entry->setDateCreated(intval($report->created_at));
            $entry->setContent($report->description ? $report->description : '?');

            foreach ($report->reportAttachments as $attachment) {
                /** \app\models\db\ReportAttachment $attachment */
                $extension = pathinfo($attachment->name, PATHINFO_EXTENSION);
                $attachmentUrl = $attachment->getAttachmentUrl(
                    ReportAttachment::SIZE_PICTURE_ORIGINAL,
                    true
                );

                $fileSize = 1;

                if ($attachment->isStorageS3()) {
                    try {
                        $client = new Client();
                        $head = $client->head($attachmentUrl);

                        if ($head && ($length = $head->getHeader('Content-Length')) && isset($length[0])) {
                            list($fileSize) = $length;
                        }
                    } catch (\Exception $e) {
                        // Absorbing not found
                        if ($e->getCode() != 404) {
                            throw $e;
                        }
                    }
                } else {
                    $fileSize = filesize(Yii::getAlias('@webroot') . $attachmentUrl);
                }

                $uri = $attachment->isStorageS3()
                    ? $attachment->getAttachmentUrl()
                    : Url::base(true) . $attachment->getAttachmentUrl();

                switch ($extension) {
                    case 'jpg':
                    case 'jpeg':
                        $entry->setEnclosure([
                            'uri' => $uri,
                            'type' => 'image/jpeg',
                            'length' => $fileSize,
                        ]);
                        break;

                    case 'png':
                        $entry->setEnclosure([
                            'uri' => $uri,
                            'type' => 'image/png',
                            'length' => $fileSize,
                        ]);
                        break;
                }
            }

            $feed->addEntry($entry);
        }

        header('Content-Type: application/rss+xml; charset=utf-8');
        echo $feed->export('rss');
        exit;
    }
}
