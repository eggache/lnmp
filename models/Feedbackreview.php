<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "feedbackreview".
 *
 * @property integer $id
 * @property integer $feedbackcheckid
 * @property integer $status
 * @property integer $reviewperson
 * @property string $reason
 * @property integer $reviewtime
 * @property integer $attributes
 * @property integer $type
 */
class Feedbackreview extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbackreview';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedbackcheckid', 'status', 'reviewperson', 'type'], 'required'],
            [['feedbackcheckid', 'status', 'reviewperson', 'reviewtime', 'attributes', 'type'], 'integer'],
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
            'feedbackcheckid' => 'Feedbackcheckid',
            'status' => 'Status',
            'reviewperson' => 'Reviewperson',
            'reason' => 'Reason',
            'reviewtime' => 'Reviewtime',
            'attributes' => 'Attributes',
            'type' => 'Type',
        ];
    }

    public function behaviors()
	{
        return [
            'timestamp' => [
                'class'     => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['reviewtime'],
                ],
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }
}
