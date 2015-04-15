<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\PicRedis;
class TestController extends Controller
{
    public function actionIndex()
    {
        $picredis = new PicRedis;
        $pic = PicRedis::find()->all();
        foreach ($pic as $value) {
            var_dump($value->attributes);
        }
        exit;
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
}
