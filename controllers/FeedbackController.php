<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;
use app\models\Coupon;
use app\models\Deal;

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
            var_dump("insert is ok");exit;
        } else {
            $orderid = $request->get('orderid', 0);
            $dealid = $request->get('dealid', 0);
            $userid = $request->get('userid', 0);
            return $this->render('feedback', [
                'feedback'  => $feedback,
                'picform'   => $picform,
                'orderid'   => $orderid,
                'dealid'    => $dealid,
                'userid'    => $userid,
            ]);
        }
    }

    public function actionFeedbacklist()
    {
        $dealid = 1;
        $deal = Deal::find()->where(['id' => $dealid])->all();
        var_dump($deal);exit;
    }

}
