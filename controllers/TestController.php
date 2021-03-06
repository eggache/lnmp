<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\PicRedis;
class TestController extends Controller
{
    public function actionIndex()
    {
        $redis = Yii::$app->get('redis');
        $ret = $redis->lrange('pic_redis', 0, -1);
        foreach ($ret as $n => $k) {
            $out = $redis->HGETALL('pic_redis:a:'. $k);
        }
        var_dump($out[0]);exit;
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
        $type = Yii::$app->request->get('type', 3);
        $controller = TrieController::getInstance($type);
        //$controller->trieTest();
        $matches = $controller->search('不要脸真的已经到了无以复加的luo聊表演！');
        var_dump($matches);
    }

    public function actionShowpicredis()
    {
        $picredis = new PicRedis;
        $pic = PicRedis::find()->all();
        var_dump(count($pic));exit;
        foreach ($pic as $value) {
            var_dump($value->file);exit;
        }
        exit;
    }

    public function actionAjax()
    {
        return $this->render('ajax');
    }

    public function actionGet()
    {
        $json = ['ajax is ok'];
        echo json_encode($json); 
        return ;
    }
}
