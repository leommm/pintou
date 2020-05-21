<?php


namespace app\modules\api\models;


use app\models\CommissionService;
use app\models\IntentionFollow;
use app\models\ProjectIntention;
use yii\db\Exception;

class IntentionFollowForm extends ApiModel
{
    public $intention_id;
    public $nanny_id;
    public $remark;
    public $is_deal;

    public function rules()
    {
        return [
            [['intention_id','nanny_id','remark','is_deal'],'required'],
            [['intention_id','nanny_id','is_deal'],'integer'],
            [['remark'],'string']
        ];
    }

    public function attributeLabels()
    {
        return[
            'intention_id' => '意向ID',
            'nanny_id' => '保姆ID',
            'remark' => '跟进备注',
        ];
    }

    public function save() {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $intention = ProjectIntention::findOne($this->intention_id);
        if ($intention->nanny_id != $this->nanny_id) {
            return ['code'=>1,'msg'=>'您不是该意向的投资保姆。'];
        }
        if ($intention->status != 2) {
            return ['code'=>1,'msg'=>'该项目未在跟进状态，无法跟进。'];
        }
        if ($this->is_deal) {
            $sum_money = floatval($intention->shop_money) + floatval($intention->flats_money) + floatval($intention->parking_money);
            if ($sum_money <= 0 || !$intention->stage) {
                return ['code'=>1,'msg'=>'拼投信息不完整，请联系后台录入。'];
            }
            $intention->status = 3;
            $intention->deal_time = date('Y-m-d H:i:s');
            $intention->save();
            //开始分佣
            $commission_service = new CommissionService($intention->id);
            try {
                $commission_service->handleCommission();
            } catch (Exception $e) {
                return ['code'=>1,'msg'=>$e->getMessage()];
            }
        }
        $record = new IntentionFollow();
        $record->attributes = [
            'intention_id' => $this->intention_id,
            'nanny_id' => $this->nanny_id,
            'remark' => $this->remark,
            'project_id' => $intention->project_id,
            'member_id' => $intention->member_id,
            'status' => $intention->status,
        ];
        $record->save();
        return ['code'=>0,'msg'=>'success'];
    }

}