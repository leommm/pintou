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
class GoodsVisitLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_visit_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        
            [['user_id','addtime','goods_id'], 'integer'],
        ];

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
           
            'user_id'      => '用户id',
            'goods_id' => '商品id',
            'addtime'     => '添加时间',
        ];
    }
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id'=>'goods_id']);
    }

}
