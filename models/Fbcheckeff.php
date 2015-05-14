<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fbcheckeff".
 *
 * @property integer $id
 * @property integer $cnt
 * @property integer $usetime
 * @property integer $hour
 * @property integer $type
 * @property integer $checkperson
 * @property integer $modtime
 */
class Fbcheckeff extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fbcheckeff';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cnt', 'usetime', 'hour', 'type', 'checkperson', 'modtime'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cnt' => 'Cnt',
            'usetime' => 'Usetime',
            'hour' => 'Hour',
            'type' => 'Type',
            'checkperson' => 'Checkperson',
            'modtime' => 'Modtime',
        ];
    }

    public function behaviors(){
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modtime'],
                ],
            ],
        ];
    }
}
