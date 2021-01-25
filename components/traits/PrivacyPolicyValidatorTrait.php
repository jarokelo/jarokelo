<?php

namespace app\components\traits;

use Yii;

/**
 *
 */
trait PrivacyPolicyValidatorTrait
{
    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    abstract public function addError($attribute, $error = '');

    /**
     * @param string $attribute
     * @param array $params
     */
    public function validatePrivacyPolicy($attribute, array $params = null)
    {
        if (!Yii::$app->user->getIsGuest()) {
            return;
        }

        if (!filter_var($this->{$attribute}, FILTER_VALIDATE_BOOLEAN)) {
            $this->addError($attribute, Yii::t('auth', 'error.privacy.policy'));
        }
    }
}
