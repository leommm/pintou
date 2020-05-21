<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 11:07
 */

namespace app\modules\mch\controllers\mch;

use app\models\Order;
use app\modules\mch\controllers\Controller;
use app\modules\mch\models\ExportList;
use app\modules\mch\models\mch\OrderListForm;
use app\modules\mch\models\OrderDetailForm;

class OrderController extends Controller
{
    public function actionIndex()
    {
        $form = new OrderListForm();
        $form->store_id = $this->store->id;
        $form->attributes = \Yii::$app->request->get();
        $form->attributes = \Yii::$app->request->post();
        $data = $form->search();

        // 获取可导出数据
        $f = new ExportList();
        $exportList = $f->getList();
        return $this->render('index', [
            'row_count' => $data['row_count'],
            'pagination' => $data['pagination'],
            'list' => $data['list'],
            'exportList' => \Yii::$app->serializer->encode($exportList)
        ]);
    }

    public function actionDetail()
    {
        $form = new OrderDetailForm();
        $form->store_id = $this->store->id;
        $form->order_id = \Yii::$app->request->get('order_id');
        $arr = $form->search();
        $arr['is_update'] = false;
        return $this->render('/order/detail', $arr);
    }
}
