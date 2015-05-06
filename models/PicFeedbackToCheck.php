<?php
namespace app\models;

use app\classes\PictureToCheckIf;
use app\models\Picfeedbackcheck;

class PicFeedbackToCheck implements PictureToCheckIf
{
    private $feedback;
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

    public function needManCheck()
    {
        return $this->feedback->needManCheck();
    }

    public function machineCheck($status, $reason)
    {
        $check = new Picfeedbackcheck;
        $check->picfeedbackid = $this->feedback->id;
        $check->couponid = $this->feedback->couponid;
        $check->oldstatus = 0;
        $check->status = $status;
        $check->checkperson = 0;
        $check->reason = $reason;
        $check->save();
        $this->setCheckStatus(0, $status);
    }

    public function putToRecycle($status)
    {
    
    }

    public function setCheckStatus($checkperson, $status)
    {
        $this->feedback->setCheckStatus($checkperson, $status);
    }
}
