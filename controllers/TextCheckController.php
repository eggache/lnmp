<?php
namespace app\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;
use app\models\TextToCheck;
use app\controllers\FeedbackcheckController;

class TextCheckController extends Controller
{
    const TYPE_DEALFEEDBACK_COMMENT = 0;
    const TYPE_DEALFEEDBACK_CHECK   = 1;
    const TYPE_DEALFEEDBACK_REVIEW  = 2;
    const TYPE_DEALFEEDBACK_CONFIRM = 3;

    const STATUS_NEW    = 0;
    const STATUS_PASS   = 1;
    const STATUS_BAN    = 2;

    const CHECK_QUEUE_PROFIX = "checkqueue_";
    public static $checkTypeConfig = [
        self::TYPE_DEALFEEDBACK_COMMENT => [
                                            'checkModel'        => '\app\models\TextToCheck',
                                            'prepareCheckData'  => ['app\controllers\FeedbackcheckController', 'prepareCommentData'],
                                           ],
        self::TYPE_DEALFEEDBACK_CHECK   => [
                                            'checkModel'        => '\app\models\TextToCheck',
                                            'prepareCheckData'  => ['app\controllers\FeedbackcheckController', 'prepareCheckData'],
                                           ],
        self::TYPE_DEALFEEDBACK_REVIEW  => [
                                            'checkModel'        => '\app\models\TextToReview',
                                            'prepareCheckData'  => ['app\controllers\FeedbackcheckController', 'prepareCheckData'],
                                           ],
        self::TYPE_DEALFEEDBACK_CONFIRM => [
                                            'checkModel'        => '\app\models\TextToConfirm',
                                            'prepareCheckData'  => ['app\controllers\FeedbackcheckController', 'prepareCheckData'],
                                           ],
    ];

    public static $instances;
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
        return self::CHECK_QUEUE_PROFIX . $this->typeConfig . "_{$status}";
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
        $redis->ZREMRANGEBYRANK($key, 0, $row);
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
        $ret = call_user_func(self::$checkTypeConfig[$this->typeConfig]['prepareCheckData'], $checkIds);
        return $ret;
    }

    public function multiSetStatus($checkperson, $multiStatus)
    {
        foreach($multiStatus as $id => $status) {
            $checkModel = self::$checkTypeConfig[$this->typeConfig]['checkModel'];
            $tocheck = new $checkModel($id);
            $tocheck->setCheckStatus($checkperson, $status);
        }
    }
}
