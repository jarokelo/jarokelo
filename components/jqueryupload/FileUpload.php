<?php

namespace app\components\jqueryupload;

use app\assets\jqueryupload\AudioPreviewAsset;
use app\assets\jqueryupload\FileuploadAsset;
use app\assets\jqueryupload\ImagePreviewAsset;
use app\assets\jqueryupload\VideoPreviewAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class FileUpload extends \yii\widgets\InputWidget
{
    /**
     * @var array|boolean size (width,height) of the preview, false to disable client side preview
     */
    public $imagePreview = false;

    /**
     * @var boolean whether to enable client-side preview for audio files.
     */
    public $audioPreview = false;

    /**
     * @var boolean whether to enable client-side preview for video files.
     */
    public $videoPreview = false;

    /**
     * @var array button options
     * The following special options are supported:
     * - tag: string, the tag name of the button. Defaults to 'span'.
     * - label: string, the button's label
     * - encode: boolean, whether to encode the label
     * - icon: string, the html for the button's icon
     */
    public $buttonOptions = [];

    protected function registerAssets()
    {
        $view = $this->getView();
        FileuploadAsset::register($view);

        if ($this->audioPreview) {
            AudioPreviewAsset::register($view);
        }
        if ($this->imagePreview) {
            ImagePreviewAsset::register($view);
        }
        if ($this->videoPreview) {
            VideoPreviewAsset::register($view);
        }
    }

    public function run()
    {
        $id = $this->options['id'];
        $containerId = $id . '-container';
        $fileId = $id . '-file';
        $hiddenId = $id . '-hidden';

        $attributeName = $this->attribute;
        $hiddenInput = Html::activeHiddenInput($this->model, $attributeName, ['id' => $hiddenId, 'value' => '']);
        $fileInput = Html::activeInput('file', $this->model, $attributeName, array_merge($this->options, ['id' => $fileId]));

        $buttonOptions = $this->buttonOptions;

        if (!isset($buttonOptions['class'])) {
            $buttonOptions['class'] = 'au-upload-button btn btn-primary';
        } else {
            $buttonOptions['class'] .= ' au-upload-button';
        }
        $buttonTag = ArrayHelper::remove($buttonOptions, 'tag', 'span');
        $label = ArrayHelper::remove($buttonOptions, 'label', 'Upload');
        $encode = ArrayHelper::remove($buttonOptions, 'encode', true);
        $icon = ArrayHelper::remove($buttonOptions, 'icon', '');
        if ($encode) {
            $label = Html::encode($label);
        }
        if ($icon) {
            $label = "$icon $label";
        }

        $view = $this->getView();

        $view->registerCss(
            ".au-upload-button {
                position: relative;
                overflow: hidden;
            }
            .au-upload-button input {
                position: absolute;
                top: 0;
                right: 0;
                margin: 0;
                opacity: 0;
                -ms-filter: 'alpha(opacity=0)';
                font-size: 200px;
                direction: ltr;
                cursor: pointer;
            }",
            [],
            'au-bootstrap-file-input'
        );

        $output = '';

        $output .= Html::beginTag('span', [
            'id' => $containerId,
        ]);

        $fakeInput = Html::tag('span', '', [
            'id' => $id,
        ]);

        $output .= Html::beginTag($buttonTag, $buttonOptions);
        $output .= Html::tag('span', $label, []);
        $output .= $hiddenInput . $fileInput;
        $output .= $fakeInput;
        $output .= Html::endTag($buttonTag);

        $output .= Html::tag('span', '', [
            'class' => 'filename',
        ]);
        $output .= Html::endTag('span');

        $this->registerAssets();

        $options = [
            'attributeId' => $id,
            'input' => "#$id",
            'fileInput' => "#$fileId",
            'hiddenInput' => "#$hiddenId",
        ];

        $options = Json::htmlEncode($options);
        $view->registerJs("jQuery('#$containerId').simplefileupload($options);");

        return $output;
    }
}
