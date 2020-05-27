<?php


namespace app\modules\api\models;


use app\models\Member;
use app\models\PintouShop;

class ForgetPasswordForm extends ApiModel
{
    public $phone;
    public $code;
    public $password;
    public $password2;
    public $user_id;

    public function rules()
    {
        return [
            [['phone','password','password2','user_id'],'required'],   //type 1-会员、2-投资保姆、3-经纪人、4-合伙人、5商户
            [['code','phone','user_id'],'integer'],
            [['phone'],'match','pattern'=>\app\models\Model::MOBILE_PATTERN,'message'=>'手机号有误'],
            [['code'],'checkCode'],
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

    public function save() {
        if (!$this->validate()){
            return $this->errorResponse;
        }
        if ($this->password !== $this->password2) {
            return ['code'=>1,'两次密码输入不一致'];
        }
        $model = Member::find()->andWhere(['phone'=>$this->phone,'is_delete'=>0])->one();
        if (!$model) {
            $model = PintouShop::find()->andWhere(['phone'=>$this->phone,'is_delete'=>0])->one();
        }
        if (!$model) {
            return ['code'=>1,'后台未录入'];
        }
        if ($model->is_active == 0) {
            return ['code'=>1,'账号未认证'];
        }
        if (isset($model->role) && $model->role != 2 && $model->user_id != $this->user_id) {
            return ['code'=>1,'非认证微信号，无法操作'];
        }
        $model->password = \Yii::$app->getSecurity()->generatePasswordHash($this->password);
        $model->save();
        return ['code'=>0,'msg'=>'修改成功'];

    }

}