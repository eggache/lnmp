<?php
namespace app\commands;

use yii\console\Controller;
use app\models\Deal;
use app\models\Coupon;
use app\controllers\TextCheckController;

class TestController extends Controller
{
    public function actionIndex()
    {
        $deals = Deal::find()->limit(100)->all();
        foreach ($deals as $deal) {
            $coupon = new Coupon;
            $coupon->dealid = $deal->id;
            $coupon->userid = 1;
            $coupon->save();
        }
    }
}
