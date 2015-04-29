<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\controllers\PictureCheckController;

/**
 * This is the model class for table "picfeedback".
 *
 * @property integer $id
 * @property integer $userid
 * @property integer $dealid
 * @property integer $couponid
 * @property integer $addtime
 * @property integer $modtime
 * @property string $attributes
 * @property integer $status
 * @property string $imagename
 */
class Picfeedback extends \yii\db\ActiveRecord
{
    private $checkqueue = "image_mac_check";

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
            [['userid', 'dealid', 'couponid', 'imagename'], 'required'],
            [['userid', 'dealid', 'couponid', 'addtime', 'modtime', 'attributes', 'status'], 'integer'],
            [['imagename'], 'string', 'max' => 512]
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
            'couponid' => 'Couponid',
            'addtime' => 'Addtime',
            'modtime' => 'Modtime',
            'attributes' => 'Attributes',
            'status' => 'Status',
            'imagename' => 'Imagename',
        ];
    }

    public function behaviors()
	{
        return [
            'timestamp' => [
                'class'     => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['addtime', 'modtime'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modtime'],
                ],
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $redis = Yii::$app->get('redis');
        $name = explode('.', $this->imagename);
        $name = $name[0];
        $json = json_encode([
            'id'        => $this->id,
            'imagename' => $name,
        ]);
        $redis->zadd($this->checkqueue, $this->id, $json);
    }
}
