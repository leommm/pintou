<?php
namespace app\modules\mch\controllers;
/**
 * Created by PhpStorm.
 * User: wch
 * Date: 2018/11/29
 * Time: 11:16
 */
use app\modules\mch\models\CountForm;
use app\modules\mch\models\StoreDataForm;
class CountController extends Controller
{


    /*
     * 平台入订单/商品/需求统计
     * */
    public function actionOrder()
    {
        if (\Yii::$app->request->isAjax) {
//            $form = new StoreDataForm();
            $form = new CountForm();
            $form->store_id = $this->store->id;
            $form->sign = \Yii::$app->request->get('sign');
            $form->type = \Yii::$app->request->get('type');
            $store_data = $form->search();
//            echo "<pre>";print_r($store_data);exit;
            return json_encode($store_data);
        } else {
            return $this->render('order', [
                'store' => $this->store,
            ]);
        };

    }

    /*
     * 平台交易统计
     * */
    public function actionTrade()
    {
        if (\Yii::$app->request->isAjax) {
//            $form = new StoreDataForm();
            $form = new CountForm();
            $form->store_id = $this->store->id;
            $form->sign = \Yii::$app->request->get('sign');
            $form->type = \Yii::$app->request->get('type');
            $form->limit = 10;
            $store_data = $form->search2();
//            echo "<pre>";print_r($store_data);exit;
            return json_encode($store_data);
        } else {
            return $this->render('trade', [
                'store' => $this->store,
            ]);
        };
    }

    /*
     * 平台会员统计
     * */
    public function actionUser()
    {
        if (\Yii::$app->request->isAjax) {
//            $form = new StoreDataForm();
            $form = new CountForm();
            $form->store_id = $this->store->id;
            $form->sign = \Yii::$app->request->get('sign');
            $form->type = \Yii::$app->request->get('type');
            $form->limit = 10;
            $store_data = $form->search3();
//            echo "<pre>";print_r($store_data);exit;
            return json_encode($store_data);
        } else {
            return $this->render('user', [
                'store' => $this->store,
            ]);
        };
    }


}