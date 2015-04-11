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
        var_dump(22);exit;
        Yii::$app->redis->flushall();//删除redis中的所有数据
    }
}
