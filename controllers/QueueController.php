<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class QueueController extends Controller
{
    const TYPE_FEEDBACK_MAC = 0;
    const TYPE_FEEDBACK_MAN = 2;

    const PROFIX = "recyclequeue_";
    public static $instances;
    protected $typeConfig;
    protected $redis;
    
    public function getInstance($config)
    {
        if (!isset($instances[$config])) {
            self::$instances[$config] = new self($config);
        }
        return self::$instances[$config];
    }

    public function __construct($config)
    {
        $this->typeConfig = $config;
        $this->redis = Yii::$app->redis;
    }

    public function getCheckQueue()
    {
        return self::PROFIX . "{$this->typeConfig}";
    }

    public function pushToQueue($id)
    {
        $queue = $this->getCheckQueue();
        $this->redis->zadd($queue, time(), $id);
    }

    public function getFormQueue($timeLimit = 1)
    {
        $queue = $this->getCheckQueue();
        $this->redis->multi();
        $this->redis->zrange($queue, 0, 0, 'withscores');
        $this->redis->zremrangebyrank($queue, 0, 0);
        $ret = $this->redis->exec();
        if ($ret[0] == null) {
            return ;
        }
        $id = $ret[0][0];
        $time = $ret[0][1];
        if ($time + $timeLimit * 60 < time()){
            return $id;
        } else {
            $this->redis->zadd($queue, $time, $id);
        }
    }
}
