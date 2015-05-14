<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fbcheckstat".
 *
 * @property integer $id
 * @property integer $hour
 * @property integer $type
 * @property integer $passcnt
 * @property integer $totalcnt
 * @property integer $modtime
 * @property integer $checkperson
 */
class Fbcheckstat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fbcheckstat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hour', 'type', 'passcnt', 'totalcnt', 'modtime', 'checkperson'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hour' => 'Hour',
            'type' => 'Type',
            'passcnt' => 'Passcnt',
            'totalcnt' => 'Totalcnt',
            'modtime' => 'Modtime',
            'checkperson' => 'Checkperson',
        ];
    }
}
