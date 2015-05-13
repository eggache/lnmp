<?php
namespace app\models;

use app\models\Dealfeedback;
use app\models\Keywords;
use app\classes\TextToCheckIf;
use app\controllers\KeywordsCheckController;
use app\controllers\TextCheckController;

class TextToReview implements TextToCheckIf
{
    public $feedback;
    public static $recycle = "text_review";

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
        return !empty($this->feedback) & !$this->feedback->getAttr(Dealfeedback::ATTR_REVIEWED);
    }

    public function putToRecycle($status)
    {
        $queue = \app\controllers\QueueController::getInstance(self::$recycle);
        $queue->pushToQueue($this->feedback->id);
    }

    public function setCheckStatus($checkperson, $status)
    {
        $check = Feedbackcheck::findOne([
            'dealfeedbackid' => $this->feedback->id, 
            ['checkperson !=:person', ':person' => 0],
        ]);
        $review = new Feedbackreview;
        $review->feedbackcheckid = $check->id;
        $review->status = $status;
        $review->reviewperson = $checkperson;
        $review->type = 0;
        $review->save();
        if ($check->status != $review->status) {
            $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_CONFIRM);
            $controller->pushForCheck($this->feedback->id);
            $status = $status == TextCheckController::STATUS_PASS ? 1 : 0;
            $this->feedback->setAttr(Dealfeedback::ATTR_REVIEWED, 1);
            $this->feedback->setAttr(Dealfeedback::ATTR_CHECK_PASS, $status);
            $this->feedback->setAttr(Dealfeedback::ATTR_CHECK_BAN, $status^1);
        }
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
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_REVIEW);
        $controller->pushForCheck($id);
    }
}
