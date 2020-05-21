<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_member_income".
 *
 * @property integer $id
 * @property integer $intention_id
 * @property integer $member_id
 * @property string $amount
 * @property string $create_time
 * @property integer $is_delete
 */
class MemberIncome extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_member_income';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intention_id', 'member_id'], 'required'],
            [['intention_id', 'member_id', 'is_delete'], 'integer'],
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
            'intention_id' => 'Intention ID',
            'member_id' => 'Member ID',
            'amount' => 'Amount',
            'create_time' => 'Create Time',
            'is_delete' => 'Is Delete',
        ];
    }

    public function getIntention() {
        return $this->hasOne(ProjectIntention::className(), ['id' => 'intention_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $setting = SystemSetting::findOne(1);
            $stage = $this->intention->stage;
            $a = 'account_a_'.$stage;
            $b = 'account_b_'.$stage;
            $a_rate = $setting->$a /100;
            $b_rate = $setting->$b /100;
            $data = [
                'member_id' => $this->member_id,
                'intention_id' => $this->intention_id,
            ];
            $a_log = new CommissionLog();
            $a_log->attributes = $data;
            $a_log->type = 1;
            $a_log->amount = bcmul($this->amount,$a_rate,2);
            $a_log->save();

            $b_log = new CommissionLog();
            $b_log->type = 2;
            $b_log->amount = bcmul($this->amount,$b_rate,2);
            $b_log->save();

            $member = Member::findOne($this->member_id);
            $member->account_a += $a_log->amount;
            $member->account_b += $b_log->amount;
            $member->save();
        }
        return parent::afterSave($insert, $changedAttributes);
    }
}
