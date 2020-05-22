<?php


namespace app\modules\api\models;


use app\hejiang\ApiResponse;
use app\models\ShopIncome;
use app\utils\Helper;
use yii\data\Pagination;

class ConsumeListForm extends ApiModel
{
    public $member_id=0;
    public $shop_id=0;
    public $page=1;
    public $limit=0;

    public function rules()
    {
        return [
            [['member_id','limit','page','shop_id'],'integer'],
        ];
    }

    public function search() {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = ShopIncome::find()->alias('a')
            ->joinWith('member as b')
            ->joinWith('shop as c')
            ->select('a.id,a.real_name,b.shop_name,a.amount,a.create_time')
            ->andFilterWhere(['a.member_id'=>$this->member_id,'shop_id'=>$this->shop_id]);
        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $list = $query->orderBy('create_time desc')->limit($pagination->limit)->offset($pagination->offset)->asArray()->all();
        $is_next = Helper::judgeNext($this->page,$this->limit,$count);
        return new ApiResponse(0,'success',['is_next'=>$is_next,'list'=>$list]);

    }

}