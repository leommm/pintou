<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_shop_income".
 *
 * @property integer $id
 * @property integer $shop_id
 * @property integer $member_id
 * @property string $amount
 * @property integer $is_cash
 * @property string $create_time
 */
class ShopIncome extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_shop_income';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'member_id', 'amount'], 'required'],
            [['shop_id', 'member_id', 'is_cash'], 'integer'],
            [['amount'], 'number'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => '店铺id',
            'member_id' => '会员id',
            'amount' => '消费金额',
            'is_cash' => '是否提现',
            'create_time' => 'Create Time',
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function getShop() {
        return $this->hasOne(PintouShop::className(), ['id' => 'shop_id']);
    }

    public function getMember() {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);

    }
}
