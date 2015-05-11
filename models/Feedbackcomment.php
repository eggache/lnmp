<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "feedbackcomment".
 *
 * @property string $id
 * @property string $comment
 * @property string $apdcomment
 */
class Feedbackcomment extends ActiveRecord
{

    public function __construct () {
        $this->on('afterInsert', [$this, 'beforeInsert']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbackcomment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment', 'apdcomment'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment' => 'Comment',
            'apdcomment' => 'Apdcomment',
        ];
    }

    public function behaviors()
    {
        return [
        ];
    }

    public function beforeInsert()
    {
    }

    
}
