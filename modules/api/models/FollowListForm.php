<?php


namespace app\modules\api\models;


use app\models\IntentionFollow;
use app\utils\Helper;
use yii\data\Pagination;

class FollowListForm extends ApiModel
{
    public $intention_id;
    public $page = 1;
    public $limit = 10;

    public function rules()
    {
        return [
          [['intention_id','page','limit'],'integer'],
          [['intention_id'],'required']
        ];
    }

    public function search() {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = IntentionFollow::find()
            ->andWhere(['intention_id' => $this->intention_id,'is_delete'=>0]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $list = $query->orderBy('create_time desc')
            ->limit($pagination->limit)->offset($pagination->offset)
            ->asArray()->all();
        $is_next = Helper::judgeNext($this->page,$this->limit,$count);
        return ['code'=>0,'msg'=>'success','data'=>['is_next'=>$is_next,'list'=>$list]];
    }

}