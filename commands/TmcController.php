<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Keywords;
use app\models\TextToCheck;
use app\controllers\TrieController;

class TmcController extends Controller
{
    public function actionIndex()
    {
        $redis = Yii::$app->redis;
        $trie = TrieController::getInstance(3);
        $list = $redis->zrange('checkqueue_0_0', 0, -1);
        foreach ($list as $id) {
            $model = new TextToCheck($id);
            $model->machineCheck();
        }
    }
}
