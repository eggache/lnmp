<?php
namespace app\models;

use app\classes\PictureToCheckIf;
use app\models\Picfeedbackcheck;
use app\controllers\PictureCheckController;

class PicToCheck implements PictureToCheckIf
{
    private $feedback;
    public static $recycle = "pic_check";
    public function __construct($id)
    {
        if ($id) {
            $this->feedback = Picfeedback::findOne($id);
        } else {
            $this->feedback = new Picfeedback;
        }
    }

    public function getFeedback()
    {
        return $this->feedback;
    }

    public function machineCheck($status, $reason)
    {
        $check = new Picfeedbackcheck;
        if (empty($this->feedback)) {
            return ;
        }
        $check->picfeedbackid = $this->feedback->id;
        $check->oldstatus = 0;
        $check->status = $status;
        $check->checkperson = 0;
        $check->reason = $reason;
        $check->save();
        $this->feedback->setCheckStatus(0, $status);
        $controller = PictureCheckController::getInstance(PictureCheckController::TYPE_PICFEEDBACK_CHE);
        $controller->pushForCheck($this->feedback->id);
    }

    public function needManCheck()
    {
        return !empty($this->feedback) & !$this->feedback->getAttr(Picfeedback::ATTR_MAN_CHECKED);
    }

    public function putToRecycle($status)
    {
        $queue = \app\controllers\QueueController::getInstance(self::$recycle);
        $queue->pushToQueue($this->feedback->id);
    }

    public function setCheckStatus($checkperson, $status)
    {
        $this->feedback->setCheckStatus($checkperson, $status);
        $check = new Picfeedbackcheck;
        $check->picfeedbackid = $this->feedback->id;
        $check->oldstatus = 0;
        $check->status = $status;
        $check->checkperson = $checkperson;
        $check->save();
    }

    public static function putForRecycle($id)
    {
        $obj = new self($id);
        if (!$obj->needManCheck()) {
            return ;
        }
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_CHECK);
        $controller->pushForCheck($id);
    }
}
