<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_intention_follow".
 *
 * @property integer $id
 * @property integer $intention_id
 * @property integer $nanny_id
 * @property integer $member_id
 * @property integer $project_id
 * @property string $remark
 * @property integer $status
 * @property string $create_time
 * @property integer $is_delete
 */
class IntentionFollow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_intention_follow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intention_id', 'nanny_id', 'member_id', 'project_id', 'status', 'is_delete'], 'integer'],
            [['remark'], 'required'],
            [['create_time'], 'safe'],
            [['remark'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'intention_id' => '意向id',
            'nanny_id' => '保姆id',
            'member_id' => '会员id',
            'project_id' => '项目id',
            'remark' => '跟进备注',
            'status' => '跟进时意向状态',
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

    public function getProject() {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getNanny() {
        return $this->hasOne(Member::className(), ['id' => 'nanny_id']);
    }

    public function getIntention() {
        return $this->hasOne(ProjectIntention::className(), ['id' => 'intention_id']);
    }

    public function getMember() {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

}
