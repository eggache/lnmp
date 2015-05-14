<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Dealfeedback;

class DealFeedbackController extends Controller
{
    public static function commentCheck($comment)
    {
        $banwords = TrieController::$banwords;
        foreach ($banwords as $banword) {
            
        }
    }

	public static function commentStyle($comment)
	{
        // 检查用户输入评价内容是否含有关键词
        $banwords = require('../config/banwords.php');
        foreach ($banwords as $banword) {
            if (mb_stripos($comment, $banword, 0, 'utf-8') !== false) {
                return true;
            }
        }

        $pattern = '/([a-zA-Z0-9]+\.)+([a-zA-Z0-9])+/u';
        if (preg_match($pattern, $comment)) {
            return true;
        }
        
        // 连续标点符号多于等于6个，算凑字数
        $pattern = '/[\pP]{6,}/u';
        if (preg_match($pattern, $comment)) {
            return true;
        }
        
        // 连续英文字符多于等于15个，算凑字数
        $pattern = '/[a-zA-Z\d]{15,}/u';
        if (preg_match($pattern, $comment)) {
            return true;
        }
        
        //去重后中文字符占总中文字符1/3以下
        $commentClean = preg_replace('/[\pPa-zA-Z0-9\s]+/u', '', $comment);
        preg_match_all("/./u", $commentClean, $commentArr);
        if (mb_strlen($commentClean, 'utf-8') > 3 * count(array_flip($commentArr[0]))) {
            return true;
        }
        
        return false;
    }

    public static function recentComment($comment)
    {
        $feedback = Dealfeedback::find()->orderBy('addtime', 'desc')->limit(2)->all();
        $comments = [];
        foreach ($feedback as $fb) {
            $ret = self::commentLCS($comment, $fb->getComment());
            if ($ret) {
                return true;
            }
        }
        return false;
    }

    public static function commentLCS($comment, $hiscmt)
    {
        $clen = mb_strlen($comment, 'utf-8');
        $hlen = mb_strlen($hiscmt, 'utf-8');
        $value = [];
        for ($i = 0; $i < $clen; $i ++ ) {
            $c_char = mb_substr($comment, $i, 1, 'utf-8');
            for ($j = 0; $j < $hlen; $j ++) {
                $h_char = mb_substr($hiscmt, $j, 1, 'utf-8');
                $add = $c_char === $h_char ? 1 : 0;
                $up = isset($value[$i-1][$j]) ? intval($value[$i-1][$j]) : 0;
                $left = isset($value[$i][$j-1]) ? intval($value[$i][$j-1]) : 0;
                $up_left = isset($value[$i-1][$j-1]) ? intval($value[$i-1][$j-1]) : 0;
                $max = max($up, $left);
                $max = max($max, $up_left + $add);
                $value[$i][$j] = $max;
            }
        }
        return $value[$clen-1][$hlen-1]/$clen > 0.15;
    }
	
    public static function computeFeedbackWeight($userid, $couponid, $dealid, $comment, $score, $piccount = 0)
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
