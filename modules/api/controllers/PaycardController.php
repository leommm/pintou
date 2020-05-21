<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23 0023
 * Time: 14:59
 */
namespace app\modules\api\controllers;
use app\modules\api\models\PaycardForm;
use app\modules\api\models\PublishForm;
use app\hejiang\BaseApiResponse;
use app\models\Paycard;

    class PaycardController extends Controller{


     public  function actionCard(){
       $form =new PaycardForm();
       $form->user_id=\Yii::$app->user->id;
       return new BaseApiResponse($form->card());

     }

    //打卡
    public function actionIndex(){
        $form=new PaycardForm();
        $form->lng1 =\Yii::$app->request->get('lng1');
        $form->lat1 =\Yii::$app->request->get('lat1');
        $form->station_id =\Yii::$app->request->get('station_id');
       /* $form->user_id =\Yii::$app->user->id;*/
        $form->user_id =\Yii::$app->user->id;
        $form->addtime=time();

        return new BaseApiResponse($form->save());

        
    }
//打卡记录
   public function actionList(){

        $form=new paycardForm();
        $form->time =\Yii::$app->request->get('time');
        $form->time1 =\Yii::$app->request->get('time1');
        $form->user_id =\Yii::$app->user->id;

        return new BaseApiResponse($form->paylist());
   }

    
}

 //发布需求