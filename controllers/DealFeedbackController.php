<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class DealFeedbackController extends Controller
{
    public static function computeFeedbackWeight($userid, $orderid, $dealid, $poiid, $comment, $score, $piccount = 0)
    {
        // orderid 为0表示的是霸王餐项目的评分信息
        //if ($orderid == 0) {
        //    $addtime = time();
        //} else {
        //    $addtime = self::getMinAddTime($userid, $orderid);
        //}
        //if (!$addtime) {
        //    $addtime = time();
        //}
        /** 计算评价的默认排序评分*/
        // 评价次数与权重
        //$fbtimes = self::getFbtimesByUserID($userid);
        $fbtimes = 2;
        $addtime = time();
        if ($piccount === 0) {
            ++$fbtimes;
        }
        $ftWeight = self::getFTWeight($fbtimes);
        // 评价字数与权重
        $wcWeight = self::getWCWeight($comment);
        $picWeight = self::getPicWeight($piccount);
        $scoreWeight = self::getScoreWeight($score);
        // 评价的添加时间与项目的开始时间天数差
        //$deal = DealModel::getFromPool($dealid);
        //$poiBeginTime = $poiid ? self::getPoiFirstFeedbackTime($poiid) - 86400 : $deal->begintime;
        //// deal评价半衰期 70天, poi评价半衰期半年,因为poi存活时间>>deal,相同半衰期权重数值溢出
        //$beginTimes = [[1.01, $deal->begintime], [1.004, $poiBeginTime]];
        $begintime = 1420041600;
        $beginTimes = [[1.01, $begintime], [1.004, $begintime]];
        $ret = [];
        foreach ($beginTimes as $values) {
            list($base, $begintime) = $values;
            $distance = max(round(($addtime - $begintime) / 86400, 2), 1);
            $timeWeight = round(pow($base, $distance), 2);
            $ret[] = ($ftWeight + $wcWeight + $picWeight + $scoreWeight) * $timeWeight * 100;
        }
        return $ret;
    }

    //评价次数权重
    public static function getFTWeight($feedtimes)
    {
        if ($feedtimes == 0) {
        	return 0;
        } elseif ($feedtimes >= 1 && $feedtimes <= 2) {
        	return 1;
        } elseif ($feedtimes >= 3 && $feedtimes <= 5) {
        	return 2;
        } elseif ($feedtimes >= 6 && $feedtimes <= 15) {
        	return 3;
        } elseif ($feedtimes >= 16 && $feedtimes <= 30) {
        	return 4;
        } elseif ($feedtimes >= 31 && $feedtimes <= 50) {
        	return 5;
        } elseif ($feedtimes >= 51 && $feedtimes <= 250) {
        	return 6;
        } else {
        	return 7;
        }
    }

    //评价字数权重
    public static function getWCWeight($comment)
    {
        $pattern = '/[\x{4e00}-\x{9fa5}]+/u';
        $match = [];
        preg_match_all($pattern, $comment, $match);
        $zhChars = [];
        if ($match) {
            foreach ($match[0] as $value) {
                $subZhChars = preg_split('/(?<!^)(?!$)/u', $value);
                foreach ($subZhChars as $zhChar) {
                    $zhChars[$zhChar] = 1;
                }
            }
        }
        $wordscount = count($zhChars);
        if ($wordscount <= 10) {
        	return 0;
        } elseif ($wordscount > 10 && $wordscount < 20) {
        	return 1;
        } elseif ($wordscount >= 20 && $wordscount < 28) {
        	return 2;
        } elseif ($wordscount >= 28 && $wordscount < 36) {
        	return 3;
        } elseif ($wordscount >= 36 && $wordscount < 43) {
        	return 4;
        } elseif ($wordscount >= 43 && $wordscount < 48) {
        	return 5;
        } elseif ($wordscount >= 49 && $wordscount < 61) {
        	return 6;
        } else {
        	return 7;
        }
    }

    public static function getPicWeight($piccount)
    {
        if ($piccount <= 0) {
        	return 0;
        } elseif ($piccount === 1) {
        	return 2;
        } elseif ($piccount === 2) {
        	return 4;
        } elseif ($piccount === 3 || $piccount === 4) {
        	return 5;
        } else {
        	return 7;
        }
    }

    public static function getScoreWeight($score)
    {
        $score = round($score / 10);
        if ($score < 1 || $score > 5) {
        	return 0;
        }
        if ($score < 4) {
        	return $score + 1;
        }
        if ($score === 4) {
        	return 4.5;
        }
        return 4.75;
    }
}
