<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Keywords;
use app\models\TextToCheck;
use app\controllers\TrieController;
use app\controllers\TextCheckController;

class TmcController extends Controller
{
    public function actionIndex()
    {
        $redis = Yii::$app->redis;
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_COMMENT);
        $list = $controller->getFromCheckQueue();
        $list = [1];
        foreach ($list as $id) {
            $model = new TextToCheck($id);
            $model->machineCheck();
        }
    }
}
