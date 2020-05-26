<?php


namespace app\modules\api\models;


use app\models\CommissionLog;
use app\models\Member;
use app\models\User;
use app\utils\Helper;

class SubListForm extends ApiModel
{
    public $member_id;
    public $page = 1;
    public $limit = 10;

    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id', 'page', 'limit'], 'integer']
        ];
    }

    public function search()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $ids = SubListService::getSubList($this->member_id);
        $ids = array_slice($ids, ($this->page - 1) * $this->limit, $this->limit);
        $is_next = Helper::judgeNext($this->page, $this->limit, count($ids));
        $list = [];
        foreach ($ids as $k => $id) {
            $member = Member::findOne($id);
            $list[$k]['member_id'] = $id;
            $list[$k]['real_name'] = $member->real_name;
            $list[$k]['phone'] = $member->phone;
            $list[$k]['avatar_url'] = User::findOne($member->user_id)->avatar_url;
            $list[$k]['time'] = date('Y-m-d', strtotime($member->active_time));
            $sum_commission = CommissionLog::find()->alias('a')
                ->joinWith('intention as b', false)
                ->andWhere(['a.member_id' => $this->member_id, 'a.is_delete' => 0])
                ->andWhere(['b.member_id' => $id,'a.is_delete' => 0])
                ->andWhere(['in', 'a.type', [3, 4]])
                ->sum('amount');
            $list[$k]['sum_commission'] = empty($sum_commission) ? '0.00' : $sum_commission;
        }

        return ['code' => 0, 'msg' => 'success', ['is_next'=>$is_next,'list'=>$list]];

    }


}