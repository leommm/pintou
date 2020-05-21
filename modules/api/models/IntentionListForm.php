<?php


namespace app\modules\api\models;


use app\models\Enum;
use app\models\ProjectIntention;
use app\models\SystemSetting;
use app\utils\Helper;
use yii\data\Pagination;

class IntentionListForm extends ApiModel
{
    public $nanny_id=0;
    public $member_id=0;
    public $page = 1;
    public $limit = 10;

    public function rules()
    {
        return [
            [['nanny_id','member_id','page','limit'],'integer'],
        ];
    }

    public function search(){
        if (empty($this->nanny_id) && empty($this->member_id)) {
            return ['code'=>1,'ID不能为空'];
        }
        if ($this->nanny_id && $this->member_id) {
            return ['code'=>1,'请选择一种身份'];
        }
        $query = ProjectIntention::find()->alias('a')
            ->select('a.id as intention_id,c.id as project_id,c.title,c.sub_title,c.cover_pic,a.remark,
            a.type,a.real_name,a.phone,a.parking_money,a.flats_money,a.shop_money,a.status,a.create_time,
            a.nanny_id,b.real_name as nanny_name,b.phone as nanny_phone')
            ->joinWith('nanny as b',false)
            ->joinWith('project as c',false)
            ->andWhere(['a.is_delete'=>0]);
        if ($this->member_id) {
           $query->andWhere(['a.member_id'=>$this->member_id])->andWhere(['in','a.status',[1,2,3]]);
        }
        if ($this->nanny_id) {
            $query->andWhere(['a.nanny_id'=>$this->nanny_id])->andWhere(['in','a.status',[2,3]]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $list = $query->groupBy('a.id')->orderBy('a.id desc')
            ->limit($pagination->limit)->offset($pagination->offset)
//            ->createCommand()->getRawSql();var_dump($list);die;
            ->asArray()->all();

        $nanny_rate = SystemSetting::findOne(1)->nanny_commission;
        foreach ($list as $k => $v) {
            $list[$k]['product_type'] = Enum::getTypeNameByString($v['type']);
            $list[$k]['create_time'] = date('Y-m-d',strtotime($v['create_time']));
            $list[$k]['sum_money'] = floatval($v['parking_money']) + floatval($v['flats_money']) + floatval($v['shop_money']);
            if (!$list[$k]['sum_money']) {
                $list[$k]['sum_money'] = '暂未投资';
            }
            $list[$k]['nanny_money'] =bcmul($list[$k]['sum_money'] , $nanny_rate/100,2);
        }

        $is_next = Helper::judgeNext($this->page,$this->limit,$count);
        return ['code'=>0,'msg'=>'success','data'=>['is_next' => $is_next,'list'=>$list]];
    }

}