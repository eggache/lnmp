<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "feedbackconfirm".
 *
 * @property integer $id
 * @property integer $feedbackcheckid
 * @property integer $confirmstatus
 * @property integer $errtype
 * @property integer $confirmperson
 * @property integer $checkperson
 * @property integer $checktime
 * @property integer $confirmtime
 * @property integer $type
 */
class Feedbackconfirm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbackconfirm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedbackcheckid', 'confirmstatus', 'errtype', 'confirmperson', 'checkperson', 'checktime', 'confirmtime', 'type'], 'integer'],
            [['checkperson', 'type'], 'required']
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
            'confirmstatus' => 'Confirmstatus',
            'errtype' => 'Errtype',
            'confirmperson' => 'Confirmperson',
            'checkperson' => 'Checkperson',
            'checktime' => 'Checktime',
            'confirmtime' => 'Confirmtime',
            'type' => 'Type',
        ];
    }
}
