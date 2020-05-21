<?php
namespace app\modules\mch\controllers;
/**
 * Created by PhpStorm.
 * User: wch
 * Date: 2018/11/29
 * Time: 11:16
 */
use app\models\User;
use app\models\Share;
use app\models\Order;
use app\models\Goods;
use app\models\Mch;
use app\models\OrderDetail;
use app\modules\mch\models\StoreDataForm;
use yii\data\Pagination;
use app\modules\mch\models\TenantForm;
class TenantController extends Controller
{


    /*
     * 总代统计
     * */
    public function actionGeneral($page=1)
    {
        $zd=Share::find()->where('general_id = 1')->count();
        $order=Order::find()->sum('general_price');
        $ordernum=Order::find()->where('general_price != 0.00 ')->count();
        $query=Order::find()->alias('a')->leftJoin(['u'=>User::tableName()],'a.general_id = u.id')
            ->leftJoin(['d'=>OrderDetail::tableName()],'a.id = d.order_id')
            ->leftJoin(['g'=>Goods::tableName()],'d.goods_id = g.id')
            ->select('a.general_price,d.goods_id,u.nickname,g.name,a.total_price')
            ->orderBy('d.addtime desc')
            ->where('a.general_price != 0');
        $count = $query->count();
        $list=$query ->asArray()->all();

        $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);

        if (\Yii::$app->request->isAjax) {
            $form = new StoreDataForm();
            $form->store_id = $this->store->id;
            $form->sign = \Yii::$app->request->get('sign');
            $form->type = \Yii::$app->request->get('type');
            $store_data = $form->search();
            return $store_data;
        } else {
            return $this->render('general', [
                'store' => $this->store,
                'zd'=>$zd,'order'=>$order,
                'ordernum'=>$ordernum,
                'pagination'=>$pagination,
                'row_count'=>$count,
                'list' => $list,
            ]);
        };

    }

    /*
  * 入驻商统计
  * */
    public function actionEnter()
    {


        if (\Yii::$app->request->isAjax) {
            $form = new TenantForm();
            $form->store_id = $this->store->id;
            $form->sign = \Yii::$app->request->get('sign');
            $form->type = \Yii::$app->request->get('type');
            $store_data = $form->search3();

            return $store_data;
        } else {
            return $this->render('enter', [
                'store' => $this->store,

            ]);
        };

    }



}