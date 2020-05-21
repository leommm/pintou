<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_commission_log".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $member_id
 * @property integer $intention_id
 * @property string $amount
 * @property integer $is_settle
 * @property string $create_time
 * @property integer $is_delete
 */
class CommissionLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_commission_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'member_id', 'intention_id'], 'required'],
            [['type', 'member_id', 'intention_id', 'is_settle', 'is_delete'], 'integer'],
            [['amount'], 'number'],
            [['create_time','settle_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型:1-返现到银行卡、2-返现到可消费、3-一级转介绍、4-二级转介绍、5-城市佣金、6-保姆佣金',
            'member_id' => '会员id',
            'intention_id' => '意向id',
            'amount' => '金额',
            'is_settle' => '是否结算 0否、1是',
            'create_time' => 'Create Time',
            'is_delete' => 'Is Delete',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function getMember() {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getIntention() {
        return $this->hasOne(ProjectIntention::className(), ['id' => 'intention_id']);
    }
}
