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
class Screen extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%screen}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_count','savetime','is_show'], 'integer'],
        ];

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_count'        => '订单金额',
            'savetime'      => '保存时间',
            'is_show' => '展示',
        ];
    }


}
