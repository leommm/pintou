<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%order_detail}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $goods_id
 * @property integer $num
 * @property string $total_price
 * @property integer $addtime
 * @property integer $is_delete
 * @property string $attr
 * @property string $pic
 * @property string $integral
 */
class Publish extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%publish}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'default'],
            [['title', 'images','image','type'], 'string'],
            [['title', 'content'], 'trim'],
            [['create_time','type','state'], 'integer'],
        ];

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title'        => '标题',
            'image'      => '图片',
            'content' => '简介',
            'type'     => '类型',
        ];
    }
public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id'=>'goods_id']);
    }

}
