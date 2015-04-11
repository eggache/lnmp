<?php
namespace app\classes;
interface TextToCheckIf
{
    public function getFeedback();
    public function machineCheck();
    public function needManCheck();
    public function putToRecycle($status);
    public function setCheckStatus($checkPerson, $status);
}
