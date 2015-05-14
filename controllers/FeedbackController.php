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
use app\models\Fbcheckstat;
use app\models\Fbcheckeff;
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
            $ret = DealFeedbackController::commentStyle($comment);
            if ($ret) {
                var_dump("评价文字含有违规词语");
                return;
            }
//            $ret = DealFeedbackController::recentComment($comment);
            if ($ret) {
                var_dump("重复评价禁止提交");
                return;
            }
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
            $check = $request->post('check', []);
            foreach ($check as $id => &$status) {
                $status = $status == 'pass' ? TextCheckController::STATUS_PASS : TextCheckController::STATUS_BAN; 
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
        $request = Yii::$app->request;
        $starttime = $request->get('begintime', time());
        $endtime = $request->get('endtime', time());
        $start = date('YmdH', strtotime($starttime));
        $end = date('YmdH', strtotime($endtime));
        $cond = ['between', 'hour', $start, $end];
        $arr = Fbcheckstat::find()->where($cond)->where(['type' => [1, 11], 'checkperson' => 1])->all();
        $userlist = [
            1   => '张茂强',
            2   => '路人甲',
            3   => '匪兵乙',
        ];
        $checkperson = Yii::$app->request->get('checkperson', 0);
        $list = [];
        foreach ($arr as $obj) {
            $checkperson = $obj->checkperson;
            if (!isset($list[$checkperson])) {
                $list[$checkperson]['textcnt'] = 0;
                $list[$checkperson]['textban'] = 0;
                $list[$checkperson]['piccnt'] = 0;
                $list[$checkperson]['picban'] = 0;
            }
            $list[$checkperson]['checkperson'] = $userlist[$checkperson];
            if ($obj->type == 11) {
                $list[$checkperson]['textcnt'] += $obj->totalcnt;
                $list[$checkperson]['textban'] += $obj->totalcnt - $obj->passcnt;
            } else {
                $list[$checkperson]['piccnt'] += $obj->totalcnt;
                $list[$checkperson]['picban'] += $obj->totalcnt - $obj->passcnt;
            }
        }
        return $this->render('stat', [
            'list'          => $list,
            'checkperson'   => $checkperson,
            'userlist'      => $userlist,
            'url'           => Yii::$app->request->url,
        ]); 
    }

    public function actionEff()
    {
        $request = Yii::$app->request;
        $starttime = $request->get('begintime', time());
        $endtime = $request->get('endtime', time());
        $start = date('YmdH', strtotime($starttime));
        $end = date('YmdH', strtotime($endtime));
        $cond = ['between', 'hour', $start, $end];
        $arr = Fbcheckeff::find()->where($cond)->where(['type' => [1, 11], 'checkperson' => 1])->all();
        $userlist = [
            1   => '张茂强',
            2   => '路人甲',
            3   => '匪兵乙',
        ];
        $checkperson = Yii::$app->request->get('checkperson', 0);
        $list = [];
        foreach ($arr as $obj) {
            $checkperson = $obj->checkperson;
            if (!isset($list[$checkperson])) {
                $list[$checkperson]['textcnt'] = 0;
                $list[$checkperson]['texttime'] = 0;
                $list[$checkperson]['piccnt'] = 0;
                $list[$checkperson]['pictime'] = 0;
            }
            $list[$checkperson]['checkperson'] = $userlist[$checkperson];
            if ($obj->type == 11) {
                $list[$checkperson]['textcnt'] += $obj->cnt;
                $list[$checkperson]['texttime'] += $obj->usetime;
            } else {
                $list[$checkperson]['piccnt'] += $obj->cnt;
                $list[$checkperson]['pictime'] += $obj->usetime;
            }
        }
        return $this->render('eff', [
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
