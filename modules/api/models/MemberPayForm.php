<?php


namespace app\modules\api\models;


use app\models\Member;
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