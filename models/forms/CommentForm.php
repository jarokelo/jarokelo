<?php

namespace app\models\forms;

use app\models\db\ReportActivity;
use app\models\db\ReportAttachment;
use yii\base\Model;

class CommentForm extends Model
{
    /**
     * @var string
     */
    public $comment;

    /**
     * @var array
     */
    public $pictures;

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
            [['comment'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['comment'], 'trim'],
            [['comment'], 'default'],
            [['comment'], 'required', 'message' => 'Kérjük írd be a hozzászólásod.'],
            [['comment'], 'string'],
            [['pictures'], 'each', 'rule' => ['string']],
        ];
    }

    /**
     * Adds a comment to the Report.
     *
     * @return bool True, if adding the comment was successful
     */
    public function handleComment()
    {
        /** @var \app\models\db\Report $this->report */
        if ($this->report === null) {
            return false;
        }

        if (!$this->validate()) {
            return false;
        }

        $activity = $this->report->constructActivity(ReportActivity::TYPE_COMMENT, [
            'user_id'        => true,
            'comment'        => $this->comment,
            'original_value' => $this->comment,
        ]);

        if (!$activity->save()) {
            return false;
        }

        $attachment = null;

        foreach ((array)$this->pictures as $picture) {
            $this->report->addAttachment(ReportAttachment::TYPE_COMMENT_PICTURE, [
                'report_activity_id' => $activity->id,
                'name' => $picture,
            ]);
        }

        return true;
    }
}
