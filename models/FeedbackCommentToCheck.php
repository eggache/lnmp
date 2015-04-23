<?php
namespace app\models;

use app\models\Dealfeedback;
use app\models\Keywords;
use app\classes\TextToCheckIf;
use app\controllers\KeywordsCheckController;

class FeedbackCommentToCheck implements TextToCheckIf
{
    public $feedback;

    public function __construct($id = 0)
    {
        $this->textCheck();
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
        $this->textCheck();

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

    public function textCheck()
    {
        $comment = "买 了 个 表，，，色情，   你  ，， ╮(╯_╰)╭妹。。。大保健";
        $typeList = [Keywords::TYPE_BISHA, Keywords::TYPE_XIANFAHOUSHEN, Keywords::TYPE_ZANGHUA];
        foreach ($typeList as $type) {
            $keywords = KeywordsCheckController::hasKeyWords($type, $comment);
            $ret[$type] = $keywords;
        }
        var_dump($ret);exit;
    }
}
