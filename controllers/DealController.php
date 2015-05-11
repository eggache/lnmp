<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use app\models\Deal;
use app\models\Dealfeedback;
use app\models\Feedbackcomment;

class DealController extends Controller
{
    public function actionIndex()
    {
        $query = Deal::find();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionFeedback()
    {
        $dealid = Yii::$app->request->get('dealid', 0);
        $query = Dealfeedback::find()->where(['dealid' => $dealid])->orderBy('weight');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $commentList = [];
        foreach ($models as $model) {
            $commentList[$model->commentid] = Feedbackcomment::findOne($model->commentid);
        }
        return $this->render('feedback', [
            'commentList'   => $commentList,
            'models'        => $models,
            'pages'         => $pages,
        
        ]);
    }
}
