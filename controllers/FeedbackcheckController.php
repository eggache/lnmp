<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\controllers\TextCheckController;
use app\models\FeedbackCommentToCheck;

class FeedbackcheckController extends Controller
{
    public function actionIndex()
    {
        $controller = TextCheckController::getInstance('check');
        $ret = $controller->getListForCheck(0);

    }

}
