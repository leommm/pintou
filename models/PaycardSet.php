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
class PaycardSet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%paycard_set}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['distance'], 'string'],

        ];

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'distance'        => '打卡距离',
        ];
    }
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id'=>'goods_id']);
    }

}
