<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;
use app\models\Feedbackcomment;
use app\models\Feedbackcheck;
use app\models\ToCheck;
use app\models\Coupon;
use app\models\Deal;
use app\controllers\PicfeedbackController;

class FeedbackController extends Controller
{
    public function actionFeedback()
    {
        $request = Yii::$app->request;
        $feedback = new FeedbackForm;
        $picform = new PicForm;
        if ($request->isPost) {
            $feedback = $request->post()['FeedbackForm'];
            $comment = $feedback['comment'];
            $feedbackid = Dealfeedback::add($feedback);
            $picids = PicfeedbackController::storeInRedis($feedbackid);
            return $this->redirect('/tofeedback');
        } else {
            $couponid = $request->get('couponid', 0);
            $dealid = $request->get('dealid', 0);
            $userid = $request->get('userid', 0);
            return $this->render('feedback', [
                'feedback'  => $feedback,
                'picform'   => $picform,
                'couponid'  => $couponid,
                'dealid'    => $dealid,
                'userid'    => $userid,
            ]);
        }
    }

    public function actionList()
    {
        $dealid = Yii::$app->request->get('dealid', 1);
        $deal = Deal::find()->where(['id' => $dealid])->one();
        $title = $deal['dealtitle'];
        $coupons = Coupon::find()->where(['dealid' => $dealid])->all();
        var_dump(count($coupons));exit;
        $list = [];
        foreach ($coupons as $coupon) {
            $feedback = Dealfeedback::find()
                ->where(['couponid' => $coupon['id']])
                ->one();
            $list[] = [
                'dealid'    => $dealid,
                'couponid'  => $coupon['id'],
                'userid'    => $coupon['userid'],
                'comment'   => $feedback->getComment(),
            ];
        }

        return $this->render('list', [
                'list'  => $list,
                'title' => $title,
            ]);
    }

    public function actionDefault()
    {
        return $this->render('default');
    }

    public function actionCheck()
    {
        $list = [];
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_CHECK);
        $request = Yii::$app->request;
        if ($request->isPost) {
            $check = $request->post('check');
            foreach ($check as $id => &$status) {
                $status = $status == 'pass' ? TextCheckController::STATUS_PASS : TextCheckController::STATUS_BAN; 
            }
            $controller->multiSetStatus(1, $check);
        }
        $list = $controller->getListForCheck();
        return $this->render('check', [
                'list'  => $list,
            ]);
    }

    public function actionReview()
    {
        $list = [];
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_REVIEW);
        $request = Yii::$app->request;
        if ($request->isPost) {
            $check = $request->post('check');
            foreach ($check as $id => &$status) {
                $status = $status == 'pass' ? TextCheckController::STATUS_PASS : TextCheckController::STATUS_BAN; 
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
        $controller = TextCheckController::getInstance(TextCheckController::TYPE_DEALFEEDBACK_CONFIRM);
        $request = Yii::$app->request;
        if ($request->isPost) {
            $check = $request->post('check');
            foreach ($check as $id => &$status) {
                $status = $status == 'pass' ? TextCheckController::STATUS_PASS : TextCheckController::STATUS_BAN; 
            }
            $controller->multiSetStatus(1, $check);
        }
        $list = $controller->getListForCheck();
        return $this->render('confirm', [
                'list'  => $list,
            ]);
    }

    public function actionStat()
    {
        $list = [];
        for($i = 0; $i < 10; $i ++) {
            $list[] = [
                'checkperson'   => '张茂强',
                'textcnt'   => $cnt = rand(1,100),
                'textban'   => rand(1,$cnt/10),
                'piccnt'    => $cnt = rand(1,500),
                'picban'    => rand(1, $cnt/10),
            ];
        }
        $userlist = [
            1   => '张茂强',
            2   => '路人甲',
            3   => '匪兵乙',
        ];
        $checkperson = Yii::$app->request->get('checkperson', 0);
        return $this->render('stat', [
            'list'          => $list,
            'checkperson'   => $checkperson,
            'userlist'      => $userlist,
            'url'           => Yii::$app->request->url,
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

        $query = Feedbackcheck::find();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        foreach ($models as $model) {
            $feedback = Dealfeedback::findOne($model->dealfeedbackid);
            if (empty($feedback)) {
                continue;
            }
            $list[] =[
                'id'            => $model->dealfeedbackid,
                'score'         => $feedback->score,
                'comment'       => $feedback->getComment(),
                'checktime'     => $model->checktime,
                'status'        => $model->status,
                'checkperson'   => $model->checkperson,
            ];
        }
        return $this->render('his', [
            'list'          => $list,
            'userlist'      => $userlist,
            'checkperson'   => $checkperson,
            'url'           => Yii::$app->request->url,
            'pages' => $pages,
        ]);
    }

}   
