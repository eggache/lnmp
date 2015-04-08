<?php
namespace app\models;

use yii\base\Model;

class PicForm extends Model
{
    public $pic;

    public function rules()
    {
        return [
            [['pic'], 'file'],
        ];
    }
}
