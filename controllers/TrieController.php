<?php
namespace app\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use app\models\Keywords;
class TrieController extends Controller
{
    public static $instances;
    public static $banwords = ['http', '巨乳', '一夜欢', '昆明事件', '昆明的勇者', '十五字', '15字', '十五个字', '十五個字', '15个字', '15個字', '凑字', '字数'];
    private $wordMap = ['root' => 0];
    private $trie;
    private $dict;
    private $check;
    private $base;

    public static $dictConfig = [
        Keywords::TYPE_BISHA,
        Keywords::TYPE_XIANSHENHOUFA,
        Keywords::TYPE_XIANFAHOUSHEN,
        Keywords::TYPE_ZANGHUA,
        Keywords::TYPE_IGNORE,
    ];

    public function getInstance($dict)
    {
        if (!isset(self::$dictConfig[$dict])) {
            throw new Exception("Don`t have this dictionary");
        }
        if (!isset(self::$instances[$dict])) {
            self::$instances[$dict] = new self($dict);
        }
        return self::$instances[$dict];
    }

    public function __construct($dict)
    {
        $keywords = Keywords::find()->where(['type' => self::$dictConfig[$dict]])->limit(10000)->all();
        foreach ($keywords as &$keyword) {
            $keyword = $keyword['raw'];
        }
        $this->splitWords($keywords);
        $this->createDobuleArray($keywords);
        $this->dict = $dict;
    }

    public function updateDict()
    {

    }

    public function createTrie($words)
    {
        $cnt = 0;
        $trie['root']['index'] = 0;
        foreach ($words as $word) {
            $node = &$trie['root'];
            $wlen = mb_strlen($word, 'utf-8') ;
            for ($i = 0; $i < $wlen; ++$i) {
                $char = mb_substr($word, $i, 1, 'utf-8');
                if ($i == $wlen-1) {
                    $node[$char]['word'] = $word;
                    $node[$char]['index'] = isset($node[$char]['index']) ? $node[$char]['index'] : ++ $cnt;
                } else {
                    $node[$char]['index'] = isset($node[$char]['index']) ? $node[$char]['index'] : ++ $cnt;
                    $node = &$node[$char];
                }
            }
        }
        return $trie;
    }

    public function findWord($text)
    {
    
    }

    public function createDobuleArray($words)
    {
        $trie = $this->createTrie($words);
        $basePos = [ 0 => 0];
        $queue = [];
        array_push($queue, $trie);
        while($queue != []) {
            $front = array_shift($queue);
            $key = array_keys($front);
            if (empty($key) || $key[0] == 'word') {
                continue;
            }
            $key = $key[0];
            $value = $front[$key];
            $keyIndex = $value['index'];
            unset($value['index']);
            $index = [];
            $flag = 0;
            foreach ($value as $word => $node) {
                if ($word == 'word') {
                    $flag = 1;
                    continue;
                }
                if ($word == 'index') {
                    continue;
                }
                $index[$node['index']] = $this->wordMap[$word];
                array_push($queue, [ $word => $node ]);
                unset($node);
            }
            $p = $basePos[$keyIndex];
            for ($i = 0 | $flag; ; ++ $i) {
                $ret = true;
                foreach ($index as $j) {
                    $cnt = $i + $j;
                    if (isset($check[$cnt]) || isset($base[$cnt])) {
                        $ret = false;
                        break;
                    }
                }
                if ($ret == true) {
                    $base[$p] = $flag ? -$i : $i;
                    $flag && $this->trie[$p] = isset($value['word']) ? $value['word'] : '';
                    foreach ($index as $k => $j) {
                        $check[$i + $j] = $p;
                        $basePos[$k] = $i + $j;
                    }
                    break;
                }
            }
        }
        ksort($check);
        ksort($base);
        $this->check = $check;
        $this->base = $base;
        unset($check);
        unset($base);
    }

    public function clearSpecialChars($text)
    {
        //保留中文数字字母，排除标点和其他
        $pattern = '/([^\x{4e00}-\x{9fa5}a-zA-Z\d]|[\pP])/u';
        return strtolower(preg_replace($pattern, '', $text));
    }

    public function splitWords($words)
    {
        $cnt = 1;
        foreach ($words as $word) {
            //$word = $this->clearSpecialChars($word);
            $len = mb_strlen($word, 'utf-8');
            for ($i = 0; $i < $len; ++ $i) {
                $char = mb_substr($word, $i, 1, 'utf-8');
                $this->wordMap[$char] = isset($this->wordMap[$char]) ? $this->wordMap[$char] : $cnt ++;
            }
        }
    }

    public function searchKeyWords($text)
    {
        $ret = [];
        $pos = 0;
        $len = mb_strlen($text, 'utf-8');
        for ($i = 0; $i < $len; $i ++) {
            $char = mb_substr($text, $i, 1, 'utf-8');
            if (!isset($this->wordMap[$char])) {
                break;
            }
            $index = $this->wordMap[$char];
            $pos = abs($this->base[$pos]) + $index;
            if (!isset($this->base[$pos])) {
                break;
            }
            if ($this->base[$pos] < 0) {
                if ($this->trie[$pos] != mb_substr($text, 0, $i+1, 'utf-8')) {
                    break;
                }
                $ret[] = $this->trie[$pos];
            }
        }
        return $ret;
    }

    public function search($text)
    {
        $text = $this->clearSpecialChars($text);
        $matches = [];
        $len = mb_strlen($text, 'utf-8');
        for ($i = 0; $i < $len; $i ++) {
            $substr = mb_substr($text, $i, $len - $i, 'utf-8');
            $ret = $this->searchKeyWords($substr);
            $matches = array_merge($ret, $matches);
        }
        return $matches;
    }


}
