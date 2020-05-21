<?php

namespace app\modules\mch\controllers\bargain;

use app\models\Express;
use app\modules\mch\controllers\Controller;
use app\modules\mch\models\group\BrOrderForm;
use app\modules\mch\models\ExportList;
use app\models\BrOrder;

class OrderController extends Controller
{
	public function actionIndex($offline = null)
	{
		$form = new BrOrderForm();
		$form->attributes = \Yii::$app->request->get();   
		$form->attributes = \Yii::$app->request->post();
		if ($offline != null) {
			$form->offline = $offline;
		}
		$form->store_id = $this->store->id;
		$arr = $form->getList();
		if ($arr === null) {
			return null;
		}

		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$exportList = $f->getList();
		
		return $this->render('index', [
			'list' => $arr['list'],
			'pagination' => $arr['p'],
			'express_list' => $this->getExpressList(),
			'row_count' => $arr['row_count'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	private function getExpressList()
	{
		$storeExpressList = BrOrder::find()
			->select('express')
			->where([
				'AND',
				['store_id' => $this->store->id],
				['is_send' => 1],
				['!=', 'express', ''],
			])->groupBy('express')->orderBy('send_time DESC')->limit(5)->asArray()->all();
		$expressLst = Express::getExpressList();
		$newStoreExpressList = [];
		foreach ($storeExpressList as $i => $item) {
			foreach ($expressLst as $value) {
				if ($value['name'] == $item['express']) {
					$newStoreExpressList[] = $item['express'];
					break;
				}
			}
		}

		$newPublicExpressList = [];
		foreach ($expressLst as $i => $item) {
			$newPublicExpressList[] = $item['name'];
		}

		return [
			'private' => $newStoreExpressList,
			'public' => $newPublicExpressList,
		];
	}
}