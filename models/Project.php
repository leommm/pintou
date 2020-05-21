<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cshopmall_project".
 *
 * @property integer $id
 * @property string $title
 * @property string $sub_title
 * @property string $cover_pic
 * @property string $content
 * @property string $type
 * @property integer $read_count
 * @property integer $virtual_read_count
 * @property integer $sort
 * @property integer $agree_count
 * @property integer $virtual_agree_count
 * @property integer $virtual_favorite_count
 * @property string $create_time
 * @property integer $is_delete
 * @property integer $is_chosen
 * @property integer $is_show
 * @property integer $is_hot
 * @property string $area
 * @property integer $p_id
 * @property integer $c_id
 * @property integer $d_id
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cshopmall_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','cover_pic','content','area'],'required'],
            [['cover_pic', 'content'], 'string'],
            [['read_count', 'virtual_read_count', 'sort', 'is_delete', 'is_chosen', 'is_show', 'is_hot', 'p_id', 'c_id', 'd_id'], 'integer'],
            [['create_time', 'agree_count', 'virtual_agree_count', 'virtual_favorite_count','type'], 'safe'],
            [['title', 'sub_title', 'area'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'sub_title' => '副标题',
            'cover_pic' => '封面图片',
            'content' => '详情',
            'type' => '支持产品 1：车位、2：公寓、3：商铺',
            'read_count' => '阅读量',
            'virtual_read_count' => '虚拟阅读量',
            'sort' => '排序：升序',
            'agree_count' => '点赞数',
            'virtual_agree_count' => '虚拟点赞数',
            'virtual_favorite_count' => '虚拟收藏量',
            'create_time' => 'Create Time',
            'is_delete' => 'Is Delete',
            'is_chosen' => 'Is Chosen',
            'is_show' => '是否展示',
            'is_hot' => '是否热门',
            'area' => '所属区域',
            'p_id' => 'P ID',
            'c_id' => 'C ID',
            'd_id' => 'D ID',
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
}
