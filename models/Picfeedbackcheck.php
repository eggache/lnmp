<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "picfeedbackcheck".
 *
 * @property integer $id
 * @property integer $picfeedbackid
 * @property integer $couponid
 * @property integer $oldstatus
 * @property integer $status
 * @property integer $checkperson
 * @property integer $checktime
 * @property string $reason
 */
class Picfeedbackcheck extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'picfeedbackcheck';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['picfeedbackid', 'couponid', 'oldstatus', 'status'], 'required'],
            [['picfeedbackid', 'couponid', 'oldstatus', 'status', 'checkperson', 'checktime'], 'integer'],
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
            'picfeedbackid' => 'Picfeedbackid',
            'couponid' => 'Couponid',
            'oldstatus' => 'Oldstatus',
            'status' => 'Status',
            'checkperson' => 'Checkperson',
            'checktime' => 'Checktime',
            'reason' => 'Reason',
        ];
    }
}
