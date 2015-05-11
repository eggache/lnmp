<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "picfeedbackconfirm".
 *
 * @property integer $id
 * @property integer $picfeedbackcheckid
 * @property integer $confirmstatus
 * @property integer $errtype
 * @property integer $confirmperson
 * @property integer $checkperson
 * @property integer $checktime
 * @property integer $confirmtime
 */
class Picfeedbackconfirm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'picfeedbackconfirm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['picfeedbackcheckid', 'confirmstatus', 'errtype', 'confirmperson', 'checkperson', 'checktime', 'confirmtime'], 'integer'],
            [['checkperson'], 'required']
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
            'confirmstatus' => 'Confirmstatus',
            'errtype' => 'Errtype',
            'confirmperson' => 'Confirmperson',
            'checkperson' => 'Checkperson',
            'checktime' => 'Checktime',
            'confirmtime' => 'Confirmtime',
        ];
    }
}
