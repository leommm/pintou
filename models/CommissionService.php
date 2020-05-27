<?php


namespace app\models;

use yii\db\Exception;

class CommissionService
{
    public $intention_id;
    public $intention;
    /**
     * 一级车位佣金
     * @var float|int
     */
    private $first_parking;
    /**
     * 一级公寓佣金
     * @var float|int
     */
    private $first_flats;
    /**
     * 一级商铺佣金
     * @var float|int
     */
    private $first_shop;
    /**
     * 二级佣金
     * @var float|int
     */
    private $second;
    /**
     * 城市佣金
     * @var float|int
     */
    private $city_commission;
    /**
     * 保姆佣金
     * @var float|int
     */
    private $nanny_commission;
    /**
     * 总金额
     * @var float
     */
    private $sum_money;


    public function __construct($intention_id){
        $this->intention_id = $intention_id;
        $this->intention = ProjectIntention::findOne($intention_id);
        $this->sum_money = floatval($this->intention->parking_money) + floatval($this->intention->flats_money) + floatval($this->intention->shop_money);
        $setting = SystemSetting::findOne(1);
        $this->first_parking = $setting->first_parking / 100;
        $this->first_flats = $setting->first_flats / 100;
        $this->first_shop = $setting->first_shop / 100;
        $this->second = $setting->second / 100;
        $this->city_commission = $setting->city_commission / 100;
        $this->nanny_commission = $setting->nanny_commission / 100;

    }

    //开始分佣

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function  handleCommission() {
        if ($this->intention->status != 3 ) {
            return false;
        }
        //开启事务
        $transaction  = \Yii::$app->db->beginTransaction();
        try {
            //给会员发送消息
            MessageService::createMsg($this->intention->member_id,1,'系统通知','您的'.$this->intention->project->title . '拼投成功','pages/projects_history/projects_history');

            //保姆分佣
            $nanny_commission_amount = bcmul($this->sum_money,$this->nanny_commission,2);
            $this->createLog($this->intention->nanny_id,6,$nanny_commission_amount);
            $this->intention->nanny->account_c += $nanny_commission_amount;
            $this->intention->nanny->save();
            MessageService::createMsg($this->intention->nanny_id,2,'系统通知','您跟进的'.$this->intention->project->title . '拼投成功','pages/investment_nanny/investment_nanny');

            //上级分佣
            if ($this->intention->member->parent_id) {
                $first_commission_amount = bcmul($this->first_parking,$this->intention->parking_money,2)
                    + bcmul($this->first_flats,$this->intention->flats_money,2)
                    + bcmul($this->first_shop,$this->intention->shop_money,2);
                $this->createLog($this->intention->member->parent_id,3,$first_commission_amount);
                $this->intention->member->parent->account_c += $first_commission_amount;
                $this->intention->member->parent->save();
            }

            //上上级分佣
            if ($this->intention->member->parent->parent_id) {
                $second_commission_amount = bcmul($this->sum_money,$this->second,2);
                $this->createLog($this->intention->member->parent->parent_id,4,$second_commission_amount);
                $this->intention->member->parent->parent->account_c += $second_commission_amount;
                $this->intention->member->parent->parent->save();
            }

            //城市分佣
            $city_member = Member::find()->andWhere(['is_delete' => 0, 'is_partner' => 1, 'd_id' => $this->intention->project->d_id])->one();
            if ($city_member) {
                $city_commission_amount = bcmul($this->sum_money,$this->city_commission,2);
                $this->createLog($city_member->id,5,$city_commission_amount);
                $city_member->account_c += $city_commission_amount;
                $city_member->save();
                MessageService::createMsg($city_member->id,4,'系统通知','您的区域内有新项目拼投成功','pages/partner_page/partner_page');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new Exception('分佣失败');
        }
        $transaction->commit();
        return true;
    }

    private function createLog($member_id,$type,$amount) {
        $log = new CommissionLog();
        $log->attributes = [
            'intention_id' => $this->intention_id,
            'member_id' => $member_id,
            'type' => $type,
            'amount' => $amount
        ];
        return $log->save();
    }

}