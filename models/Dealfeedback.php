<?php

namespace app\models;

use Yii;
use app\models\Feedbackcomment;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "dealfeedback".
 *
 * @property string $id
 * @property integer $userid
 * @property integer $dealid
 * @property string $orderid
 * @property integer $poiid
 * @property integer $score
 * @property integer $weight
 * @property integer $bizacctid
 * @property integer $addtime
 * @property integer $apdaddtime
 * @property integer $replytime
 * @property integer $apdreplytime
 * @property integer $modtime
 * @property string $attributes
 * @property integer $status
 * @property string $commentid
 * @property string $replyid
 */
class Dealfeedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static $tableKey = [
        'userid'    => 'userid',
        'dealid'    => 'dealid',
        'orderid'   => 'orderid',
    ];
    public static function tableName()
    {
        return 'dealfeedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'dealid', 'orderid'], 'required'],
            [['userid', 'dealid', 'orderid', 'poiid', 'score', 'weight', 'bizacctid', 'addtime', 'apdaddtime', 'replytime', 'apdreplytime', 'modtime', 'attributes', 'status', 'commentid', 'replyid'], 'integer']
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
            'poiid' => 'Poiid',
            'score' => 'Score',
            'weight' => 'Weight',
            'bizacctid' => 'Bizacctid',
            'addtime' => 'Addtime',
            'apdaddtime' => 'Apdaddtime',
            'replytime' => 'Replytime',
            'apdreplytime' => 'Apdreplytime',
            'modtime' => 'Modtime',
            'attributes' => 'Attributes',
            'status' => 'Status',
            'commentid' => 'Commentid',
            'replyid' => 'Replyid',
        ];
    }

    public static function add($arr)
    {
        $comment = new Feedbackcomment;
        $comment->comment = $arr['comment'];
        $comment->insert();
        $obj = new self;
        foreach (self::$tableKey as $index) {
            $obj->$index = $arr[$index];
        }
        $obj->save();
        return $obj->id;
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
