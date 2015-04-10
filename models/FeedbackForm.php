<?php
namespace app\models;

use yii\base\Model;

class FeedbackForm extends Model
{
    public $orderid;
    public $userid;
    public $dealid;
    public $poiid;
    public $score;
    public $comment;
    public $has_pic;
    public $picids;

    public function rules()
    {
        return [
            [['orderid', 'userid', 'dealid', 'comment'], 'required'],
            [['orderid', 'userid', 'dealid'], 'integer'],
            ['comment', 'string', 'max' => 512, 'min' => 15],
        ];
    }
}
