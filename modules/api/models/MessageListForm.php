<?php

namespace app\modules\api\models;

use app\hejiang\ApiResponse;
use app\models\SystemMessage;
use app\utils\Helper;
use yii\data\Pagination;


class MessageListForm extends ApiModel
{
    public $page=1;
    public $limit = 10;
    public $member_id=null;
    public $shop_id=null;
    public $type=1;

    public function rules()
    {
        return [
            [['page','limit','member_id','shop_id','type'], 'integer'],
        ];
    }

    public function search()
    {
        if (empty($this->shop_id) && empty($this->member_id)) {
            return new ApiResponse(1,'缺少参数');
        }

        $query = SystemMessage::find()
            ->andFilterWhere(['member_id'=>$this->member_id,'shop_id'=>$this->shop_id])
            ->andWhere(['is_delete'=>0])
            ->andWhere(['in','type',[0,$this->type]]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $query = $query->orderBy('create_time DESC');
        $list = $query->limit($pagination->limit)->offset($pagination->offset)
            ->select('id,title,content,is_read,type,create_time')
            ->asArray()->all();
        foreach ($list as $i => $item) {
            $list[$i]['time'] = date('Y-m-d',strtotime($item['create_time']));
            unset($list[$i]['create_time']);
        }

        $notRead = SystemMessage::find()->andFilterWhere(['member_id'=>$this->member_id,'shop_id'=>$this->shop_id])
            ->andWhere(['is_read'=>0])->andWhere(['in','type',[0,$this->type]])->count();
        $data = [
            'is_next' => Helper::judgeNext($this->page,$this->limit,$count),
            'not_read' => $notRead,
            'list'=>$list
        ];
        return new ApiResponse(0,'success',$data);
    }
}
