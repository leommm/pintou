<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2018/3/9
 * Time: 10:26
 */

namespace app\modules\user\controllers;

use app\hejiang\ApiCode;
use app\models\Mch;
use app\models\User;
use app\models\UserAuthLogin;
use yii\helpers\VarDumper;
use yii\web\Response;

class PassportController extends Controller
{
    public $layout = 'passport';

    public function actionLogin()
    {
        $m = new UserAuthLogin();
        $m->store_id = $this->store->id;
        $m->token = md5(uniqid());
        $m->addtime = time();
        $m->save();
        $at = $this->wechat->getAccessToken();
        $curl = $this->wechat->curl;
        $api = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $at;
        $data = json_encode([
            'scene' => $m->token,
            'page' => 'pages/web/login/login',
            'line_color' => [
                'r' => 39,
                'g' => 92,
                'b' => 45,
            ],
        ], JSON_UNESCAPED_UNICODE);
        $curl->post($api, $data);
        $error = null;
        return $this->render('login', [
            'error' => $error,
            'token' => $m->token,
            'img_url' => 'data:image/jpeg;base64,' . base64_encode($curl->response),
        ]);
    }

    public function actionCheckLogin($token)
    {
        if (!$token) {
            return [
                'code' => 1,
                'msg' => 'token不能为空',
            ];
        }
        for ($i = 0; $i < 3; $i++) {
            $m = UserAuthLogin::findOne(['token' => $token]);
            if (!$m) {
                return [
                    'code' => 1,
                    'msg' => '错误的token',
                ];
            }
            if ($m->is_pass == 0) {
                sleep(3);
            }
            if ($m->is_pass == 1) {
                $user = User::findOne($m->user_id);
                \Yii::$app->user->login($user);
                $mch = Mch::find()->where(['store_id' => $this->store->id, 'user_id' => $m->user_id])->one();
                if ($mch->is_open === Mch::IS_OPEN_FALSE) {
                    return [
                      'code' => ApiCode::CODE_ERROR,
                      'msg' => '店铺已被关闭,请联系管理员'
                    ];
                }
                return [
                    'code' => ApiCode::CODE_SUCCESS,
                    'msg' => '登录成功',
                ];
            }
        }
        return [
            'code' => -1,
            'msg' => '请扫描小程序码登录',
        ];
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        \Yii::$app->response->redirect(\Yii::$app->urlManager->createUrl(['user']))->send();
    }
}
