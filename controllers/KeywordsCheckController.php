<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Keywords;
class KeywordsCheckController extends Controller
{
    public static function getKeyWords($type)
    {
        $keywords = Keywords::find()->where(['type' => $type])->all();
        foreach($keywords as $keyword) {
            $retKeywords[] = $keyword->raw;
        }
        return $retKeywords;
    }

    public static function hasKeyWords($type, $comment)
    {
        $comment = self::cleanSpecialChars($comment);
        $keywords = self::getKeyWords($type);
        $arr = [];
        foreach ($keywords as $dirty) {
            if (($pos = mb_stripos($comment, $dirty, 0, 'utf-8')) !== false) {
                $arr[$pos] = $dirty;
            }
        }
        ksort($arr);
        return $arr;
    }

    public static function cleanSpecialChars($text)
    {
        // 保留中文数字字母，排除标点和其他
        $pattern = '/([^\x{4e00}-\x{9fa5}a-zA-Z\d]|[\pP])/u';
        return strtolower(preg_replace($pattern, '', $text));
    }

}
