<?php
namespace app\commands;

use yii\console\Controller;
use app\controllers\TextCheckController;

class TestController extends Controller
{
    public function actionIndex()
    {
        $controller = TextCheckController::getInstance('check');
        while(true) {
            for($i = 1; $i < 21; $i ++) {
                $controller->pushForCheck($i, 0);
            }
            sleep(2);
        }
    }
}
