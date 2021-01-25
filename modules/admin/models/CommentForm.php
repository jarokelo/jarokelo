<?php

namespace app\modules\admin\models;

use Yii;

use yii\base\Model;

/**
 * Form for editing a Comment.
 *
 * @package app\modules\admin\models
 */
class CommentForm extends Model
{
    /**
     * @var string[]
     */
    public $attachments;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var \app\models\db\Report
     */
    public $report;

    /**
     * @var \app\models\db\ReportActivity
     */
    public $reportActivity;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment'], 'required'],
            [['comment'], 'string'],
            [['attachments'], 'each', 'rule' => ['string']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comment'     => Yii::t('report', 'answer.form.comment'),
            'attachments' => Yii::t('report', 'answer.form.attachments'),
        ];
    }
}
