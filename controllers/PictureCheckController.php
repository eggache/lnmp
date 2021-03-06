<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicRedis;
use app\models\Fbcheckeff;
use app\models\Fbcheckstat;
use app\controllers\DataProcessController;

class PictureCheckController extends Controller
{
    //图片审核状态
    const STATUS_NEW    = 0;
    const STATUS_PASS   = 1;
    const STATUS_BAN    = 2;

    public static $instances;
    const CHECK_QUEUE_PROFIX = "picfeedback_";
    const TYPE_PICFEEDBACK_MAC  = 0;
    const TYPE_PICFEEDBACK_CHE  = 1;
    const TYPE_PICFEEDBACK_REV  = 2;
    const TYPE_PICFEEDBACK_CON  = 3;
    
    private $typeConfig;
    private $redis;
    public static $errorCodes = [0 => '机器审核错误', 1 => '机器通过', 2 => '美团logo', 5 => '禁止图片相似', 6 => '大众点评logo'];
    public static $checkTypeConfig = [
        self::TYPE_PICFEEDBACK_MAC      => [
                                            'checkModel'        => '\app\models\PicToCheck',
                                            'prepareCheckData'  => ['app\controllers\DataProcessController', 'preparePicCheckData'],
                                           ],
        self::TYPE_PICFEEDBACK_CHE      => [
                                            'checkModel'        => '\app\models\PicToCheck',
                                            'prepareCheckData'  => ['app\controllers\DataProcessController', 'preparePicCheckData'],
                                           ],
        self::TYPE_PICFEEDBACK_REV      => [
                                            'checkModel'        => '\app\models\PicToReview',
                                            'prepareCheckData'  => ['app\controllers\DataProcessController', 'preparePicCheckData'],
                                           ],
        self::TYPE_PICFEEDBACK_CON      => [
                                            'checkModel'        => '\app\models\PicToConfirm',
                                            'prepareCheckData'  => ['app\controllers\DataProcessController', 'prepareCheckData'],
                                           ],
        
    ];
    const IMAGE_HASH = "image_hash";

    public static function getInstance($config)
    {
        if (!isset(self::$checkTypeConfig[$config])) {
            throw new Exception('don`t have this config');
        }
        if (!isset(self::$instances[$config])) {
            self::$instances[$config] = new self($config);
        }
        return self::$instances[$config];
    }

    public function __construct($config)
    {
        $this->typeConfig = $config;
        $this->redis = Yii::$app->redis;
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
        $ret = call_user_func(self::$checkTypeConfig[$this->typeConfig]['prepareCheckData'], $checkIds);
        return $ret;
    }

    public function multiSetStatus($checkperson, $multiStatus, $effinfo = [])
    {
        $checkModel = self::$checkTypeConfig[$this->typeConfig]['checkModel'];
        $passcnt = 0;
        foreach ($multiStatus as $id => $status) {
            $model = new $checkModel($id);
            if ($status == self::STATUS_PASS) {
                $passcnt ++;
            }
            $model->setCheckStatus($checkperson, $status);
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
        $type = $this->typeConfig;
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
        $eff->type = $this->typeConfig;
        $eff->cnt += intval($cnt);
        $eff->checkperson = $checkperson;
        $eff->usetime += $endtime - $starttime;
        $eff->save();

        //工作量
        $arr = [
            'checkperson'   => $checkperson,
            'cnt'           => $cnt,
            'type'          => $this->typeConfig,
            'pass'          => $pass,
            'hour'          => date('YmdH'),
        ];
        $controller = QueueController::getInstance(QueueController::TYPE_FBCHECKSTAT);
        $json = json_encode($arr);
        $controller->pushToQueue($json);
    }

}
