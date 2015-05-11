<?php
namespace app\commands;

use yii\console\Controller;
use app\models\Deal;
use app\models\Coupon;
use app\models\Feedbackcheck;
use app\controllers\TextCheckController;

class TestController extends Controller
{
    public function actionIndex()
    {
        $check = Feedbackcheck::find()->all();
        foreach ($check as $obj) {
            $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_REVIEW);
            $controller->pushForCheck($obj->dealfeedbackid);
        }
    }
}
