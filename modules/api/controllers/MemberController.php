<?php


namespace app\modules\api\controllers;

use app\hejiang\BaseApiResponse;
use app\modules\api\behaviors\LoginBehavior;
use app\modules\api\models\ConsumeListForm;
use app\modules\api\models\ForgetPasswordForm;
use app\modules\api\models\MemberIncomeForm;
use app\modules\api\models\MemberLoginForm;
use app\modules\api\models\MemberPayForm;
use app\modules\api\models\MemberRegisterForm;
use app\modules\api\models\SubListForm;
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

    //忘记密码
    public function actionForgetPassword() {
        $form = new ForgetPasswordForm();
        $form->attributes = \Yii::$app->request->post();
        $form->user_id = \Yii::$app->user->id;
        return new BaseApiResponse($form->save());
    }

    //收入\消费记录
    public function actionConsumeList() {
        $form = new ConsumeListForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->search();
    }

    /**
     * 扫码消费
     * @return BaseApiResponse
     * @throws \yii\db\Exception
     */
    public function actionPay() {
        $form = new MemberPayForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->pay());
    }

    /**
     * 下级列表
     */
    public function actionSubList() {
        $form = new SubListForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->search());
    }

}