<?php
namespace app\models;

use app\models\Dealfeedback;
use app\classes\TextToCheckIf;

class FeedbackCommentToCheck implements TextToCheckIf
{
    public $feedback;

    public function __construct($id = 0)
    {
        if ($id) {
            $this->feedback = Dealfeedback::getDealFeedbackById($id);
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
        return $this->feedback->needManCheck();
    }

    public function putToRecycle($status)
    {

    }

    public function setCheckStatus($checkPerson, $status)
    {

    }
}
