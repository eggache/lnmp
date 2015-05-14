<?php
namespace app\commands;

use yii\console\Controller;
use app\models\Deal;
use app\models\Coupon;
use app\models\Feedbackcheck;
use app\models\Fbcheckstat;
use app\controllers\TextCheckController;
use app\controllers\QueueController;

class StatController extends Controller
{
    public function actionIndex()
    {
        $queue = QueueController::getInstance(QueueController::TYPE_FBCHECKSTAT);
        $num = 30;
        $stat = [];
        for ($i = 0; $i < $num; $i ++) {
            $json = $queue->getFormQueue(0);
            if (empty($json)) {
                break;
            }
            $arr = json_decode($json, true);
            $key = $arr['hour'] . '_' . $arr['checkperson'] . '_' . $arr['type'];
            $pass = isset($stat[$key]['pass']) ? $stat[$key]['pass'] + $arr['pass'] : $arr['pass'];
            $cnt = isset($stat[$key]['cnt']) ? $stat[$key]['cnt'] + $arr['cnt'] : $arr['cnt'];
            $stat[$key] = [
                'hour'  => $arr['hour'],
                'checkperson'   => $arr['checkperson'],
                'type'          => $arr['type'],
                'pass'          => $pass,
                'cnt'           => $cnt,
            ];
        }
        foreach ($stat as $obj) {
            $cond = [
                'hour'  => $obj['hour'],
                'checkperson'   => $obj['checkperson'],
                'type'  => $obj['type'],
            ];
            $fb = Fbcheckstat::find()->where($cond)->one();
            if (empty($fb)) {
                $fb = new Fbcheckstat; 
            }
            $fb->passcnt += $obj['pass'];
            $fb->totalcnt += $obj['cnt'];
            $fb->checkperson = $obj['checkperson'];
            $fb->hour   = $obj['hour'];
            $fb->type   = $obj['type'];
            $fb->save();
        }
    }
}

