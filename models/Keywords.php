<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "keywords".
 *
 * @property integer $id
 * @property string $raw
 * @property string $clean
 * @property integer $category
 * @property integer $type
 * @property integer $status
 * @property integer $modtime
 * @property integer $userid
 */
class Keywords extends \yii\db\ActiveRecord
{
    /** 类型: 必杀词 */
    const TYPE_BISHA = 0;
    /** 类型: 先审后发词 */
    const TYPE_XIANSHENHOUFA = 1;
    /** 类型: 先发后审词 */
    const TYPE_XIANFAHOUSHEN = 2;
    /** 类型: 脏话 */
    const TYPE_ZANGHUA = 3;
    /** 类型: 基础词库屏蔽词,敏感词检测时忽略基础词库中此type的敏感词 */
    const TYPE_IGNORE = 4;

    //基础词库
    const CATEGORY_BASE = 0;
    //涉黄补充词库
    const CATEGORY_SEX = 1;
    //猫眼补充词库
    const CATEGORY_CATEYE = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'keywords';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'type', 'status', 'modtime', 'userid'], 'integer'],
            [['raw', 'clean'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'raw' => 'Raw',
            'clean' => 'Clean',
            'category' => 'Category',
            'type' => 'Type',
            'status' => 'Status',
            'modtime' => 'Modtime',
            'userid' => 'Userid',
        ];
    }
}
