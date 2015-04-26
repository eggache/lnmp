<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\PicRedis;
class TestController extends Controller
{
    public function actionIndex()
    {
    }

    public function actionDelredis()
    {
        Yii::$app->redis->flushall();//删除redis中的所有数据
    }

    public function actionTestredis()
    {
        Yii::$app->redis->zadd('test', 1, 1);
        $ret = Yii::$app->redis->zrange('test', 0, -1);
        var_dump($ret);exit;

    }

    public function actionQueue()
    {
        $controller = QueueController::getInstance(0);
        $controller->getFormQueue();
    }

    public function actionPushqueue()
    {
        $controller = QueueController::getInstance(0);
        $controller->pushToQueue(1);
    }

    public function actionCheckqueue()
    {
        $queue = QueueController::getInstance(0);
        $queue->pushToQueue(1111);
    }

    public function actionTrie()
    {
        $controller = TrieController::getInstance(3);
        //$controller->trieTest();
        $matches = $controller->search('不要脸真的已经到了无以复加的地步！');
        var_dump($matches);
    }

    public function actionShowpicredis()
    {
        $picredis = new PicRedis;
        $pic = PicRedis::find()->all();
        foreach ($pic as $value) {
            var_dump($value->id);
        }
        exit;
    }
}
