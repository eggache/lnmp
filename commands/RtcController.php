<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RtcController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        $config = [
            '\app\models\TextToCheck'   => 'putForRecycle',
            //'\app\models\TextToReview'  => 'putForRecycle',
        ];
        $timeLimit = 0;     //minutes
        foreach($config as $className => $func) {
            $recycle = $className::$recycle;
            $queue = \app\controllers\QueueController::getInstance($recycle);
            while(true){
                $id = $queue->getFormQueue($timeLimit);
                if (empty($id)) {
                    break;
                }
                call_user_func([$className, $func], $id);
            }
        }
    }
}
