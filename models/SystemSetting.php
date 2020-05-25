<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_system_setting".
 *
 * @property integer $id
 * @property string $first_parking
 * @property string $first_flats
 * @property string $first_shop
 * @property string $second
 * @property string $city_commission
 * @property string $nanny_commission
 * @property string $account_a_1
 * @property string $account_a_2
 * @property string $account_a_3
 * @property string $account_b_1
 * @property string $account_b_2
 * @property string $account_b_3
 * @property integer $condition
 * @property string $index_ad
 */
class SystemSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_system_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_parking', 'first_flats', 'first_shop', 'second', 'city_commission','nanny_commission', 'account_a_1', 'account_a_2', 'account_a_3', 'account_b_1', 'account_b_2', 'account_b_3'], 'number'],
            [['condition'], 'integer'],
            [['index_ad'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_parking' => '一级车位佣金',
            'first_flats' => '一级公寓佣金',
            'first_shop' => '一级商铺佣金',
            'second' => '二级佣金',
            'nanny_commission' => '保姆佣金',
            'city_commission' => '城市级佣金',
            'account_a_1' => 'A账户1-3年返利',
            'account_a_2' => 'A账户4-6年返利',
            'account_a_3' => 'A账户7-10年返利',
            'account_b_1' => 'B账户1-3年返利',
            'account_b_2' => 'B账户4-6年返利',
            'account_b_3' => 'A账户7-10年返利',
            'condition' => '注册会员是否需审核 0否1是',
            'index_ad' => '首页广告位',
        ];
    }
}
