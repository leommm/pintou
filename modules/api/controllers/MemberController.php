<?php


namespace app\modules\api\controllers;

use app\hejiang\BaseApiResponse;
use app\modules\api\behaviors\LoginBehavior;
use app\modules\api\models\ConsumeListForm;
use app\modules\api\models\MemberIncomeForm;
use app\modules\api\models\MemberLoginForm;
use app\modules\api\models\MemberRegisterForm;
use app\utils\Sms;

class MemberController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginBehavior::className(),
            ],
        ]);
    }

    //发送验证码
    public function actionSms() {
        $phone = \Yii::$app->request->post('phone');
//        $cache = \Yii::$app->cache->get('code_cache'.$phone);var_dump($cache->code);die;
        return new BaseApiResponse(Sms::send_text('', $phone));
    }

    //成员认证
    public function actionRegister() {
        $form = new MemberRegisterForm();
        $form->attributes = \Yii::$app->request->post();
        $form->user_id = \Yii::$app->user->id;
        return new BaseApiResponse($form->register());
    }

    //登录
    public function actionLogin() {
        $form = new MemberLoginForm();
        $form->attributes = \Yii::$app->request->post();
        $form->user_id = \Yii::$app->user->id;
        return new BaseApiResponse($form->login());
    }

    //收入\消费记录
    public function actionConsumeList() {
        $form = new ConsumeListForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->search();
    }

}