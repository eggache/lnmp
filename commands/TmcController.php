<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Keywords;
use app\models\TextToCheck;
use app\controllers\TrieController;

class TextmaccheckController extends Controller
{
    public function actionIndex()
    {
        $redis = Yii::$app->redis;
        $typeList = [Keywords::TYPE_BISHA, Keywords::TYPE_XIANFAHOUSHEN, Keywords::TYPE_ZANGHUA];
        foreach ($typeList as $type) {
            $trie = TrieController::getInstance(3);
            $list = $redis->zrange('checkqueue_0', 0, -1);
            foreach ($list as $id) {
                $model = new TextToCheck($id);
                $model->machineCheck();
            }
        }
    }
}
