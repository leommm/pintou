<?php


namespace app\modules\api\models;


use app\models\MemberIncome;

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
        $list = MemberIncome::find()->select('amount,create_time')->asArray()->all();

        return ['code'=>0,'msg'=>'success','data'=>['list'=>$list]];
    }


}