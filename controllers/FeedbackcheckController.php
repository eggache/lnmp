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
        $type = TextCheckController::TYPE_DEALFEEDBACK_COMMENT;
        $controller = TextCheckController::getInstance($type);
        $ret = $controller->getListForCheck(0);

    }

    public function prepareCommentData()
    {
        var_dump(1111);exit;
    }

}
