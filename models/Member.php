<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_member".
 *
 * @property Member $parent
 * @property integer $id
 * @property integer $user_id
 * @property integer $parent_id
 * @property string $real_name
 * @property integer $phone
 * @property string $password
 * @property integer $sex
 * @property integer $role
 * @property string $id_card
 * @property string $bank_card
 * @property string $area
 * @property integer $p_id
 * @property integer $c_id
 * @property integer $d_id
 * @property string $create_time
 * @property string $account_a
 * @property string $account_b
 * @property string $account_c
 * @property integer $is_partner
 * @property string $share_img
 * @property string $pay_code

 */
class Member extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'parent_id', 'sex', 'role', 'p_id', 'c_id', 'd_id', 'is_partner','is_delete','is_active'], 'integer'],
            [['phone','real_name','id_card', 'bank_card',], 'required'],
            [['create_time','active_time'], 'safe'],
            [['account_a', 'account_b', 'account_c'], 'number'],
            [['real_name', 'password', 'id_card', 'bank_card', 'area', 'share_img','pay_code'], 'string', 'max' => 255],
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
            'parent_id' => '上级id',
            'real_name' => '真实姓名',
            'phone' => '手机号码',
            'password' => '登录密码',
            'sex' => '性别 0：位置、1：男、2：女',
            'role' => '成员角色 1：认证会员、2：投资保姆、3：经纪人',
            'id_card' => '身份证号',
            'bank_card' => '银行卡号',
            'area' => '所属区域',
            'p_id' => '省份id',
            'c_id' => '地级市id',
            'd_id' => '区县id',
            'create_time' => '创建时间',
            'account_a' => 'A账户金额（返利到银行卡）',
            'account_b' => 'B账户金额（可消费）',
            'account_c' => 'C账户金额（转介绍佣金）',
            'is_partner' => '是否为城市合伙人（特殊身份）',
            'share_img' => '分享图片地址',
            'is_active' => '是否认证'
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function getParent() {
        return $this->hasOne(Member::className(), ['id' => 'parent_id']);
    }
}
