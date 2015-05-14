<?php
namespace app\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use app\models\Fbcheckeff;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;
use app\models\TextToCheck;
use app\controllers\DataProcessController;
use app\controllers\QueueController;

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
                                            'prepareCheckData'  => ['app\controllers\DataProcessController', 'prepareCommentData'],
                                           ],
        self::TYPE_DEALFEEDBACK_CHECK   => [
                                            'checkModel'        => '\app\models\TextToCheck',
                                            'prepareCheckData'  => ['app\controllers\DataProcessController', 'prepareCheckData'],
                                           ],
        self::TYPE_DEALFEEDBACK_REVIEW  => [
                                            'checkModel'        => '\app\models\TextToReview',
                                            'prepareCheckData'  => ['app\controllers\DataProcessController', 'prepareCheckData'],
                                           ],
        self::TYPE_DEALFEEDBACK_CONFIRM => [
                                            'checkModel'        => '\app\models\TextToConfirm',
                                            'prepareCheckData'  => ['app\controllers\DataProcessController', 'prepareCheckData'],
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

    public function multiSetStatus($checkperson, $multiStatus, $effinfo = [])
    {
        $passcnt = 0;
        foreach($multiStatus as $id => $status) {
            $checkModel = self::$checkTypeConfig[$this->typeConfig]['checkModel'];
            $tocheck = new $checkModel($id);
            if ($status == self::STATUS_PASS) {
                $passcnt ++;
            }
            $tocheck->setCheckStatus($checkperson, $status);
        }

        if (count($effinfo) > 1) {
            $starttime = isset($effinfo['starttime']) ? $effinfo['starttime'] : 0;
            $endtime = isset($effinfo['endtime']) ? $effinfo['endtime'] : 0;
            $status = 0;
            $cnt = count($multiStatus);
            $this->calculate($checkperson, $starttime, $endtime, $passcnt, $cnt, $status);
        }
    }

    public function calculate($checkperson, $starttime, $endtime, $pass, $cnt, $status)
    {
        // 效率
        if ($endtime - $starttime > 1800 || $cnt == 0) {
            return ;
        }
        $hour = date('YmdH');
        $type = 1 + 10 * $this->typeConfig;
        $cond = [
            'hour'  => $hour,
            'type'  => $type,
            'checkperson'   => $checkperson,
        ];
        $eff = Fbcheckeff::find()->where($cond)->one();
        if (empty($eff)) {
            $eff = new Fbcheckeff;
        }
        $eff->hour = $hour;
        $eff->type = $type;
        $eff->cnt += intval($cnt);
        $eff->checkperson = $checkperson;
        $eff->usetime += $endtime - $starttime;
        $eff->save();

        //工作量
        $arr = [
            'checkperson'   => $checkperson,
            'cnt'           => $cnt,
            'type'          => $type,
            'pass'          => $pass,
            'hour'          => date('YmdH'),
        ];
        $controller = QueueController::getInstance(QueueController::TYPE_FBCHECKSTAT);
        $json = json_encode($arr);
        $controller->pushToQueue($json);
    }

}
