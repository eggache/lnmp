<?php

namespace app\models;

use Yii;

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
    static $feedback = [
        'userid',
        'dealid',
        'orderid',
        'poiid',
        'score',
    ];

    static $bizreply = [
        'userid',
        'dealid',
        'orderid',
        'poiid',
        'score',
        'weight',
        'bizacctid',
    ];
    /**
     * @inheritdoc
     */
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
        var_dump($arr);exit;
        foreach (self::$feedback as $key) {
            if (isset($arr[$key])) {
                return ;
            }
        }
        if (isset($arr['comment'])) {
            return ;
        }
        $obj = new self;
        $key = self::$key;
        foreach (self::$feedback as $index) {
            $obj->$index = $arr[$index];
        }
        $comment = new Feedbackcomment;
        $comment->comment = $arr['comment'];
        $comment->save();
        FeedbackController::computeFeedbackWeight($obj->userid, $obj->orderid, $obj->dealid, $obj->poiid, $comment, $arr['has_pic']);
        $obj->save();
    }
}
