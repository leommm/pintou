<?php

namespace app\modules\mch\controllers\group;

use app\models\PtCat;
use app\models\CmCat;
use app\models\BrCat;
use app\models\SpCat;
use app\models\FtCat;
use app\models\PtOrder;
use app\models\CmOrder;
use app\models\SpOrder;
use app\models\BrOrder;
use app\models\PtGoodsDetail;
use app\models\CmGoodsDetail;
use app\models\SpGoodsDetail;
use app\models\FtGoodsDetail;
use app\models\Express;
use app\modules\mch\models\ExportList;
use app\modules\mch\models\group\PtGoodsForm;
use app\modules\mch\models\group\PtOrderForm;
use app\modules\mch\models\group\OrderRefundListForm;
use app\modules\mch\models\group\CmGoodsForm;
use app\modules\mch\models\group\CmOrderForm;
use app\modules\mch\models\group\OrderRefundListForms;
use app\modules\mch\models\group\BrGoodsForm;
use app\modules\mch\models\group\BrOrderForm;
use app\modules\mch\models\group\SpGoodsForm;
use app\modules\mch\models\group\SpOrderForm;
use app\modules\mch\models\group\FtGoodsForm;
use app\modules\mch\models\group\FtOrderForm;

class MchController extends Controller
{
	/**
	 * @return string
	 * 商品列表
	 */
	public function actionGoods()
	{
		$form = new PtGoodsForm();
		$arr = $form->getList($this->store->id, -1);   
		
		foreach ($arr[0] as $k => $v) {
			$ladder_num = $v['group_num'];
			$list = PtGoodsDetail::find()->select('*')->where(['store_id' => $this->store->id])->andWhere('goods_id=:goods_id', [':goods_id' => $v['id']])->all();

			foreach ($list as $v1) {
				$ladder_num = $ladder_num . '|' . $v1->group_num;
			}
			$arr[0][$k]['ladder_num'] = $ladder_num;
		};
		
		$cat_list = PtCat::find()->select('id,name')->andWhere(['store_id' => $this->store->id, 'is_delete' => 0])->orderBy('sort ASC')->asArray()->all();
		return $this->render('goods', [
			'list' => $arr[0],
			'pagination' => $arr[1],
			'cat_list' => $cat_list,
		]);
	}

