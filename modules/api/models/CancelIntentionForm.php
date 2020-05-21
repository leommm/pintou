<?php


namespace app\modules\api\models;


use app\models\ProjectIntention;

class CancelIntentionForm extends ApiModel
{
    public $intention_id;
    public $member_id;

    public function rules()
    {
        return [
            [['intention_id','member_id'],'required'],
            [['intention_id','member_id'],'integer']
        ];
    }

    public function cancel() {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $intention = ProjectIntention::findOne($this->intention_id);
        if (!$intention) {
            return ['code'=>1,'msg'=>'未找到拼投意向'];
        }
        if ($intention->status != 1) {
            return ['code'=>1,'msg'=>'该拼投无法取消'];
        }
        if ($intention->member_id != $this->member_id) {
            return ['code'=>1,'msg'=>'不是您的意向，无法取消'];
        }
        $intention->status = 4;
        $intention->is_delete = 1;
        $intention->save();
        return ['code'=>0,'msg'=>'success'];
    }

}