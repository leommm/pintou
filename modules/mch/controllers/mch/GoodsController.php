<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 16:30
 */

namespace app\modules\mch\controllers\mch;

use app\modules\mch\controllers\Controller;
use app\modules\mch\models\mch\GoodsDetailForm;
use app\modules\mch\models\mch\GoodsListForm;

class GoodsController extends Controller
{
    public function actionGoods()
    {
        $form = new GoodsListForm();
        $form->store_id = $this->store->id;
        $form->attributes = \Yii::$app->request->get();
//        $form->status=0;
//        var_dump($form);exit;
        $arr = $form->search();
        return $this->render('goods', $arr);
    }

    public function actionDetail()
    {
        $form = new GoodsDetailForm();
        $form->store_id = $this->store->id;
        $form->goods_id = \Yii::$app->request->get('goods_id');
        $arr = $form->search();
        return $this->render('detail', $arr);
    }
}
