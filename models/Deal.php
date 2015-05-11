<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "deal".
 *
 * @property integer $id
 * @property string $dealtitle
 * @property integer $money
 * @property integer $totalfeedback
 * @property integer $totalsale
 */
class Deal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['money', 'totalfeedback', 'totalsale'], 'integer'],
            [['dealtitle'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dealtitle' => 'Dealtitle',
            'money' => 'Money',
            'totalfeedback' => 'Totalfeedback',
            'totalsale' => 'Totalsale',
        ];
    }
}
