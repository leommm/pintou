<?php


namespace app\modules\api\models;


use app\models\Member;
use app\models\MessageService;
use app\models\PintouShop;
use app\models\ShopIncome;
use yii\db\Exception;

class MemberPayForm extends ApiModel
{
    public $member_id;
    public $shop_id;
    public $amount;

    public function rules()
    {
        return [
            [['member_id','shop_id','amount'],'required'],
            [['member_id','shop_id'],'integer'],
            [['amount'],'number']
        ];
    }

    /**
     * @return \app\hejiang\ValidationErrorResponse|array
     * @throws Exception
     */
    public function pay() {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $member = Member::findOne($this->member_id);
        $shop = PintouShop::findOne($this->shop_id);

        if (!$member || !$shop) {
            return ['code'=>1,'msg'=>'参数错误'];
        }

        $left = bcsub($member->account_b,$this->amount,2);
        if ($left < 0) {
            return ['code'=>1,'msg'=>'余额不足'];
        }
        $transaction  = \Yii::$app->db->beginTransaction();
        try{
            $log = new ShopIncome();
            $log->amount = $this->amount;
            $log->member_id = $this->member_id;
            $log->shop_id = $this->shop_id;

            $member->account_b = $left;

            $shop->total_income = bcadd($shop->total_income,$this->amount,2);
            MessageService::createMsg($this->member_id,1,'系统通知','您在'.$shop->shop_name.'消费了'.$this->amount.'元','pages/records_consumption/records_consumption');
            MessageService::createShopMsg($this->shop_id,5,'系统通知',$member->real_name.'消费了'.$this->amount.'元','pages/business_center/business_center');

            if (!$log->save() || !$member->save() || !$shop->save()) {
                throw new Exception('保存失败');
            }
            $transaction->commit();
        }catch (\Exception $e) {
            $transaction->rollBack();
            return ['code'=>1,'msg'=>$e->getMessage()];
        }
        return ['code'=>0,'msg'=>'消费成功'];
    }
}