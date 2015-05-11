<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\Deal;
use app\models\Coupon;
use app\models\Dealfeedback;
use app\models\Feedbackcomment;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ImportController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        $couponid = 0;
        $fp = fopen("/root/import.txt", "r");
        $cnt = 0;
        while($row = fgets($fp))
        {
            $cnt ++;
            if ($cnt == 1) continue;
            $str = preg_split('/\s+/', $row);
            $dealtitle = $str[0];
            $value = $str[1];
            list(, $row) = explode($dealtitle, $row);
            list(, $row) = explode($value, $row);
            $row = trim($row);
            $pattern = '/\s\d+\s/';
            list($comment) = preg_split($pattern, $row);
            list(, $row) = explode($comment, $row);
            $row = trim($row);
            $matches = preg_split('/\s+/', $row);
            $num = count($matches);
            if ($num < 9) 
            {
                var_dump($cnt);
                var_dump($matches);
                continue;
            }
            $arr = [
                'title'     => $dealtitle,
                'value'     => intval($value*10),
                'comment'   => $comment,
                'addtime'   => $matches[0],
                'id'        => $matches[1],
                'score'     => $matches[2],
                'weight'    => $matches[3],
                'userid'    => $matches[4],
                'modtime'   => $matches[5],
                'picaddtime'=> $matches[6],
                'imagepath' => $matches[7],
                'url'       => $matches[8],
                'dealid'    => $matches[9],
            ];

            $deal = Deal::findOne($arr['dealid']);
            $deal = empty($deal) ? new Deal : $deal;
            $deal->dealtitle = $arr['title'];
            $deal->id = $arr['dealid'];
            $deal->money = $arr['value'];
            $deal->save();

            $comment = new Feedbackcomment;
            $comment->comment = $arr['comment'];
            $comment->save();

            $feedback = Dealfeedback::findOne($arr['id']);
            if (empty($feedback)) {
                $feedback = new Dealfeedback;
                $feedback->id = $arr['id'];
            }
            $feedback->addtime = $arr['addtime'];
            $feedback->userid = $arr['userid'];
            $feedback->dealid = $arr['dealid'];
            $feedback->commentid = $comment->id;
            $feedback->couponid = $couponid;
            $feedback->save();

            $coupon = Coupon::findOne($couponid);
            if (empty($coupon)) {
                $coupon = new Coupon;
                $coupon->id = $couponid++;
            }
            $coupon->dealid = $deal->id;
            $coupon->userid = $arr['userid'];
            $coupon->status = true;
            $coupon->save();
            if($cnt % 1000 == 1) {
                var_dump($cnt);
                sleep(1);
            }
        }
    }
}
