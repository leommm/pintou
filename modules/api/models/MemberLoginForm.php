<?php


namespace app\modules\api\models;


use app\models\CommissionLog;
use app\models\Enum;
use app\models\Member;
use app\models\PintouShop;
use app\models\ProjectIntention;
use app\models\SystemMessage;
use Yii;

class MemberLoginForm extends ApiModel
{
    public $user_id;
    public $phone;
    public $code;
    public $password;
    public $type; //type 1-会员、2-投资保姆、3-经纪人、4-合伙人、5商户
    public $login_type; // 1--手机号+验证码 2--手机号+密码
    private $model;

    public function rules()
    {
        return [
            [['type','login_type','user_id','phone'],'required'],
            [['login_type'],'in','range'=>[1,2]],
            [['phone'],'match','pattern'=>\app\models\Model::MOBILE_PATTERN,'message'=>'手机号格式有误'],
            [['type'],'checkType'],
            [['code'],'checkCode'],
            [['password'],'checkPass'],
        ];
    }

    public function attributeLabels()
    {
        return[
            'phone' => '手机号',
            'code' => '验证码',
            'password' => '密码',
            'login_type' => '登录类型',
            'type' => '身份类型',
            'user_id' => '微信用户ID'
        ];
    }

    public function checkType($attribute) {
        switch ($this->type) {
            case 1:
            case 2:
            case 3:
                $this->model = Member::find()->andWhere([
                    'phone' => $this->phone,
                    'role'=>$this->type,
                    'is_delete'=>0
                ])->one();
                break;
            case 4:
                $this->model = Member::find()->andWhere([
                    'phone' => $this->phone,
                    'is_partner'=>1,
                    'is_delete'=>0
                ])->one();
                break;
            case 5:
                $this->model = PintouShop::find()->andWhere([
                    'phone' => $this->phone,
                    'is_delete'=>0
                ])->one();
                break;
            default:
                $this->addError($attribute,'请选择正确的身份类型');
                return false;
                break;
        }
        if (!$this->model) {
            $this->addError($attribute,'后台未录入该账号');
            return false;
        }
        if (!$this->model->is_active && $this->type != 2) {
            $this->addError($attribute,'请先认证');
            return false;
        }
        if ($this->model->user_id != $this->user_id) {
            $this->addError($attribute,'非认证微信号，无法登录');
            return false;
        }
        return true;
    }

    public function checkCode($attribute) {
        if ($this->login_type != 1) {
            return true;
        }
        $cache = \Yii::$app->cache->get('code_cache'.$this->phone);
        if (!$cache) {
            $this->addError($attribute,"请先获取验证码");
            return false;
        }
        if ($cache->code != $this->code) {
            $this->addError($attribute,"验证码错误");
            return false;
        }
        \Yii::$app->cache->delete('code_cache'.$this->phone);
        return true;
    }

    public function checkPass($attribute) {
        if ($this->login_type != 2) {
            return true;
        }
//        $s = Yii::$app->getSecurity()->generatePasswordHash('123456'); var_dump($s);die;
        if (!$this->model->password) {
            return false;
        }
        $validate = Yii::$app->getSecurity()->validatePassword($this->password, $this->model->password);
        if (!$validate) {
            $this->addError($attribute,"密码错误错误");
            return false;
        }
        return true;
    }

    public function login() {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $data = $this->getDataByRole();
        return ['code'=>0,'msg'=>'success','data'=>$data];
    }

    //根据身份返回信息
    public function getDataByRole() {
        $data = [];
        switch ($this->type) {
            case 1:
                $data = $this->getMemberData();
                break;
            case 2:
                $data = $this->getNannyData();
                break;
            case 3:
                $data = $this->getAgentData();
                break;
            case 4:
                $data = $this->getPartnerData();
                break;
            case 5:
                $data = $this->getShopData();
                break;
        }
        return $data;
    }

