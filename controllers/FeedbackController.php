<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PicForm;
use app\models\FeedbackForm;
use app\models\Dealfeedback;
use app\models\Feedbackcomment;
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
            var_dump("insert is ok");exit;
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
        $feedback = Dealfeedback::find()->limit(10)->all();
        $list = [];
        foreach ($feedback as $fb) {
            $list[] = [
                'id'        => $fb->id,
                'title'     => Deal::findOne($fb->dealid)->dealtitle,
                'score'     => $fb->score,
                'addtime'   => $fb->addtime,
                'comment'   => Feedbackcomment::findOne($fb->commentid)->comment,
                'keyword'   => '',
            ];
        }
        return $this->render('check', [
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
        $list = [];
        return $this->render('his', [
                'list'  => $list,
            ]);
    }

}   
