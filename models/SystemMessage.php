<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_system_message".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $shop_id
 * @property string $title
 * @property string $content
 * @property integer $is_read
 * @property integer $is_delete
 * @property string $create_time
 * @property integer $type
 */
class SystemMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_system_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['member_id', 'shop_id', 'is_read', 'is_delete','type'], 'integer'],
            [['create_time'], 'safe'],
            [['title', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '成员id',
            'shop_id' => '商户id',
            'title' => '标题',
            'content' => '内容',
            'is_read' => 'Is Read',
            'is_delete' => 'Is Delete',
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
}
