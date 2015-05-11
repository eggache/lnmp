<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\Keywords;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class KeywordController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        $type = [
            Keywords::TYPE_BISHA		    => 'bisha.dic',
            Keywords::TYPE_XIANSHENHOUFA	=> 'xshf.dic',
            Keywords::TYPE_XIANFAHOUSHEN	=> 'xfhs.dic',
            Keywords::TYPE_ZANGHUA		    => 'zanghua.dic',
            Keywords::TYPE_IGNORE		    => 'ignore.dic',
        ];
        foreach ($type as $index => $file) {
            $keywords = Keywords::find()->where(['type' => $index])->all();
            foreach ($keywords as &$keyword) {
                $keyword = $keyword['raw'];
            }
            $out = json_encode($keywords);
            $fp = fopen($file, "w+");
            fwrite($fp, $out);
            fclose($fp);
        }
        echo "ok";
    }
}
