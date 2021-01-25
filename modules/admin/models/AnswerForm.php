<?php

namespace app\modules\admin\models;

use app\models\db\Institution;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Form for uploading an answer manually.
 *
 * @package app\modules\admin\models
 */
class AnswerForm extends CommentForm
{
    /**
     * @var integer
     */
    public $contactId;

    /**
     * @var integer
     */
    public $institutionId;

    /**
     * @var \app\models\db\Institution
     */
    public $institution;

    /**
     * @var \app\models\db\Institution[]
     */
    private $_institutions;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['institutionId'], 'required'],
            [['contactId', 'institutionId'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'institutionId' => Yii::t('report', 'answer.form.institutionId'),
        ]);
    }

    /**
     * Returns the available Institutions.
     *
     * @return \app\models\db\Institution[]
     */
    public function getInstitutions()
    {
        if (is_array($this->_institutions)) {
            return $this->_institutions;
        }

        $this->_institutions = Institution::getInstitutionsQuery($this->report->city_id, Yii::$app->user->id)->with(['contacts'])->all();

        if (is_array($this->_institutions) && count($this->_institutions) > 0) {
            foreach ($this->_institutions as $institution) {
                if ($institution->id == $this->institutionId) {
                    $this->institution = $institution;
                }
            }

            if ($this->institution === null) {
                $this->institution = $this->_institutions[0];
            }
        }

        return $this->_institutions;
    }
}
