<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "picfeedbackcheck".
 *
 * @property integer $id
 * @property integer $picfeedbackid
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
            [['picfeedbackid', 'oldstatus', 'status'], 'required'],
            [['picfeedbackid', 'oldstatus', 'status', 'checkperson', 'checktime'], 'integer'],
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
            'oldstatus' => 'Oldstatus',
            'status' => 'Status',
            'checkperson' => 'Checkperson',
            'checktime' => 'Checktime',
            'reason' => 'Reason',
        ];
    }

    public function behaviors()
	{
        return [
            'timestamp' => [
                'class'     => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['checktime'],
                ],
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }
}
