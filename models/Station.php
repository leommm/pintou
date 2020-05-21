<?php

namespace app\models;

use Yii;
use Codeception\PHPUnit\ResultPrinter\HTML;

/**
 * This is the model class for table "{{%video}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $sort
 * @property integer $is_delete
 * @property integer $addtime
 * @property integer $store_id
 * @property string $pic_url
 * @property string $content
 * @property integer $type
 */
class Station extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%station}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'name' => '站点名',

        ];
    }
    public function beforeSave($insert)
    {
        $this->title = \yii\helpers\Html::encode($this->title);
        $this->content = \yii\helpers\Html::encode($this->content);
        return parent::beforeSave($insert);
    }
}
