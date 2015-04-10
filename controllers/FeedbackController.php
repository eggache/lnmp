<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;

class FeedbackController extends Controller
{
    public function actionFeedback()
    {
        $request = Yii::$app->request;
        $feedback = new FeedbackForm;
        $picform = new PicForm;
        if ($request->isPost) {
            $feedback = $request->post()['FeedbackForm'];
            //$picids = PicfeedbackController::storeImage();
            $feedbackid = Dealfeedback::add($feedback);
            Yii::$app->redis->zadd('feedbackid', $feedbackid, $feedbackid);
            $id = Yii::$app->redis->zrange('feedbackid', 0, -1);
            var_dump($id);exit;
        } else {
            return $this->render('feedback', [
                'feedback'  => $feedback,
                'picform'   => $picform,
            ]);
        }
    }

    public static function computeFeedbackWeight($userid, $orderid, $dealid, $poiid, $comment, $score, $piccount = 0)
    {

    }

    public static function getFTWeight($feedtimes)
    {
        if ($feedtimes == 0) {
        	return 0;
        } elseif ($feedtimes >= 1 && $feedtimes <= 2) {
        	return 1;
        } elseif ($feedtimes >= 3 && $feedtimes <= 5) {
        	return 2;
        } elseif ($feedtimes >= 6 && $feedtimes <= 15) {
        	return 3;
        } elseif ($feedtimes >= 16 && $feedtimes <= 30) {
        	return 4;
        } elseif ($feedtimes >= 31 && $feedtimes <= 50) {
        	return 5;
        } elseif ($feedtimes >= 51 && $feedtimes <= 250) {
        	return 6;
        } else {
        	return 7;
        }
    }

}
