<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_setting".
 *
 * @property integer $id
 * @property string $first
 * @property string $second
 * @property string $third
 * @property string $general
 * @property integer $store_id
 * @property integer $level
 * @property integer $condition
 * @property integer $share_condition
 * @property string $content
 * @property integer $pay_type
 * @property string $min_money
 * @property string $agree
 * @property string $first_name
 * @property string $second_name
 * @property string $third_name
 * @property string $pic_url_1
 * @property string $pic_url_2
 * @property integer $price_type
 * @property integer $bank
 * @property integer $remaining_sum
 * @property string $rebate
 * @property integer $is_rebate
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first', 'second', 'third', 'general', 'min_money', 'rebate'], 'number'],
            [['store_id', 'level', 'condition', 'share_condition', 'pay_type', 'price_type', 'bank', 'remaining_sum', 'is_rebate'], 'integer'],
            [['content', 'agree', 'pic_url_1', 'pic_url_2'], 'string'],
            [['first_name', 'second_name', 'third_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first' => 'First',
            'second' => 'Second',
            'third' => 'Third',
            'general' => 'General',
            'store_id' => 'Store ID',
            'level' => 'Level',
            'condition' => 'Condition',
            'share_condition' => 'Share Condition',
            'content' => 'Content',
            'pay_type' => 'Pay Type',
            'min_money' => 'Min Money',
            'agree' => 'Agree',
            'first_name' => 'First Name',
            'second_name' => 'Second Name',
            'third_name' => 'Third Name',
            'pic_url_1' => 'Pic Url 1',
            'pic_url_2' => 'Pic Url 2',
            'price_type' => 'Price Type',
            'bank' => 'Bank',
            'remaining_sum' => 'Remaining Sum',
            'rebate' => 'Rebate',
            'is_rebate' => 'Is Rebate',
        ];
    }
}
