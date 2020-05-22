<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_member_apply".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $parent_id
 * @property integer $type
 * @property string $real_name
 * @property string $phone
 * @property string $bank_card
 * @property string $id_card
 * @property integer $status
 * @property string $shop_name
 * @property string $shop_address
 * @property string $license
 * @property string $create_time
 * @property int $is_delete

 */
class MemberApply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_member_apply';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'status','is_delete','parent_id'], 'integer'],
            [['type', 'real_name', 'phone'], 'required'],
            [['create_time'], 'safe'],
            [['real_name', 'phone', 'id_card', 'shop_name', 'shop_address', 'license'], 'string', 'max' => 255],
            [['bank_card'], 'string', 'max' => 48],
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
            'type' => '申请类型 1：认证会员、3：经纪人、5、商户',
            'real_name' => '真实姓名',
            'phone' => '手机号',
            'bank_card' => '银行卡号',
            'id_card' => '身份证号',
            'status' => '审核状态 0：未审核、1：通过、2：拒绝',
            'shop_name' => '店铺名称',
            'shop_address' => '店铺地址',
            'license' => '营业执照',
            'create_time' => '申请时间',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
