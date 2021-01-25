<?php

namespace app\components;

class ActiveForm extends \yii\widgets\ActiveForm
{
    public $fieldClass = 'app\components\ActiveField';

    /**
     * @var string the default CSS class for the error summary container.
     * @see errorSummary()
     */
    public $errorSummaryCssClass = 'error-summary';
    /**
     * @var string the CSS class that is added to a field container when the associated attribute is required.
     */
    public $requiredCssClass = 'required';
    /**
     * @var string the CSS class that is added to a field container when the associated attribute has validation error.
     */
    public $errorCssClass = 'has-error';
    /**
     * @var string the CSS class that is added to a field container when the associated attribute is successfully validated.
     */
    public $successCssClass = 'has-success';
    /**
     * @var string the CSS class that is added to a field container when the associated attribute is being validated.
     */
    public $validatingCssClass = 'validating';
}
