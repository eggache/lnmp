<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coupon".
 *
 * @property string $id
 * @property string $userid
 * @property string $dealid
 */
class Coupon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'dealid'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'dealid' => 'Dealid',
        ];
    }
}
