<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\controllers\PictureCheckController;
use app\models\PicToCheck;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PicmacresultController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        $redis = Yii::$app->redis;
        $key = 'pic_mac_result';
        $redis->multi();
        $redis->zrange($key, 0, 10, "withscores");
        $redis->zremrangebyrank($key, 0, 0);
        $objs = $redis->exec();
        $objs = empty($objs) ? [] : $objs[0];
        $ret = [];
        for ($i = 0; $i < count($objs); $i += 2) {
            $ret[$objs[$i]] = $objs[$i+1];
        }
        foreach ($ret as $result => $id) {
            $reason = isset(PictureCheckController::$errorCodes[$result]) ? PictureCheckController::$errorCodes[$result] : 0;
            $reason = $result == 1 ? '' : $reason;
            $tocheck = new PicToCheck($id);
            $status = $result <= 1 ? PictureCheckController::STATUS_PASS : PictureCheckController::STATUS_BAN;
            $tocheck->machineCheck($status, $reason);
        }
    }
}
