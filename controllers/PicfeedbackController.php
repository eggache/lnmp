<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
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

    public static function storeImage()
    {
        $model = new PicForm;
        $model->pic = UploadedFile::getInstance($model, 'pic');
        if ($model->pic && $model->validate()) {
            $picName = md5($model->pic->baseName).'.'.$model->pic->extension;
            $model->pic->saveAs('../image/' . $picName);
        }
        return $picName;
    }
}