    //获取会员数据
    private function getMemberData() {
        $settle_amount = CommissionLog::find()->select('amount,create_time,is_settle')->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_settle'=>1])
            ->andWhere(['in','type',[3,4]])->sum('amount');
        $unsettle_amount = CommissionLog::find()->select('amount,create_time,is_settle')->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_settle'=>0])
            ->andWhere(['in','type',[3,4]])->sum('amount');
        $all_amount = $settle_amount + $unsettle_amount;
        $num = SubListService::getSubCount($this->model->id);
        $sum = floatval($this->model->account_a) + floatval($this->model->account_b) + floatval($this->model->account_c);
        $member_info = [
            'member_id' => $this->model->id,
            'parent_name' => $this->model->parent->real_name,
            'role_name' => Enum::$LOGIN_TYPE[$this->type],
            'real_name' => $this->model->real_name,
            'phone' => $this->model->phone,
            'area' => $this->model->area,
            'account_a' => $this->model->account_a,
            'account_b' => $this->model->account_b,
            'account_c' => $this->model->account_c,
            'sum_money' => empty($sum) ? "0.00" : $sum,
            'settle_amount' => empty($settle_amount)? "0.00" :$settle_amount,
            'unsettle_amount' => empty($unsettle_amount)? "0.00" :$unsettle_amount,
            'all_amount' => empty($all_amount)? "0.00":$all_amount,
            'client_num' => $num,
            'share_img' => $this->model->share_img,
            'pay_code' => $this->model->pay_code,
            'not_read' => SystemMessage::find()->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_read'=>0])->count(),
        ];
        $intention_list = ProjectIntention::find()->alias('a')
            ->select('a.phone,a.remark,a.create_time,a.type,a.status,
            a.nanny_id,b.real_name as nanny_name,b.phone as nanny_phone,
            c.id as project_id,c.title,c.cover_pic')
            ->joinWith('nanny as b',false)
            ->joinWith('project as c',false)
            ->andWhere(['a.member_id'=>$this->model->id,'a.is_delete'=>0])
            ->andWhere(['in','a.status',[1,2,3]])
            ->orderBy('a.create_time')->limit(6)->asArray()->all();
        foreach ($intention_list as $k => $v) {
            $intention_list[$k]['product_type'] = Enum::getTypeNameByString($v['type']);
        }
        return [
          'member_info' => $member_info,
          'intention_list' => $intention_list,
        ];
    }
    //获取保姆数据
    private function getNannyData() {
        $all_income =  CommissionLog::find()->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'type'=>6])->sum('amount');
        $unsettle = CommissionLog::find()->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'type'=>6,'is_settle'=>0])->sum('amount');
        $nanny_info = [
            'nanny_id' => $this->model->id,
            'real_name' => $this->model->real_name,
            'phone' => $this->model->phone,
            'role_name' => Enum::$LOGIN_TYPE[$this->type],
            'client_num' => ProjectIntention::find()->andWhere(['nanny_id'=>$this->model->id,'is_delete'=>0])->groupBy('member_id')->count(),
            'all_income' =>empty($all_income)?"0.00":$all_income,
            'unsettled_income' => empty($unsettle)?"0.00":$unsettle,
            'not_read' => SystemMessage::find()->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_read'=>0])->count(),
        ];
        return [
            'nanny_info' => $nanny_info,
        ];
    }

    //获取经纪人数据
    private function getAgentData() {
        $settle_amount = CommissionLog::find()->select('amount,create_time,is_settle')->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_settle'=>1])
            ->andWhere(['in','type',[3,4]])->sum('amount');
        $unsettle_amount = CommissionLog::find()->select('amount,create_time,is_settle')->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_settle'=>0])
            ->andWhere(['in','type',[3,4]])->sum('amount');
        $all_amount = $settle_amount + $unsettle_amount;
        $num = SubListService::getSubCount($this->model->id);

        $agent_info = [
            'agent_id' => $this->model->id,
            'real_name' => $this->model->real_name,
            'phone' => $this->model->phone,
            'role_name' => Enum::$LOGIN_TYPE[$this->type],
            'settle_amount' => empty($settle_amount)? "0.00" :$settle_amount,
            'unsettle_amount' => empty($unsettle_amount)? "0.00" :$unsettle_amount,
            'all_amount' => empty($all_amount)? "0.00":$all_amount,
            'client_num' => $num,
            'share_img' => $this->model->share_img,
            'not_read' => SystemMessage::find()->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_read'=>0])->count(),
        ];
        return [
            'agent_info' => $agent_info
        ];
    }

    //获取合伙人数据
    private function getPartnerData(){
        $settle_amount = CommissionLog::find()->select('amount,create_time,is_settle')->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_settle'=>1])
            ->andWhere(['in','type',[5]])->sum('amount');
        $unsettle_amount = CommissionLog::find()->select('amount,create_time,is_settle')->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_settle'=>0])
            ->andWhere(['in','type',[5]])->sum('amount');
        $all_amount = $settle_amount + $unsettle_amount;
        $partner_info = [
            'partner_id' => $this->model->id,
            'real_name' => $this->model->real_name,
            'phone' => $this->model->phone,
            'role_name' => Enum::$LOGIN_TYPE[$this->type],
            'settle_amount' => empty($settle_amount)? "0.00" :$settle_amount,
            'unsettle_amount' => empty($unsettle_amount)? "0.00" :$unsettle_amount,
            'all_amount' => empty($all_amount)? "0.00":$all_amount,
            'area' => $this->model->area,
            'not_read' => SystemMessage::find()->andWhere(['member_id'=>$this->model->id,'is_delete'=>0,'is_read'=>0])->count(),
        ];
        return [
            'partner_info' => $partner_info
        ];
    }

    //获取商户数据
    public function getShopData() {
        $shop_info = [
            'shop_id' => $this->model->id,
            'real_name' => $this->model->real_name,
            'phone' => $this->model->phone,
            'shop_name' => $this->model->shop_name,
            'collection_code' => $this->model->collection_code,
            'role_name' => Enum::$LOGIN_TYPE[$this->type],
            'total_income' => $this->model->total_income,
            'cash_amount' => '0.00',
            'uncash_amount' => '0.00',
            'not_read' => SystemMessage::find()->andWhere(['shop_id'=>$this->model->id,'is_delete'=>0,'is_read'=>0])->count(),
        ];
        return [
            'shop_info' => $shop_info
        ];
    }
}