<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "feedbackcomment".
 *
 * @property integer $id
 * @property string $comment
 * @property string $format
 * @property integer $modtime
 */
class Feedbackcomment extends \yii\db\ActiveRecord
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
            [['modtime'], 'integer'],
            [['comment', 'format'], 'string', 'max' => 512]
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
            'format' => 'Format',
            'modtime' => 'Modtime',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modtime'],
                ],
            ],
        ];
    }

    public function beforeInsert()
    {
    }
}
