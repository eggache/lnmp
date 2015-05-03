<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;
use app\models\Coupon;
use app\models\Deal;
use app\controllers\PicfeedbackController;

class FeedbackController extends Controller
{
    public function actionFeedback()
    {
        $request = Yii::$app->request;
        $feedback = new FeedbackForm;
        $picform = new PicForm;
        if ($request->isPost) {
            $feedback = $request->post()['FeedbackForm'];
            $comment = $feedback['comment'];
            $feedbackid = Dealfeedback::add($feedback);
            $picids = PicfeedbackController::storeInRedis($feedbackid);
            var_dump("insert is ok");exit;
        } else {
            $couponid = $request->get('couponid', 0);
            $dealid = $request->get('dealid', 0);
            $userid = $request->get('userid', 0);
            return $this->render('feedback', [
                'feedback'  => $feedback,
                'picform'   => $picform,
                'couponid'  => $couponid,
                'dealid'    => $dealid,
                'userid'    => $userid,
            ]);
        }
    }

    public function actionList()
    {
        $dealid = Yii::$app->request->get('dealid', 1);
        $deal = Deal::find()->where(['id' => $dealid])->one();
        $title = $deal['dealtitle'];
        $coupons = Coupon::find()->where(['dealid' => $dealid])->all();
        var_dump(count($coupons));exit;
        $list = [];
        foreach ($coupons as $coupon) {
            $feedback = Dealfeedback::find()
                ->where(['couponid' => $coupon['id']])
                ->one();
            $list[] = [
                'dealid'    => $dealid,
                'couponid'  => $coupon['id'],
                'userid'    => $coupon['userid'],
                'comment'   => $feedback->getComment(),
            ];
        }

        return $this->render('list', [
                'list'  => $list,
                'title' => $title,
            ]);
    }

}
