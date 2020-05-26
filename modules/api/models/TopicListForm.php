<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/9/28
 * Time: 14:11
 */

namespace app\modules\api\models;

use app\hejiang\ApiResponse;
use app\models\Topic;
use app\utils\Helper;
use yii\data\Pagination;
use app\models\TopicType;

class TopicListForm extends ApiModel
{
    public $page=1;
    public $limit = 10;
    public $type='-1';

    public function rules()
    {
        return [
            [['page','limit'], 'integer'],
            ['type', 'string'],
        ];
    }

    public function search()
    {
        
        if ($this->type==='-1') {
            $query = Topic::find()->where([ 'is_delete' => 0,'is_chosen' =>1]);
        } elseif ($this->type==='-2') {
            $query = Topic::find()->where([ 'is_delete' => 0]);
        } elseif ($this->type) {
            $query = Topic::find()->where([ 'is_delete' => 0])->andWhere(['in','type',[0,$this->type]]);
        } else {
             $query = Topic::find()->where([ 'is_delete' => 0]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        if ($this->type=='-2') {
            $query = $query->orderBy('addtime DESC');
        }else {
            $query = $query->orderBy('sort ASC,addtime DESC');
        }
        $list = $query->limit($pagination->limit)->offset($pagination->offset)
            ->select('id,title,sub_title,cover_pic,read_count,virtual_read_count,addtime,layout')->asArray()->all();
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
            $list[$i]['time'] = date('Y-m-d',$item['addtime']);
            unset($list[$i]['addtime']);
            $list[$i]['read_count'] = $read_count;
        }
        return new ApiResponse(0, 'success', ['is_next' => Helper::judgeNext($this->page,$this->limit,$count),'list'=>$list]);
    }
}
