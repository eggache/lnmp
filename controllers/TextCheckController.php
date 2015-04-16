<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;
use app\models\FeedbackCommentToCheck;

class TextCheckController extends Controller
{
    public $redis;
    public $key;
    const TYPE_DEALFEEDBACK_COMMENT = 0;

    public $config = [
        self::TYPE_DEALFEEDBACK_COMMENT     => [
                                                    'checkModel'    => 'app\models\FeedbackCommentToCheck',
                                                ],
    ];

    public static $preConfig;

    public static function getInstance($config = 'check')
    {
        $obj = new static;
        $obj->key = "text_{$config}";
        return $obj;
    }

    public function __construct()
    {
        $this->redis = Yii::$app->redis;
    }

    public function getPreset()
    {
        return [$this->redis, $this->key];
    }

    public function pushForCheck($id, $status)
    {
        list($redis, $key) = $this->getPreset();
        $redis->zadd($key, $id, $id);
    }

    public function getFromCheckQueue($status, $row = 20)
    {
        list($redis, $key) = $this->getPreset();
        $redis->multi();
        $redis->zrange($key, 0, $row);
        //$redis->ZREMRANGEBYRANK($key, 0, $row);
        $ret = $redis->exec();
        return $ret[0];
    }

    public function getListForCheck($status, $row = 20)
    {
        $ids = $this->getFromCheckQueue($status, $row);
        $checkIds = [];
        foreach ($ids as $id) {
            $tocheck = new FeedbackCommentToCheck($id);
            if ($tocheck->needManCheck()) {
                $tocheck->putToRecycle($status);
                $checkIds[] = $id;
            }
        }
        call_user_func($modelName, $prepareFunc);
        return $checkIds;
    }
}
