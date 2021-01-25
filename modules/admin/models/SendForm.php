<?php

namespace app\modules\admin\models;

use Yii;
use app\components\EmailHelper;
use app\models\db\Institution;
use app\models\db\Rule;
use yii\base\Model;

/**
 * Send to authority form.
 *
 * @package app\modules\admin\models
 */
class SendForm extends Model
{
    /**
     * @var integer
     */
    public $institution_id;

    /**
     * @var \app\models\db\Institution
     */
    public $institution;

    /**
     * @var boolean
     */
    public $test = 0;

    /**
     * @var integer[]
     */
    public $selectedContacts;

    /**
     * @var \app\modules\admin\models\ExtraContact[]
     */
    public $extraContacts;

    /**
     * @var \app\models\db\Report
     */
    public $report;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institution_id', 'test'], 'required'],
            [['institution_id'], 'integer'],
            [['selectedContacts'], 'each', 'rule' => ['integer']],
            [['test'], 'boolean'],
            [['extraContacts'], 'validateExtraContacts'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'institution_id' => Yii::t('report', 'send.institution'),
            'contacts'       => Yii::t('report', 'send.contacts'),
        ];
    }

    /**
     * Validates the ExtraContacts array. On error, it is cleared.
     */
    public function validateExtraContacts()
    {
        if (empty($this->extraContacts) || !is_array($this->extraContacts)) {
            $this->extraContacts = [];
        } else {
            $newContacts = [];

            foreach ($this->extraContacts as $model) {
                if ($model->validate()) {
                    $newContacts[] = $model;
                }
            }

            $this->extraContacts = $newContacts;
        }
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        $this->test = $this->test == 1 ? 1 : 0;
        $this->selectedContacts = [];
        $this->extraContacts = [];

        if (isset($data['RuleContact'])) {
            foreach ($data['RuleContact'] as $id => $selected) {
                if ($selected != 1) {
                    continue;
                }

                $this->selectedContacts[] = $id;
            }
        }

        if (isset($data['ExtraContact']) && is_array($data['ExtraContact'])) {
            foreach ($data['ExtraContact'] as $contact) {
                $extraContact = new ExtraContact();
                if ($extraContact->load($contact, '')) {
                    $extraContact->test = $extraContact->test == 1 ? 1 : 0;

                    $this->extraContacts[] = $extraContact;
                }
            }
        }

        return true;
    }

    /**
     * Sends the report to the institution.
     *
     * @return boolean True, if the email was sent successfully
     */
    public function handleSend()
    {
        if (!$this->validate() || !$this->loadInstitution(false)) {
            Yii::error('Unable to validate SendForm! Errors: ' . print_r($this->getErrors(), true));
            return false;
        }

        $contacts = [];

        if (!$this->test) {
            foreach ($this->institution->contacts as $contact) {
                if (in_array($contact->id, $this->selectedContacts)) {
                    $contacts[] = [
                        'email' => $contact->email,
                        'name' => $contact->name,
                    ];
                }
            }
        }

        foreach ($this->extraContacts as $contact) {
            if ($contact->test === $this->test) {
                $contacts[] = [
                    'email' => $contact->email,
                    'name' => $contact->name,
                ];
            }
        }

        if (empty($contacts)) {
            return false;
        }

        EmailHelper::sendInstitutionSendReport($this->report, $this->institution, $contacts);

        return $this->test === 0;
    }

    /**
     * Loads the Institution.
     *
     * @param bool $loadSelected If true, loads the selected Contacts by the Report's Rule.
     * @return bool True, if the Institution exists.
     */
    public function loadInstitution($loadSelected = true)
    {
        if ($this->institution_id === null || empty($this->institution_id)) {
            return false;
        }

        $this->institution = Institution::find()->where(['id' => $this->institution_id])->with('contacts')->one();

        if ($loadSelected) {
            /* @var \app\models\db\Rule $rule */
            $rule = $this->report->rule_id === null ? null : Rule::find()->where(['id' => $this->report->rule_id, 'institution_id' => $this->institution_id])->with('ruleContacts')->one();
            if ($rule !== null) {
                foreach ($rule->ruleContacts as $ruleContact) {
                    $this->selectedContacts[] = $ruleContact->contact_id;
                }
            }
        }

        return $this->institution !== null;
    }
}
