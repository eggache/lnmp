<?php
namespace app\models;

use yii\redis\ActiveRecord;

class PicRedis extends ActiveRecord
{
    public function attributes()
    {
        return ['id', 'file'];
    }

}
