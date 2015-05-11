<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\controllers\TextCheckController;

/**
 * This is the model class for table "feedbackcheck".
 *
 * @property integer $id
 * @property integer $dealfeedbackid
 * @property integer $oldstatus
 * @property integer $status
 * @property integer $checkperson
 * @property integer $checktime
 * @property string $reason
 * @property integer $type
 */
class Feedbackcheck extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbackcheck';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dealfeedbackid', 'oldstatus', 'status'], 'required'],
            [['dealfeedbackid', 'oldstatus', 'status', 'checkperson', 'checktime', 'type'], 'integer'],
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
            'dealfeedbackid' => 'Dealfeedbackid',
            'oldstatus' => 'Oldstatus',
            'status' => 'Status',
            'checkperson' => 'Checkperson',
            'checktime' => 'Checktime',
            'reason' => 'Reason',
            'type' => 'Type',
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
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_CHECK);
        $controller->pushForCheck($this->dealfeedbackid);
    }
}
