<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
use app\models\PicRedis;
use yii\web\UploadedFile;

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

    public function storeInRedis()
    {
        $model = new PicRedis;
        $pic = UploadedFile::getInstanceByName("pic");
        $file = file_get_contents($pic->tempName);
        $name = md5($file);
        $exist = PicRedis::find()->where(['id' => $file])->exists();
        if ($exist) {
            return 'Picture exist';
        }
        $model->id = $name;
        $model->file = $file;
        $model->save();
    }

    public function storeImage()
    {
    
    }

}
