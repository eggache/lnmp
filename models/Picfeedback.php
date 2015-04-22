<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "picfeedback".
 *
 * @property string $id
 * @property integer $userid
 * @property integer $dealid
 * @property string $orderid
 * @property integer $category
 * @property string $url
 * @property string $imagepath
 * @property string $title
 * @property integer $addtime
 * @property integer $modtime
 * @property string $attributes
 * @property integer $status
 */
class Picfeedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'picfeedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'dealid', 'orderid', 'category'], 'required'],
            [['userid', 'dealid', 'orderid', 'category', 'addtime', 'modtime', 'attributes', 'status'], 'integer'],
            [['url', 'imagepath', 'title'], 'string', 'max' => 512]
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
            'orderid' => 'Orderid',
            'category' => 'Category',
            'url' => 'Url',
            'imagepath' => 'Imagepath',
            'title' => 'Title',
            'addtime' => 'Addtime',
            'modtime' => 'Modtime',
            'attributes' => 'Attributes',
            'status' => 'Status',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['addtime', 'modtime'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modtime'],
                ],
            ],
        ];
    }
}
