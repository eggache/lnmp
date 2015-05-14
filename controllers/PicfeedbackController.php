<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use app\models\PicForm;
use app\models\PicRedis;
use yii\web\UploadedFile;
use app\models\Picfeedback;
use app\models\Dealfeedback;
use app\models\Picfeedbackcheck;

class PicfeedbackController extends Controller
{
    const WATER_MARK = '/usr/share/nginx/html/lnmp/web/watermark.png';

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
        if (empty($pic)) return;
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
        $picfeedback->imagename = $ret . '.' . $img->pictureExtToFormat[$pic->extension];
        $picfeedback->userid = $feedback->userid;
        $picfeedback->dealid = $feedback->dealid;
        $picfeedback->couponid = $feedback->couponid;
        $picfeedback->insert();
        $transaction->commit();
    }

    public function actionCheck()
    {
        $list = [];
        $controller = PictureCheckController::getInstance(PictureCheckController::TYPE_PICFEEDBACK_CHE);
        $request = Yii::$app->request;
        if ($request->isPost) {
            $check = $request->post('pic', []);
            foreach ($check as $id => &$status) {
                $status = $status == 'pass' ? PictureCheckController::STATUS_PASS : PictureCheckController::STATUS_BAN; 
            }
            $eff['starttime'] = $request->post('starttime', 0);
            if ($eff['starttime'] == 0) {
                $controller->multiSetStatus(1, $check);
            }
            $eff['endtime'] = time();
            $controller->multiSetStatus(1, $check, $eff);
        }
        $list = $controller->getListForCheck();
        return $this->render('check', [
                'list'  => $list,
            ]);
    }

    public function actionReview()
    {
        $list = [];
        $controller = PictureCheckController::getInstance(PictureCheckController::TYPE_PICFEEDBACK_REV);
        $request = Yii::$app->request;
        if ($request->isPost) {
            $check = $request->post('pic');
            foreach ($check as $id => &$status) {
                $status = $status == 'pass' ? PictureCheckController::STATUS_PASS : PictureCheckController::STATUS_BAN; 
            }
            $controller->multiSetStatus(1, $check);
        }
        $list = $controller->getListForCheck();
        return $this->render('review', [
                'list'  => $list,
            ]);
    }

    public function actionConfirm()
    {
        $list = [];
        $controller = PictureCheckController::getInstance(PictureCheckController::TYPE_PICFEEDBACK_CON);
        $request = Yii::$app->request;
        if ($request->isPost) {
            $check = $request->post('pic');
            foreach ($check as $id => &$status) {
                $status = $status == 'pass' ? PictureCheckController::STATUS_PASS : PictureCheckController::STATUS_BAN; 
            }
            $controller->multiSetStatus(1, $check);
        }
        $list = $controller->getListForCheck();
        return $this->render('confirm', [
                'list'  => $list,
            ]);
    }

    public function actionHis()
    {
        $userlist = [
            1   => '张茂强',
            2   => '路人甲',
            3   => '匪兵乙',
        ];
        $list = [];
        $checkperson = Yii::$app->request->get('checkperson', 0);

        $query = Picfeedbackcheck::find();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->setPageSize(10);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        foreach ($models as $model) {
            $feedback = Picfeedback::findOne($model->picfeedbackid);
            if (empty($feedback)) {
                continue;
            }
            $list[] =[
                'id'            => $model->picfeedbackid,
                'checktime'     => $model->checktime,
                'status'        => $model->status,
                'checkperson'   => $model->checkperson,
                'url'           => "/image/" . $feedback->imagename,
            ];
        }
        return $this->render('his', [
            'list'          => $list,
            'userlist'      => $userlist,
            'checkperson'   => $checkperson,
            'url'           => Yii::$app->request->url,
            'pages'         => $pages,
        ]);
    }

}
