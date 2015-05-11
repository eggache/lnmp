<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Keywords;
use app\controllers\TrieController;

class TextmaccheckController extends Controller
{
    public function actionIndex()
    {
        $redis = Yii::$app->redis;
        $typeList = [Keywords::TYPE_BISHA, Keywords::TYPE_XIANFAHOUSHEN, Keywords::TYPE_ZANGHUA];
        foreach ($typeList as $type) {
            $trie = TrieController::getInstance(0);
            $matches = $trie->search('不要脸真的已经到了无以复加的地步！luo聊表演');
            var_dump(11111);
            var_dump($matches);exit;

        }
    
    }
}
