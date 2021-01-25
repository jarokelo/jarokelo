<?php

namespace app\components\widgets;

use app\assets\PjaxAsset;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class Pjax extends \yii\widgets\Pjax
{
    public $enabled = true;

    public $timeout = 10000;

    public $enablePushState = false;

    public $enableReplaceState = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if ($this->enabled && $this->requiresPjax()) {
            ob_start();
            ob_implicit_flush(false);
            $view = $this->getView();
            $view->beginPage();
            $view->head();
            $view->beginBody();
            if ($view->title !== null) {
                echo Html::tag('title', Html::encode($view->title));
            }
        } else {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'div');
            echo Html::beginTag($tag, array_merge([
                'data-pjax-container' => '',
                'data-pjax-push-state' => $this->enablePushState,
                'data-pjax-replace-state' => $this->enableReplaceState,
                'data-pjax-timeout' => $this->timeout,
                'data-pjax-scrollto' => $this->scrollTo,
            ], $options));
        }
    }
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->enabled || !$this->requiresPjax()) {
            echo Html::endTag(ArrayHelper::remove($this->options, 'tag', 'div'));
            if ($this->enabled) {
                $this->registerClientScript();
            }
            return;
        }
        $view = $this->getView();
        $view->endBody();

        $view->endPage(true);
        $content = ob_get_clean();
        // only need the content enclosed within this widget
        $response = Yii::$app->getResponse();
        $response->clearOutputBuffers();
        $response->setStatusCode(200);
        $response->format = Response::FORMAT_HTML;
        $response->content = $content;

        // This is needed in case a pjax request has been redirected.
        if (!isset($response->headers['X-PJAX-URL'])) {
            $response->headers->set('X-PJAX-URL', Yii::$app->request->url);
        }

        $response->send();
        Yii::$app->end();
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $id = $this->options['id'];
        $this->clientOptions['push'] = $this->enablePushState;
        $this->clientOptions['replace'] = $this->enableReplaceState;
        $this->clientOptions['timeout'] = $this->timeout;
        $this->clientOptions['scrollTo'] = $this->scrollTo;
        $options = Json::htmlEncode($this->clientOptions);
        $js = '';
        if ($this->linkSelector !== false) {
            $linkSelector = Json::htmlEncode($this->linkSelector !== null ? $this->linkSelector : '#' . $id . ' a');
            $js .= "jQuery(document).pjax($linkSelector, \"#$id\", $options);";
        }
        if ($this->formSelector !== false) {
            $formSelector = Json::htmlEncode($this->formSelector !== null ? $this->formSelector : '#' . $id . ' form[data-pjax]');
            $js .= "\njQuery(document).on('submit', $formSelector, function (event) {jQuery.pjax.submit(event, '#$id', $options);});";
        }
        $view = $this->getView();
        PjaxAsset::register($view);
        if ($js !== '') {
            $view->registerJs($js);
        }
    }
}
