<?php


namespace app\modules\api\models;



use app\models\Member;
use app\models\MemberApply;
use app\models\PintouShop;
use app\models\SystemSetting;

/**
 * 用户认证逻辑层
 * Class MemberRegisterForm
 * @package app\modules\api\models
 *
 */
class MemberRegisterForm extends ApiModel
{
    public $user_id;
    public $type;
    public $phone;
    public $real_name;
    public $id_card;
    public $bank_card;
    public $parent_id=0;
    public $code;

    public function rules()
    {
        return [
            [['type','phone','real_name','id_card','bank_card','code','user_id'],'required'],   //type 1-会员、2-投资保姆、3-经纪人、4-合伙人、5商户
            [['type','parent_id','code','user_id'],'integer'],
            [['real_name'],'required'],
            [['phone'],'match','pattern'=>\app\models\Model::MOBILE_PATTERN,'message'=>'手机号有误'],
            [['id_card'],'match','pattern'=>'/\+\d{17}[\d|x]|\d{15}\d/','message'=>'请检查身份证号'],
            [['bank_card'],'match','pattern'=>'/\d{15}|\d{19}/','message'=>'请检查银行卡信息'],
            [['code'],'checkCode'],
        ];
    }

    public function attributeLabels()
    {
        return[
            'type' => '身份',
            'parent_id' => '上级',
            'real_name' => '姓名',
            'phone' => '手机号',
            'id_card' => '身份证号',
            'bank_card' => '银行卡号',
            'code' => '验证码',
            'user_id' => '用户ID'
        ];
    }

    public function checkCode($attribute,$params) {
        $cache = \Yii::$app->cache->get('code_cache'.$this->phone);
        if (!$cache) {
            $this->addError($attribute,"请先获取验证码");
            return false;
        }
        if ($cache->code != $this->code) {
            $this->addError($attribute,"验证码错误");
            return false;
        }
        return true;
    }

    public function register() {
        if (!$this->validate()){
            return $this->errorResponse;
        }
        switch ($this->type) {
            case 1:
//            case 2:
            case 3:
                $query = Member::find()->andWhere(['role'=>$this->type,'is_delete'=>0]);
                break;
//            case 4:
//                $query = Member::find()->andWhere(['is_parent'=>1,'is_delete'=>0]);
//                break;
            case 5:
                $query = PintouShop::find()->andWhere(['is_delete'=>0]);
                break;
            default:
                return ['code'=>1,'msg'=>'请选择正确的身份类型'];
        }
        $model = $query->andWhere([
            'phone' => $this->phone,
            'real_name' => $this->real_name,
        ])->one();
        if (!$model) {
            return ['code'=>1,'msg'=>'后台未录入该账号'];
        }
        if ($model->is_active) {
            return ['code'=>1,'msg'=>'该账号已认证'];
        }
        $condition = SystemSetting::findOne(1)->condition;
        if ($condition) {
            $exist = MemberApply::find()->andWhere(['is_delete'=>0,'status'=>0,'user_id'=>$this->user_id,'phone'=>$this->phone])->exists();
            if ($exist) {
                return ['code'=>1,'msg'=>'您有待审核的认证申请','data'=>[]];
            }
            $apply = new MemberApply();
            $apply->attributes = [
                'type' => $this->type,
                'user_id' => $this->user_id,
                'id_card' => $this->id_card,
                'bank_card' => $this->bank_card,
                'real_name' => $this->real_name,
                'phone' => $this->phone
            ];
            $apply->save();
            return ['code'=>0,'msg'=>'认证申请已提交','data'=>[]];
        }else {
            $model->id_card = $this->id_card;
            $model->bank_card = $this->bank_card;
            $model->user_id = $this->user_id;
            $model->active_time = date('Y-m-d H:i:s');
            $model->is_active = 1;
            if ($model->save()) {
                \Yii::$app->cache->delete('code_cache'.$this->phone);
            }
            return ['code'=>0,'msg'=>'认证成功,请登录','data'=>[]];
        }

    }

}