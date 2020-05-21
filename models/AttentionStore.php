<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%attr}}".
 *
 * @property integer $id
 * @property integer $attr_group_id
 * @property string $attr_name
 * @property integer $is_delete
 * @property integer $is_default
 */
class AttentionStore extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attention_store}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'mch_id','is_delete'], 'required'],
          
        ];
    }

    /**
     * @inheritdoc
     */
   
}
