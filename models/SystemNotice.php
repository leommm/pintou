<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_system_notice".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $page_url
 * @property string $create_time
 * @property integer $is_push
 * @property integer $is_delete
 */
class SystemNotice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_system_notice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['is_push', 'is_delete'], 'integer'],
            [['content'], 'string'],
            [['create_time'], 'safe'],
            [['title', 'page_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'page_url' => '小程序页面',
            'create_time' => 'Create Time',
            'is_push' => '是否推送',
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
}
