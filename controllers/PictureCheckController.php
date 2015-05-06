<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicRedis;

class PictureCheckController extends Controller
{
    //图片审核状态
    const STATUS_NEW    = 0;
    const STATUS_PASS   = 1;
    const STATUS_BAN    = 2;

    public static $instances;
    const CHECK_QUEUE_PROFIX = "picfeedback_";
    const TYPE_PICFEEDBACK_MAC  = 0;
    
    private $typeConfig;
    private $redis;
    public static $errorCodes = [0 => '机器审核错误', 1 => '机器通过', 2 => '美团logo', 5 => '禁止图片相似', 6 => '大众点评logo'];
    public static $checkTypeConfig = [
        self::TYPE_PICFEEDBACK_MAC      => [
                                            'checkModel'        => 'app\models\FeedbackCommentToCheck',
                                            'prepareCheckData'  => ['app\controllers\FeedbackcheckController', 'prepareCommentData'],
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
        call_user_func(self::$checkTypeConfig[$this->typeConfig]['prepareCheckData']);
        return $checkIds;
    }

}
