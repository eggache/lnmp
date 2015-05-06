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
    const WATER_MARK = '/usr/share/nginx/html/lnmp/web/watermark.png';

    public function __construct()
    {
    }

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
        $img = new ImageController;
        $tempName = $pic->tempName;
        $format = isset($img->pictureExtToFormat[$pic->extension]) ? $img->pictureExtToFormat[$pic->extension] : null;
        $resizeParams = [
            'width' => 800, //指定最大宽度
            'height' => 600, //指定最大高度
            'keepProportion' => true, //保持宽高比
            'originalFormat' => $format,
            'quality' => 80, //图片质量
            'format' => 'JPEG', //目标图片格式,需大写
            'watermark' => self::WATER_MARK,
            'removeProfile' => false,
		];
        $ret = $img->resizeImage($tempName, $resizeParams);
        if (strlen($ret) != 32) {
            var_dump($ret);
            exit;
        }
        $feedback = Dealfeedback::find()->where(['id' => $feedbackid])->one();
        $transaction = Yii::$app->db->beginTransaction();
        $picfeedback = new Picfeedback;
        $picfeedback->imagename = $ret . '.' . $pic->extension;
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
