<?php

namespace app\models;

use Yii;
use app\controllers\DealFeedbackController;
use app\controllers\TextCheckController;
use app\models\Feedbackcomment;

/**
 * This is the model class for table "dealfeedback".
 *
 * @property string $id
 * @property integer $userid
 * @property integer $dealid
 * @property string $orderid
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
        'couponid',
        'score',
    ];

    static $bizreply = [
        'userid',
        'dealid',
        'couponid',
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
            [['userid', 'dealid', 'couponid'], 'required'],
            [['userid', 'dealid', 'couponid', 'score', 'weight', 'bizacctid', 'addtime', 'apdaddtime', 'replytime', 'apdreplytime', 'modtime', 'attributes', 'status', 'commentid', 'replyid'], 'integer']
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

    public function needManCheck()
    {
        return true;
    }

    public static function add($arr)
    {
        foreach (self::$feedback as $key) {
            if (!isset($arr[$key])) {
                return ;
            }
        }
        if (!isset($arr['comment'])) {
            return ;
        }
        $obj = new self;
        foreach (self::$feedback as $index) {
            $obj->$index = $arr[$index];
        }
        $comment = new Feedbackcomment;
        $comment->comment = $arr['comment'];
        $comment->save();
        $obj->commentid = $comment->id;
        $weight = DealFeedbackController::computeFeedbackWeight($obj->userid, $obj->couponid, $obj->dealid, $comment->comment, $arr['has_pic']);
        $obj->weight = $weight[0];
        $obj->save();

        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_COMMENT);
        $controller->pushForCheck($obj->id, 0);
    }

    public static function getDealFeedbackById($id)
    {
        $dealfeedback = Dealfeedback::find()->where(['id' => $id])->one();
        return $dealfeedback;
    }

}
