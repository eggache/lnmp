<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Coupon;
use app\models\Deal;

class TofeedbackController extends Controller
{
    public function actionIndex()
    {
        $coupons = Coupon::find()->where(['userid' => 1])->all();
        $feedbacklist = [];
        foreach ($coupons as $coupon) {
            $deal = Deal::find()->where(['id' => $coupon->dealid])->one();
            $feedbacklist[] = [
                'dealid'    => $coupon->dealid,
                'couponid'  => $coupon->id,
                'dealName'  => $deal->dealtitle,
                'point'     => $deal->money,
                'userid'    => $coupon->userid,
            ];
        }
        return $this->render('index', ['tofeedback' => $feedbacklist]);
    }
}
