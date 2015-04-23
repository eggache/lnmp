<?php
namespace app\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;
use app\models\FeedbackCommentToCheck;
use app\controllers\FeedbackcheckController;

class TextCheckController extends Controller
{
    const TYPE_DEALFEEDBACK_COMMENT = 0;
    const TYPE_DEALFEEDBACK_REPLY   = 1;
    const TYPE_DEALFEEDBACK_HIGH    = 2;

    const CHECK_QUEUE_PROFIX = "checkqueue_";
    public static $checkTypeConfig = [
        self::TYPE_DEALFEEDBACK_COMMENT => [
                                            'checkModel'        => 'app\models\FeedbackCommentToCheck',
                                            'prepareCheckData'  => ['app\controllers\FeedbackcheckController', 'prepareCommentData'],
                                           ],
        self::TYPE_DEALFEEDBACK_REPLY   => [
                                            'checkModel'        => 'app\models\FeedbackReplyToCheck',
                                            'prepareCheckData'  => ['app\controllers\FeedbackcheckController', 'prepareReplyData'],
                                           ],
        self::TYPE_DEALFEEDBACK_HIGH    => [
                                            'checkModel'        => 'app\models\HqFeedbackToCheck',
                                            'prepareCheckData'  => ['app\controllers\FeedbackcheckController', 'prepareHighData'],
                                           ],
    ];

    public static $instance;
    private $typeConfig;
    private $redis;

    public static function getInstance($config)
    {
        if (!isset(self::$checkTypeConfig[$config])) {
            throw new Exception("error config");
        }
        if (!isset(self::$instances[$config])) {
            self::$instances[$config] = new self($config);
        }
        return self::$instances[$config];
    }

    public function __construct($config)
    {
        $this->redis = Yii::$app->redis;
        $this->typeConfig = $config;
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
        $checkModel = self::$checkTypeConfig[$this->typeConfig]['checkModel'];
        $checkIds = [];
        foreach ($ids as $id) {
            $tocheck = new $checkModel($id);
            if ($tocheck->needManCheck()) {
                $tocheck->putToRecycle($status);
                $checkIds[] = $id;
            }
        }
        call_user_func(self::$checkTypeConfig[$this->typeConfig]['prepareCheckData']);
        return $checkIds;
    }
}