	/**
	 * @return string
	 * 拼团订单列表
	 */
	public function actionOrder($offline = null)
	{
		$form = new PtOrderForm();   
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		if ($offline != null) {
			$form->offline = $offline;
		}
		$form->store_id = $this->store->id;
		$form->mch_id = -1;
		$arr = $form->getList();
		if ($arr === null) {
			return null;
		}

		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$exportList = $f->getList();

		return $this->render('order', [
			'list' => $arr['list'],
			'pagination' => $arr['p'],
			'express_list' => $this->getExpressList(),
			'row_count' => $arr['row_count'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	//售后订单列表
	public function actionRefund()
	{
		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$f->type = 1;
		$exportList = $f->getList();
		$form = new OrderRefundListForm();
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		$form->store_id = $this->store->id;
		$form->mch_id = -1;
		$form->limit = 10;
		$data = $form->search();

		return $this->render('refund', [
			'row_count' => $data['row_count'],
			'pagination' => $data['pagination'],
			'list' => $data['list'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	public function actionSpGoods()
	{
		$form = new SpGoodsForm();
		$arr = $form->getList($this->store->id, -1);

		foreach ($arr[0] as $k => $v) {
			$ladder_num = $v['group_num'];
			$list = SpGoodsDetail::find()->select('*')->where(['store_id' => $this->store->id])->andWhere('goods_id=:goods_id', [':goods_id' => $v['id']])->all();

			foreach ($list as $v1) {
				$ladder_num = $ladder_num . '|' . $v1->group_num;
			}
			$arr[0][$k]['ladder_num'] = $ladder_num;
		};

		$cat_list = SpCat::find()->select('id,name')->andWhere(['store_id' => $this->store->id, 'is_delete' => 0])->orderBy('sort ASC')->asArray()->all();
		return $this->render('spgoods', [
			'list' => $arr[0],
			'pagination' => $arr[1],
			'cat_list' => $cat_list,
		]);
	}

	public function actionSpOrder($offline = null)
	{
		$form = new SpOrderForm();
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		if ($offline != null) {
			$form->offline = $offline;
		}
		$form->store_id = $this->store->id;
		$arr = $form->getList(-1);
		if ($arr === null) {
			return null;
		}

		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$exportList = $f->getList();

		return $this->render('sporder', [
			'list' => $arr['list'],
			'pagination' => $arr['p'],
			'express_list' => $this->getExpressListSp(),
			'row_count' => $arr['row_count'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	//售后订单列表
	public function actionSpRefund()
	{
		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$f->type = 1;
		$exportList = $f->getList();
		$form = new OrderRefundListFormSp();
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		$form->store_id = $this->store->id;
		$form->limit = 10;
		$data = $form->search(-1);

		return $this->render('sprefund', [
			'row_count' => $data['row_count'],
			'pagination' => $data['pagination'],
			'list' => $data['list'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	public function actionFtGoods()
	{
		$form = new FtGoodsForm();
		$arr = $form->getList($this->store->id, -1);

		foreach ($arr[0] as $k => $v) {
			$ladder_num = $v['group_num'];
			$list = FtGoodsDetail::find()->select('*')->where(['store_id' => $this->store->id])->andWhere('goods_id=:goods_id', [':goods_id' => $v['id']])->all();

			foreach ($list as $v1) {
				$ladder_num = $ladder_num . '|' . $v1->group_num;
			}
			$arr[0][$k]['ladder_num'] = $ladder_num;
		};

		$cat_list = FtCat::find()->select('id,name')->andWhere(['store_id' => $this->store->id, 'is_delete' => 0])->orderBy('sort ASC')->asArray()->all();
		return $this->render('ftgoods', [
			'list' => $arr[0],
			'pagination' => $arr[1],
			'cat_list' => $cat_list,
		]);
	}

	public function actionFtOrder($offline = null)
	{
		$form = new FtOrderForm();
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		if ($offline != null) {
			$form->offline = $offline;
		}
		$form->store_id = $this->store->id;
		$arr = $form->getList(-1);
		if ($arr === null) {
			return null;
		}

		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$exportList = $f->getList();

		return $this->render('ftorder', [
			'list' => $arr['list'],
			'pagination' => $arr['p'],
			'express_list' => $this->getExpressListFt(),
			'row_count' => $arr['row_count'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	//售后订单列表
	public function actionFtRefund()
	{
		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$f->type = 1;
		$exportList = $f->getList();
		$form = new OrderRefundListForms();
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		$form->store_id = $this->store->id;
		$form->limit = 10;
		$data = $form->search(-1);

		return $this->render('cmrefund', [
			'row_count' => $data['row_count'],
			'pagination' => $data['pagination'],
			'list' => $data['list'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	public function actionCmGoods()
	{   
		$form = new CmGoodsForm();
		$arr = $form->getList($this->store->id, -1);

		foreach ($arr[0] as $k => $v) {
			$ladder_num = $v['group_num'];
			$list = CmGoodsDetail::find()->select('*')->where(['store_id' => $this->store->id])->andWhere('goods_id=:goods_id', [':goods_id' => $v['id']])->all();

			foreach ($list as $v1) {
				$ladder_num = $ladder_num . '|' . $v1->group_num;
			}
			$arr[0][$k]['ladder_num'] = $ladder_num;
		};

		$cat_list = CmCat::find()->select('id,name')->andWhere(['store_id' => $this->store->id, 'is_delete' => 0])->orderBy('sort ASC')->asArray()->all();
		return $this->render('cmgoods', [
			'list' => $arr[0],
			'pagination' => $arr[1],
			'cat_list' => $cat_list,
		]);
	}

	public function actionCmOrder($offline = null)
	{
		$form = new CmOrderForm();
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		if ($offline != null) {
			$form->offline = $offline;
		}
		$form->store_id = $this->store->id;
		$arr = $form->getList(-1);
		if ($arr === null) {
			return null;
		}

		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$exportList = $f->getList();

		return $this->render('cmorder', [
			'list' => $arr['list'],
			'pagination' => $arr['p'],
			'express_list' => $this->getExpressListCm(),
			'row_count' => $arr['row_count'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	//售后订单列表
	public function actionCmRefund()
	{
		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$f->type = 1;
		$exportList = $f->getList();
		$form = new OrderRefundListForms();
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		$form->store_id = $this->store->id;
		$form->limit = 10;
		$data = $form->search(-1);         

		return $this->render('cmrefund', [
			'row_count' => $data['row_count'],
			'pagination' => $data['pagination'],
			'list' => $data['list'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	public function actionBrGoods()
	{
		$form = new BrGoodsForm();
		$arr = $form->getList($this->store->id, -1);     

		$cat_list = BrCat::find()->select('id,name')->andWhere(['store_id' => $this->store->id, 'is_delete' => 0])->orderBy('sort ASC')->asArray()->all();
		return $this->render('brgoods', [
			'list' => $arr[0],
			'pagination' => $arr[1],
			'cat_list' => $cat_list,
		]);
	}

	public function actionBrOrder($offline = null)
	{
		$form = new BrOrderForm();  
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		if ($offline != null) {
			$form->offline = $offline;
		}
		$form->store_id = $this->store->id;
		$arr = $form->getList(-1);
		if ($arr === null) {
			return null;
		}
		
		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$exportList = $f->getList();
		
		return $this->render('brorder', [
			'list' => $arr['list'],
			'pagination' => $arr['p'],
			'express_list' => $this->getExpressListBr(),
			'row_count' => $arr['row_count'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	//售后订单列表
	public function actionBrRefund()
	{
		// 获取可导出数据
		$f = new ExportList();
		$f->order_type = 2;
		$f->type = 1;
		$exportList = $f->getList();
		$form = new OrderRefundListFormss();
		$form->attributes = \Yii::$app->request->get();
		$form->attributes = \Yii::$app->request->post();
		$form->store_id = $this->store->id;
		$form->limit = 10;
		$data = $form->search(-1);

		return $this->render('brrefund', [
			'row_count' => $data['row_count'],
			'pagination' => $data['pagination'],
			'list' => $data['list'],
			'exportList' => \Yii::$app->serializer->encode($exportList)
		]);
	}

	// 快递列表
	private function getExpressList()
	{
		$storeExpressList = PtOrder::find()
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

	private function getExpressListBr()
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

	private function getExpressListCm()
	{
		$storeExpressList = CmOrder::find()
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