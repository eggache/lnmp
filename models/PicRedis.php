<?php
namespace app\models;

use yii\redis\ActiveRecord;

class PicRedis extends ActiveRecord
{
    public function attributes()
    {
        return ['id', 'file'];
    }

    public static function add($name, $file)
    {
        $obj = new self;
        $obj->id = $name;
        $obj->file = $file;
        $obj->save();
    }

}
