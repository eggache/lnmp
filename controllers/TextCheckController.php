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
    const TYPE_DEALFEEDBACK_COMMENT = 0;

    const CHECK_QUEUE_PROFIX = "checkqueue_";
    public $config = [
        self::TYPE_DEALFEEDBACK_COMMENT     => [
                                                    'checkModel'    => 'app\models\FeedbackCommentToCheck',
                                                    'prepareDate'   => ['app\controllers\FeedbackcheckController', 'prepareCheckDate'],
                                                ],
    ];

    public static $instance;
    private $preConfig;
    private $redis;

    public static function getInstance($config)
    {
        if (!isset(self::$instance[$config])) {
            self::$instance[$config] = new self($config);
        }
        return self::$instance[$config];
    }

    public function __construct($config)
    {
        $this->redis = Yii::$app->redis;
        $this->preConfig = $config;
    }

    public function getCheckQueue($status = 0)
    {
        return self::CHECK_QUEUE_PROFIX . "{$status}";
    }

    public function getPreset($status = 0)
    {
        $key = $this->getCheckQueue($status);
        return [$this->redis, $key];
    }

    public function pushForCheck($id, $status = 0)
    {
        list($redis, $key) = $this->getPreset($status);
        $redis->zadd($key, $id, $id);
    }

    public function getFromCheckQueue($status = 0, $row = 20)
    {
        list($redis, $key) = $this->getPreset($status);
        $redis->multi();
        $redis->zrange($key, 0, $row);
        //$redis->ZREMRANGEBYRANK($key, 0, $row);
        $ret = $redis->exec();
        return $ret[0];
    }

    public function getListForCheck($status = 0, $row = 20)
    {
        $ids = $this->getFromCheckQueue($status, $row);
        $modelName = $this->config[$this->preConfig]['checkModel'];
        $checkIds = [];
        foreach ($ids as $id) {
            $tocheck = new FeedbackCommentToCheck($id);
            if ($tocheck->needManCheck()) {
                $tocheck->putToRecycle($status);
                $checkIds[] = $id;
            }
        }
        call_user_func($this->config[$this->preConfig]['prepareDate'], $checkIds);
        return $checkIds;
    }
}
