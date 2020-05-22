<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_pintou_shop".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $real_name
 * @property string $phone
 * @property string $wechat
 * @property string $password
 * @property string $shop_name
 * @property string $shop_type
 * @property string $shop_address
 * @property string $id_card
 * @property string $bank_card
 * @property string $license
 * @property string $total_income
 * @property string $collection_code
 * @property integer $is_delete
 * @property string $create_time
 * @property integer $is_active
 */
class PintouShop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_pintou_shop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['real_name'], 'required'],
            [['is_delete', 'is_active','user_id'], 'integer'],
            [['total_income'], 'number'],
            [['create_time'], 'safe'],
            [['real_name', 'wechat', 'password', 'shop_name', 'shop_type', 'shop_address', 'id_card', 'bank_card', 'license', 'collection_code'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'real_name' => 'Real Name',
            'phone' => 'Phone',
            'wechat' => 'Wechat',
            'password' => 'Password',
            'shop_name' => '店铺名称',
            'shop_type' => '店铺类型',
            'shop_address' => '详细地址',
            'id_card' => '身份证',
            'bank_card' => '银行卡',
            'license' => '营业执照',
            'total_income' => '累计收入',
            'collection_code' => '收款码',
            'is_delete' => 'Is Delete',
            'create_time' => 'Create Time',
            'is_active' => '是否激活',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }
}
