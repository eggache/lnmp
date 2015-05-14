<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\Coupon;
use app\models\Deal;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InitController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($default = 5)
    {
        $deals = Deal::find()->limit(5)->all();
        foreach ($deals as $deal) {
            var_dump($deal->id);
            $coupon = new Coupon;
            $coupon->dealid = $deal->id;
            $coupon->userid = 1;
            $coupon->save();
        }
    }
}

