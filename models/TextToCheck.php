<?php
namespace app\models;

use app\models\Dealfeedback;
use app\models\Keywords;
use app\classes\TextToCheckIf;
use app\controllers\KeywordsCheckController;
use app\controllers\TextCheckController;

class TextToCheck implements TextToCheckIf
{
    public $feedback;
    public static $recycle = "text_check";

    public function __construct($id = 0)
    {
        if ($id) {
            $this->feedback = Dealfeedback::findOne($id);
        } else {
            $this->feedback = new Dealfeedback;
        }
    }

    public function getFeedback()
    {
        return $this->feedback;
    }

    public function machineCheck()
    {
        if (empty($this->feedback)) {
            return ;
        }
        $result = $this->textCheck();
        var_dump($result);
        list($ban, $reason) = $result;
        $check = new Feedbackcheck;
        $check->oldstatus = 0;
        $check->status = empty($reason) ^ 1;
        $check->dealfeedbackid = $this->feedback->id;
        $check->reason = empty($reason) ? "" : $reason;
        $check->save();
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_CHECK);
        $controller->pushForCheck($this->feedback->id);
    }

    public function needManCheck()
    {
        return !empty($this->feedback) & $this->feedback->needManCheck();
    }

    public function putToRecycle($status)
    {
        $queue = \app\controllers\QueueController::getInstance(self::$recycle);
        $queue->pushToQueue($this->feedback->id);
    }

    public function setCheckStatus($checkPerson, $status)
    {
        $check = new Feedbackcheck;
        $check->oldstatus = 0;
        $check->status = $status;
        $check->dealfeedbackid = $this->feedback->id;
        $check->checkperson = $checkPerson;
        $check->save();
        if (1|rand(1,100) > 2) {
            $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_REVIEW);
            $controller->pushForCheck($this->feedback->id);
        }
        $status = $status == TextCheckController::STATUS_PASS ? 1 : 0;
        $this->feedback->setAttr(Dealfeedback::ATTR_MAN_CHECKED, 1);
        $this->feedback->setAttr(Dealfeedback::ATTR_CHECK_PASS, $status);
        $this->feedback->setAttr(Dealfeedback::ATTR_CHECK_BAN, $status^1);
    }

    public function getComment()
    {
        $comment = empty($this->feedback) ? '' : $this->feedback->getComment();
        return $comment;
    }

    public function textCheck()
    {
        $comment = $this->getComment();
        $comment = preg_replace('/\s/', '', $comment);
        $cmd = "textcheck 1 " . $comment;
        $ret  = system($cmd);
        $matches = explode(" ", $ret);
        var_dump("count of matches".count($matches));
        if (count($matches) > 1) {
            return $matches;
        } else {
            return [0, ""];
        }
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
