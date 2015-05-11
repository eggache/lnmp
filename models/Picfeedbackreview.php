<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "picfeedbackreview".
 *
 * @property integer $id
 * @property integer $picfeedbackcheckid
 * @property integer $status
 * @property integer $reviewperson
 * @property string $reason
 * @property integer $reviewtime
 * @property integer $attributes
 */
class Picfeedbackreview extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'picfeedbackreview';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['picfeedbackcheckid', 'status', 'reviewperson'], 'required'],
            [['picfeedbackcheckid', 'status', 'reviewperson', 'reviewtime', 'attributes'], 'integer'],
            [['reason'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'picfeedbackcheckid' => 'Picfeedbackcheckid',
            'status' => 'Status',
            'reviewperson' => 'Reviewperson',
            'reason' => 'Reason',
            'reviewtime' => 'Reviewtime',
            'attributes' => 'Attributes',
        ];
    }
}
