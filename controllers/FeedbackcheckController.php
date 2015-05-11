<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\controllers\TextCheckController;
use app\models\TextToCheck;

class FeedbackcheckController extends Controller
{
    public function actionIndex()
    {
        $type = TextCheckController::TYPE_DEALFEEDBACK_COMMENT;
        $controller = TextCheckController::getInstance($type);
        $ret = $controller->getListForCheck(0);

    }

    public function prepareCheckData($tocheck)
    {
        $list = [];
        foreach ($tocheck as $id) {
            $check = \app\models\Feedbackcheck::find()->where(['dealfeedbackid' => $id])->one();
            $feedback = \app\models\Dealfeedback::findOne($id);
            $dealtitle = \app\models\Deal::findOne($feedback->dealid)->dealtitle;
            $comment = \app\models\Feedbackcomment::findOne($feedback->commentid)->comment;
            $list[] = [
                'id'        => $feedback->id,
                'title'     => $dealtitle,
                'score'     => $feedback->score,
                'addtime'   => $feedback->addtime,
                'comment'   => $comment,
                'keyword'   => $check->reason,
            ];
        }
        return $list;
    }

    public function prepareReviewData($tocheck)
    {
        $list = [];
        foreach ($tocheck as $id) {
            $check = \app\models\Feedbackcheck::find()->where(['dealfeedbackid' => $id])->one();
            $feedback = \app\models\Dealfeedback::findOne($id);
            $dealtitle = \app\models\Deal::findOne($feedback->dealid)->dealtitle;
            $comment = \app\models\Feedbackcomment::findOne($feedback->commentid)->comment;
            $list[] = [
                'id'        => $feedback->id,
                'title'     => $dealtitle,
                'score'     => $feedback->score,
                'addtime'   => $feedback->addtime,
                'comment'   => $comment,
                'keyword'   => $check->reason,
            ];
        }
        return $list;
    }
}
