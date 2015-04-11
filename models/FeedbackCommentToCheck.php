<?php
namespace app\models;

use app\models\Dealfeedback;
use app\classes\TextToCheckIf;

class FeedbackCommentToCheck implements TextToCheckIf
{
    public $feedback;
    public function __construct($id = 1)
    {
        if ($id) {
            $this->feedback = new Dealfeedback($id);
        } else {
            $this->feedback = new Dealfeedback;
        }
    }

    public function getFeedback()
    {
        return 'to check model is ok !';
    }

    public function machineCheck()
    {
    
    }

    public function needManCheck()
    {
    
    }

    public function putToRecycle($status)
    {
    
    }

    public function setCheckStatus($checkPerson, $status)
    {
    
    }
}
