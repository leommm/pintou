<?php


namespace app\modules\api\models;


use app\models\CommissionLog;
use app\models\Enum;
use app\models\Member;
use app\models\ProjectIntention;
use app\utils\Helper;
use yii\data\Pagination;

class CommissionLogForm extends ApiModel
{
    public $id;
    public $page = 1;
    public $limit = 10;
    public $type = 1; // 1-会员 2-保姆 3-经纪人 4-合伙人

    public function rules()
    {
        return [
            [['id','type'],'required'],
            [['id','limit','page','type'],'integer'],
            [['type'],'checkType']
        ];
    }

    public function checkType($attribute) {
        $member = Member::findOne($this->id);
        if (in_array($this->type,[1,2,3]) && $this->type != $member->role) {
            $this->addError($attribute,'请确认身份');
            return false;
        }
        if ($this->type == 4 && $member->is_partner != 1) {
            $this->addError($attribute,'请确认身份');
            return false;
        }
        return true;
    }

    public function search() {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if ($this->type == 1 || $this->type == 3) {
            $status = [3,4];
        }elseif ($this->type == 2) {
            $status = [6];
        } elseif ($this->type == 4) {
            $status = [5];
        }else{
            return ['code'=>1,'msg'=>'类型错误'];
        }

        $query = CommissionLog::find()->select('amount,intention_id,create_time,is_settle')->andWhere(['member_id'=>$this->id,'is_delete'=>0])
                ->andWhere(['in','type',$status]);
        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $list = $query->orderBy('create_time desc')
            ->limit($pagination->limit)->offset($pagination->offset)
            ->asArray()->all();

        foreach ($list as $k => $v) {
            $intention = ProjectIntention::findOne($v['intention_id']);
            $list[$k]['real_name'] = $intention->real_name;
            $list[$k]['title'] = $intention->project->title;
            $type = [];
            if ($intention->parking_money) {
                $type[] = 1;
            }
            if ($intention->flats_money) {
                $type[] = 2;
            }
            if ($intention->shop_money) {
                $type[] = 3;
            }
            $list[$k]['sum_money'] = floatval($intention->parking_money) + floatval($intention->flats_money) + floatval($intention->shop_money);
            $list[$k]['product_type'] = Enum::getTypeNameByString(implode(',',$type));
            $list[$k]['create_time'] = date('Y-m-d',strtotime($v['create_time']));
        }
        $is_next = Helper::judgeNext($this->page,$this->limit,$count);
        $data = [
            'is_next'=>$is_next,
            'list'=>$list
        ];
        return ['code'=>0,'msg'=>'success','data'=>$data];
    }


}