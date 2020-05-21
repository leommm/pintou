<?php


namespace app\modules\api\models;


use app\models\Enum;
use app\models\ProjectIntention;

class IntentionSubmitForm extends ApiModel
{
    public $member_id;
    public $project_id;
    public $real_name;
    public $phone;
    public $type;
    public $remark;

    public function rules()
    {
        return [
            [['member_id','project_id','real_name','phone','type'],'required'],
            [['phone'],'match','pattern'=>\app\models\Model::MOBILE_PATTERN,'message'=>'手机号有误'],
            [['remark'],'safe'],
            [['member_id','project_id'],'integer'],
            [['type'],'checkType'],
        ];
    }

    public function checkType($attribute,$params) {
        $this->type = json_decode($this->type);
        if (empty($this->type)) {
            $this->addError($attribute,'请选择意向产品');
            return false;
        }
        $type = array_keys(Enum::$PRODUCT_TYPE);
        foreach ($this->type as $v) {
            if (!in_array($v,$type)) {
                $this->addError($attribute,'产品类型错误');
                return false;
            }
        }
        return true;
    }

    public function attributeLabels()
    {
        return[
            'real_name' => '姓名',
            'phone' => '手机号',
            'remark' => '留言备注',
            'member_id' => '会员ID',
            'project_id' => '项目ID',
            'type' => '咨询产品',
            'user_id' => '微信用户ID'
        ];
    }

    public function submit() {
        if (!$this->validate()){
            return $this->errorResponse;
        }
        $intention = new ProjectIntention();
        $intention->attributes = $this->attributes;
        $intention->save();
        return ['code'=>0,'msg'=>'success'];
    }


}