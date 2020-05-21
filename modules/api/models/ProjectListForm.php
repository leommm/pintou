<?php

namespace app\modules\api\models;

use app\hejiang\ApiResponse;
use app\models\Project;
use app\models\SystemMessage;
use app\utils\Helper;
use yii\data\Pagination;


class ProjectListForm extends ApiModel
{
    public $page=1;
    public $limit = 10;
    public $key_word;

    public function rules()
    {
        return [
            [['page','limit',], 'integer'],
            [['key_word'],'safe']
        ];
    }

    public function search()
    {
        $query = Project::find()->andFilterWhere([
            'or',
            ['like','title',$this->key_word],
            ['like','sub_title',$this->key_word],
            ['like','area',$this->key_word],
        ]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $query = $query->orderBy('sort ASC,create_time DESC');
        $list = $query->limit($pagination->limit)->offset($pagination->offset)
            ->select('id,title,sub_title,area,cover_pic,type,read_count,virtual_read_count,is_chosen,is_hot,create_time')
            ->asArray()->all();

        foreach ($list as $i => $item) {
            $read_count = intval($item['read_count'] + $item['virtual_read_count']);
            unset($list[$i]['read_count']);
            unset($list[$i]['virtual_read_count']);
            if ($read_count < 10000) {
                $read_count = $read_count . '次浏览';
            }
            if ($read_count >= 10000) {
                $read_count = round($read_count / 10000,2) . 'W次浏览';
            }
            $list[$i]['read_count'] = $read_count;
            $list[$i]['time'] = date('Y-m-d',strtotime($item['create_time']));
            unset($list[$i]['create_time']);
            $list[$i]['type'] = explode(',',$item['type']);
        }

        $data = [
            'is_next' => Helper::judgeNext($this->page,$this->limit,$count),
            'list'=>$list
        ];
        return new ApiResponse(0,'success',$data);
    }
}
