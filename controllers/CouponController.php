<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Deal;

class CouponController extends Controller
{
    public function actionAdd()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            $deallist = Deal::find()->all();
            return $this->render('add', [
                    'deallist'  => $deallist,
                ]);
        } else {
            $dealid = $request->get('dealid', 0);
            $userid = $request->get('userid', 1);
            $coupon = new Coupon;
            if (!$dealid || !$userid) {
                return ;
            }
            $coupon->dealid = $dealid;
            $coupon->userid = $userid;
            $coupon->update();
        }
    }
}
