<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pt_order_detail}}".
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
 */
class BrOrderDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%br_order_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'attr', 'pic'], 'required'],
            [['order_id', 'goods_id', 'num', 'addtime', 'is_delete', 'mch_id'], 'integer'],
            [['total_price'], 'number'],
            [['attr'], 'string'],
            [['pic'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'goods_id' => 'Goods ID',
            'num' => '商品数量',
            'total_price' => '此商品的总价',
            'addtime' => 'Addtime',
            'is_delete' => 'Is Delete',
            'attr' => '商品规格',
            'pic' => '商品规格图片',
			'mch_id' => '商户id'
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(BrGoods::className(), ['id'=>'goods_id']);
    }

    public function getShare()
    {
        return $this->hasOne(GoodsShare::className(), ['goods_id'=>'goods_id'])->where(['type'=>0]);
    }
}
