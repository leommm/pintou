<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23 0023
 * Time: 14:59
 */
namespace app\modules\api\controllers;
use app\modules\api\models\DistrictForm;
use app\modules\api\models\PublishForm;
use app\hejiang\BaseApiResponse;
use app\models\District;

class DistrictController extends Controller
{

    //地区选择
    public function actionIndex()
    {


        $form=new DistrictForm();

        $form->type=\Yii::$app->request->get('type');

        $form->province=\Yii::$app->request->get('province');
        $form->city=\Yii::$app->request->get('city');
        $form->district=\Yii::$app->request->get('district');
        return new BaseApiResponse($form->save());
    }
   //保存地址
    public function actionSubmit(){
        $form=new DistrictForm();
        $form->province=\Yii::$app->request->get('province');
        $form->city=\Yii::$app->request->get('city');
        $form->district=\Yii::$app->request->get('district');
        $form->user_id = \Yii::$app->user->id;
        return new BaseApiResponse($form->submit());

    }


}
