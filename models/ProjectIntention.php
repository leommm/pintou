<?php

namespace app\models;


/**
 * This is the model class for table "cshopmall_project_intention".
 *
 * @property Project $project
 * @property Member $member
 * @property Member $nanny
 * @property integer $id
 * @property integer $member_id
 * @property integer $project_id
 * @property string $real_name
 * @property string $phone
 * @property string $type
 * @property string $stage
 * @property string $remark
 * @property integer $nanny_id
 * @property string $parking_money
 * @property string $flats_money
 * @property string $shop_money
 * @property integer $status
 * @property string $create_time
 * @property string $deal_time
 * @property integer $is_delete
 */
class ProjectIntention extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_project_intention';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'project_id', 'real_name', 'phone', 'type'], 'required'],
            [['member_id', 'project_id', 'nanny_id', 'status','is_delete','stage','is_delete'], 'integer'],
            [['parking_money', 'flats_money', 'shop_money'], 'number'],
            [['create_time', 'deal_time'], 'safe'],
            [['real_name', 'remark'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 24],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '会员id',
            'project_id' => '项目id',
            'real_name' => '姓名',
            'phone' => '联系方式',
            'type' => '意向产品',
            'remark' => '留言备注',
            'nanny_id' => '分配保姆id',
            'parking_money' => '车位投资',
            'flats_money' => '公寓投资',
            'shop_money' => '商铺投资',
            'status' => '状态 0：未审核、1：已成交、2：未成交',
            'create_time' => 'Create Time',
            'deal_time' => '成交时间',
            'stage' => '投资年限'
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->create_time = date('Y-m-d H:i:s');
        }
        if (is_array($this->type)){
            $this->type = implode(',',$this->type);
        }
        return parent::beforeSave($insert);
    }

    public function getProject() {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getMember() {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getNanny() {
        return $this->hasOne(Member::className(), ['id' => 'nanny_id']);
    }
}
