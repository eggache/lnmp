<?php
namespace app\models;

use app\models\Dealfeedback;
use app\models\Keywords;
use app\classes\TextToCheckIf;
use app\controllers\KeywordsCheckController;
use app\controllers\TextCheckController;

class TextToConfirm implements TextToCheckIf
{
    public $feedback;
    public static $recycle = "text_confirm";

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
    }

    public function needManCheck()
    {
        return !empty($this->feedback) & !$this->feedback->getAttr(Dealfeedback::ATTR_CONFIRMED);
    }

    public function putToRecycle($status)
    {
        $queue = \app\controllers\QueueController::getInstance(self::$recycle);
        $queue->pushToQueue($this->feedback->id);
    }

    public function setCheckStatus($checkPerson, $status)
    {
    }

    public function getComment()
    {
        $comment = empty($this->feedback) ? '' : $this->feedback->getComment();
        return $comment;
    }

    public static function putForRecycle($id)
    {
        $obj = new self($id);
        if (!$obj->needManCheck()) {
            return ;
        }
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_CONFIRM);
        $controller->pushForCheck($id);
    }
}
