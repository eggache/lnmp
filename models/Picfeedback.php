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
    const ATTR_MAC_CHECKED  = 0;
    const ATTR_MAN_CHECKED  = 1;
    const ATTR_CHECK_PASS   = 2;
    const ATTR_CHECK_BAN    = 3;

    public function __construct()
    {
        $this->on('afterInsert', [$this, 'afterInsert']);
    }

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

    //public function afterSave($insert, $changedAttributes)
    //{
    //    parent::afterSave($insert, $changedAttributes);
    //    $redis = Yii::$app->get('redis');
    //    $name = explode('.', $this->imagename);
    //    $name = $name[0];
    //    $json = json_encode([
    //        'id'        => $this->id,
    //        'imagename' => $name,
    //    ]);
    //    $redis->zadd($this->checkqueue, $this->id, $json);
    //}

    public function afterInsert()
    {
        $redis = Yii::$app->get('redis');
        $name = explode('.', $this->imagename);
        $name = $name[0];
        $json = json_encode([
            'id'        => $this->id,
            'imagename' => $name,
        ]);
        $redis->zadd($this->checkqueue, $this->id, $json);
    }

    public function setCheckStatus($checkperson, $status)
    {
        if ($checkperson) {
            $this->setAttr(self::ATTR_MAN_CHECKED, 1);
        } else {
            $this->setAttr(self::ATTR_MAC_CHECKED, 1);
        }

        if ($status == PictureCheckController::STATUS_PASS) {
            $this->setAttr(self::ATTR_CHECK_PASS, 1);
            $this->setAttr(self::ATTR_CHECK_BAN, 0);
        } else {
            $this->setAttr(self::ATTR_CHECK_PASS, 0);
            $this->setAttr(self::ATTR_CHECK_BAN, 1);
        }
        $this->update();
    }

    public function getAttr($attr)
    {
        $value = 1 << $attr;
        return $value & $this->attributes;
    }

    public function setAttr($attr)
    {
        $value = 1 << $attr;
        $this->attributes |= $value;
        $this->update();
    }
}
