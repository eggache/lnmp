<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;

class FeedbackController extends Controller
{
    public function actionFeedback()
    {
        $request = Yii::$app->request;
        $feedback = new FeedbackForm;
        $picform = new PicForm;
        if ($request->isPost) {
            $feedback = $request->post()['FeedbackForm'];
            //$picids = PicfeedbackController::storeImage();
            $feedbackid = Dealfeedback::add($feedback);
            var_dump("insert is ok");exit;
        } else {
            return $this->render('feedback', [
                'feedback'  => $feedback,
                'picform'   => $picform,
            ]);
        }
    }

}
