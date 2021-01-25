<?php

namespace app\modules\admin\models;

use app\models\db\Report;

use DateTime;

use Yii;

use yii\base\Model;

/**
 * Status change form for the Reports.
 *
 * @package app\modules\admin\models
 */
class StatusChange extends Model
{
    /**
     * @var integer
     */
    public $status;

    /**
     * @var string
     */
    public $reason;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var string
     */
    public $solutionDate;

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
            [['status'], 'required'],
            [['status'], 'in', 'range' => array_keys($this->statuses())],

            [['comment', 'reason', 'solutionDate'], 'safe'],
            [['comment'], 'string', 'on' => ['status-5', 'status-6']],

            [['reason'], 'required', 'on' => ['status-6']],
            [['reason'], 'string', 'on' => ['status-6']],

            [['solutionDate'], 'required', 'on' => ['status-8']],
            [['solutionDate'], 'integer', 'on' => ['status-8']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'status' => Yii::t('report', 'status_change.status'),
            'reason' => Yii::t('report', 'status_change.reason'),
            'comment' => Yii::t('report', 'status_change.comment'),
            'solutionDate' => Yii::t('report', 'status_change.solutionDate'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        $this->setUpScenario();

        if ($this->solutionDate !== null) {
            $date = DateTime::createFromFormat('!Y-m-d', $this->solutionDate);
            if ($date !== false) {
                $this->solutionDate = $date->getTimestamp();
            }
        }

        return true;
    }

    /**
     * Returns the available statuses for the form.
     *
     * @return string[]
     */
    public function statuses()
    {
        $statuses = [
            Report::STATUS_WAITING_FOR_INFO => Yii::t('const', 'report.status.2'),
            Report::STATUS_WAITING_FOR_ANSWER => Yii::t('const', 'report.status.3'),
            Report::STATUS_WAITING_FOR_RESPONSE => Yii::t('const', 'report.status.4'),
            Report::STATUS_WAITING_FOR_SOLUTION => Yii::t('const', 'report.status.8'),
            Report::STATUS_RESOLVED => Yii::t('const', 'report.status.5'),
            Report::STATUS_UNRESOLVED => Yii::t('const', 'report.status.6'),
            Report::STATUS_DELETED => Yii::t('const', 'report.status.7'),
        ];

        return $statuses;
    }

    /**
     * Sets up the scenario, based on the selected status.
     */
    public function setUpScenario()
    {
        if (!in_array($this->status, [Report::STATUS_RESOLVED, Report::STATUS_UNRESOLVED, Report::STATUS_WAITING_FOR_SOLUTION])) {
            return;
        }

        $this->setScenario("status-{$this->status}");
    }

    /**
     * Updates the status or the report.
     *
     * @return bool
     */
    public function updateStatus()
    {
        if ($this->validate()) {
            $this->report->updateStatus($this->status, $this->comment, $this->reason, $this->solutionDate);
            return true;
        }

        return false;
    }
}
