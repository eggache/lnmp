<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Feedbackcheck;
use app\models\Dealfeedback;
use app\models\Feedbackcomment;
use app\models\Deal;
use app\models\Picfeedback;

class DataProcessController extends Controller
{
    public function prepareCheckData($tocheck)
    {
        $list = [];
        foreach ($tocheck as $id) {
            $check = Feedbackcheck::find()->where(['dealfeedbackid' => $id])->one();
            $feedback = Dealfeedback::findOne($id);
            $dealtitle = Deal::findOne($feedback->dealid)->dealtitle;
            $comment = Feedbackcomment::findOne($feedback->commentid)->comment;
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
            $check = Feedbackcheck::find()->where(['dealfeedbackid' => $id])->one();
            $feedback = Dealfeedback::findOne($id);
            $dealtitle = Deal::findOne($feedback->dealid)->dealtitle;
            $comment = Feedbackcomment::findOne($feedback->commentid)->comment;
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

    public function preparePicCheckData($tocheck)
    {
        $list = [];
        foreach ($tocheck as $id) {
            $model = Picfeedback::findOne($id); 
            $list[] = [
                'url'   => '/image/'.$model->imagename,
                'id'    => $model->id,
            ];
        }
        return $list;
    }
}
