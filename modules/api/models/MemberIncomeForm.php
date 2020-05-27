<?php


namespace app\modules\api\models;


use app\models\MemberIncome;
use app\models\ProjectIntention;
use app\models\SystemSetting;

class MemberIncomeForm extends ApiModel
{
    public $intention_id;

    public function rules()
    {
        return [
            [['intention_id'],'required'],
        ];
    }

    public function search() {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $intention = ProjectIntention::findOne($this->intention_id);
        $list = MemberIncome::find()->select('amount,create_time')->andWhere(['intention_id'=>$this->intention_id])->asArray()->all();
        $setting = SystemSetting::findOne(1);
        $a = 'account_a_'.$intention->stage;
        $b = 'account_b_'.$intention->stage;
        $a_rate = $setting->$a /100;
        $b_rate = $setting->$b /100;
        foreach ($list as $k => $v) {
            $list[$k]['account_a'] = bcmul($v['amount'],$a_rate,2);
            $list[$k]['account_b'] = bcmul($v['amount'],$b_rate,2);
            $list[$k]['create_time'] = date('Y-m-d',strtotime($v['create_time']));
        }
        $all_amount =  MemberIncome::find()->andWhere(['intention_id'=>$this->intention_id])->sum('amount');
       $data = [
           'all_amount' => $all_amount,
           'all_account_a' => bcmul($all_amount,$a_rate,2),
           'all_account_b' => bcmul($all_amount,$b_rate,2),
           'list'=>$list
       ];
        return ['code'=>0,'msg'=>'success','data'=>$data];
    }


}