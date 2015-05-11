<?php
namespace app\classes;

interface PictureToCheckIf
{
    public function getFeedback();
    public function needManCheck();
    public function machineCheck($status, $reason);
    public function putToRecycle($status);
    public function setCheckStatus($checkperson, $status);
}
