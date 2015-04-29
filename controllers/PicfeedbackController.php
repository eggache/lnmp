<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
use app\models\PicRedis;
use yii\web\UploadedFile;
use app\models\Picfeedback;
use app\models\Dealfeedback;

class PicfeedbackController extends Controller
{
    public function actionUpload()
    {
        $model = new PicForm;
        if (Yii::$app->request->isPost){
            $model->pic = UploadedFile::getInstance($model, 'pic');

            if ($model->pic && $model->validate()) {                
                $model->pic->saveAs('../image/' . $model->pic->baseName . '.' . $model->pic->extension);
            }
        }
        return $this->render('upload', ['model' => $model]);
    }

    public function storeInRedis($feedbackid)
    {
        $request = Yii::$app->request;
        $model = new PicRedis;
        $pic = UploadedFile::getInstanceByName("pic");
        $file = file_get_contents($pic->tempName);
        $controller = PictureCheckController::getInstance(PictureCheckController::TYPE_PICFEEDBACK_MAC);
        $ret = $controller->checkUniqueImage(md5($file));
        if ($ret == 'exist') {
            return 'picture exist';
        }
        $imageName = $controller->insertToRedis($file, $pic->extension);
        $feedback = Dealfeedback::find()->where(['id' => $feedbackid])->one();
        $transaction = Yii::$app->db->beginTransaction();
        $picfeedback = new Picfeedback;
        $picfeedback->imagename = $imageName . '.' . $pic->extension;
        $picfeedback->userid = $feedback->userid;
        $picfeedback->dealid = $feedback->dealid;
        $picfeedback->couponid = $feedback->couponid;
        $picfeedback->insert();
        $transaction->commit();
    }

    public function storeImage()
    {
    
    }

}
