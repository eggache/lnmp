<?php
namespace app\commands;

use yii\console\Controller;
use app\controllers\TextCheckController;

class TestController extends Controller
{
    public function actionIndex()
    {
        $compositeWand = NewMagickWand();
        $watermarkimage = "/usr/share/nginx/html/lnmp/web/watermark.jpg";
        $ret = file_exists($watermarkimage);
        if (!MagickReadImage($compositeWand, $watermarkimage)) {
            var_dump($ret ,$watermarkimage);exit;
            MagickDeconstructImages($compositeWand);
            return $sourceWand;
        }
        var_dump("image is ok");exit;
    }
}
