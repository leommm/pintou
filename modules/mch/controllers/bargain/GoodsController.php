<?php

namespace app\modules\mch\controllers\bargain;

use app\models\BrCat;
use app\models\BrGoods;
use app\models\GoodsShare;
use app\models\PostageRules;
use app\modules\mch\controllers\Controller;
use app\modules\mch\models\group\BrGoodsForm;
use app\modules\mch\models\group\BrCatForm;

class GoodsController extends Controller
{
	public function actionIndex()
	{
		$form = new BrGoodsForm();

		$arr = $form->getList($this->store->id);

		$cat_list = BrCat::find()->select('id,name')->andWhere(['store_id' => $this->store->id, 'is_delete' => 0])->orderBy('sort ASC')->asArray()->all();
		
		return $this->render('index', [
			'list' => $arr[0],
			'pagination' => $arr[1],
			'cat_list' => $cat_list,
		]);
	}

	/**
	 * @param int $id
	 * @return mixed|string
	 * 编辑拼团商品
	 */
	public function actionGoodsEdit($id = 0)
	{
		$goods = BrGoods::findOne(['id' => $id, 'is_delete' => 0, 'store_id' => $this->store->id]);
		$goods_share = GoodsShare::findOne(['store_id' => $this->store->id, 'goods_id' => $id, 'type' => 4]);
		if (!$goods) {
			$goods = new BrGoods();
		}
		if (!$goods_share) {
			$goods_share = new GoodsShare();
		}
		if (\Yii::$app->request->isPost) {
			$data = \Yii::$app->request->post();
			$model = $data['model'];
			$fav_time = explode(" - ",$data['fav_limit']);
			$date_start = strtotime($fav_time[0]);
			$date_end = strtotime($fav_time[1]);
			$bot_amount = $data['bot_amount'];
			$top_amount = $data['top_amount'];
			$br_lv = $data['br_lv'];
			$qj_content = [];
			$total_lv = 0;
			foreach ($bot_amount as $k => $v)
			{
				$arr = [
					'bot_amount' => $v,
					'top_amount' => $top_amount[$k],
					'br_lv' => $br_lv[$k]
				];
				$total_lv += $br_lv[$k];
				$qj_content[] = $arr;
			}
			if ($total_lv != 100)
			{
				return [
					'code' => 1,
					'msg' => '区间概率总和需等于100'
				];
			}
			$qj_content = serialize($qj_content);
			$model['store_id'] = $this->store->id;
			$model['mch_id'] = 0;
			$model['date_start'] = $date_start;
			$model['date_end'] = $date_end;
			$model['qj_content'] = $qj_content;
			$model['limit_time'] = $model['limit_time'] ? $model['limit_time'] : 0;
			$form = new BrGoodsForm();
			$form->attributes = $model;
			$form->attr = \Yii::$app->request->post('attr');
			$form->goods = $goods;
			$form->goods_share = $goods_share;
			return $form->save();
		}
		$ptCat = BrCat::find()
			->andWhere(['is_delete' => 0, 'store_id' => $this->store->id, 'mch_id' => 0])
			->asArray()
			->orderBy('sort ASC')
			->all();

		$postageRiles = PostageRules::find()->where(['store_id' => $this->store->id, 'is_delete' => 0])->all();
		foreach ($goods as $index => $value) {
			if (in_array($index, ['attr', 'full_cut', 'integral', 'payment', 'detail'])) {
				continue;
			}
			if (is_array($value) || is_object($value)) {
				continue;
			}
			if ($index != 'qj_content')
			{
				$goods[$index] = str_replace("\"", "&quot;", $value);
			}else{
				$goods[$index] = unserialize($goods['qj_content']);
			}
		}
		
		return $this->render('goods-edit', [
			'goods' => $goods,
			'cat' => $ptCat,
			'postageRiles' => $postageRiles,
			'goods_share' => $goods_share,
		]);
	}

	/**
	 * @param int $id
	 * @param string $type
	 * 上架、下架、设置热销、取消热销
	 */
	public function actionGoodsUpDown($id = 0, $type = 'down')
	{
		if ($type == 'down') {
			$goods = BrGoods::findOne(['id' => $id, 'is_delete' => 0, 'status' => 1, 'store_id' => $this->store->id]);
			if (!$goods) {
				return [
					'code' => 1,
					'msg' => '商品已删除或已下架',
				];
			}
			$goods->status = 2;
		} elseif ($type == 'up') {
			$goods = BrGoods::findOne(['id' => $id, 'is_delete' => 0, 'status' => 2, 'store_id' => $this->store->id]);
			if (!$goods) {
				return [
					'code' => 1,
					'msg' => '商品已删除或已上架',
				];
			}
			if (!$goods->goods_num) {
				return [
					'code' => 1,
					'msg' => '商品库存不足，请先完善商品库存',
//					'return_url' => \Yii::$app->urlManager->createUrl(['user/bargain/goods/goods-attr', 'id' => $goods->id]),
				];
			}
			$goods->status = 1;
		} elseif ($type == 'hot') { // 设置热销
			$goods = BrGoods::findOne(['id' => $id, 'is_delete' => 0, 'is_hot' => 0, 'store_id' => $this->store->id]);
			if (!$goods) {
				return [
					'code' => 1,
					'msg' => '商品已删除或已设为热销',
				];
			}
			if (!$goods->goods_num) {
				return [
					'code' => 1,
					'msg' => '商品库存不足，请先完善商品库存',
//					'return_url' => \Yii::$app->urlManager->createUrl(['user/bargain/goods/goods-attr', 'id' => $goods->id]),
				];
			}
			$goods->is_hot = 1;
		} elseif ($type == 'nohot') { // 取消热销
			$goods = BrGoods::findOne(['id' => $id, 'is_delete' => 0, 'is_hot' => 1, 'store_id' => $this->store->id]);
			if (!$goods) {
				return [
					'code' => 1,
					'msg' => '商品已删除或已取消热销',
				];
			}
			$goods->is_hot = 0;
		} else {
			return [
				'code' => 1,
				'msg' => '参数错误',
			];
		}
		if ($goods->save()) {
			return [
				'code' => 0,
				'msg' => '成功',
			];
		} else {
			foreach ($goods->errors as $errors) {
				return [
					'code' => 1,
					'msg' => $errors[0],
				];
			}
		}
	}

	/**
	 * @param int $id
	 * 拼团商品删除（逻辑删除）
	 */
	public function actionGoodsDel($id = 0)
	{
		$goods = BrGoods::findOne(['id' => $id, 'is_delete' => 0, 'store_id' => $this->store->id]);
		if (!$goods) {
			return [
				'code' => 1,
				'msg' => '商品删除失败或已删除',
			];
		}
		$goods->is_delete = 1;
		if ($goods->save()) {
			return [
				'code' => 0,
				'msg' => '成功',
			];
		} else {
			foreach ($goods->errors as $errors) {
				return [
					'code' => 1,
					'msg' => $errors[0],
				];
			}
		}
	}

	public function actionCat()
	{
		$form = new BrCatForm();
		$arr = $form->getList($this->store->id);
		return $this->render('cat', [
			'list' => $arr[0],
			'pagination' => $arr[1],
		]);
	}

	/**
	 * @param int $id
	 * @return mixed|string
	 * 修改拼团商品分类
	 */
	public function actionCatEdit($id = 0)
	{
		$cat = BrCat::findOne(['id' => $id, 'is_delete' => 0, 'store_id' => $this->store->id]);
		if (!$cat) {
			$cat = new BrCat();
		}
		if (\Yii::$app->request->isPost) {
			$model = \Yii::$app->request->post('model');
			$model['store_id'] = $this->store->id;
			$model['mch_id'] = 0;
			$form = new BrCatForm();
			$form->attributes = $model;
			$form->cat = $cat;
			return $form->save();
		}
		foreach ($cat as $index => $value) {
			$cat[$index] = str_replace("\"", "&quot;", $value);
		}
		return $this->render('cat-edit', [
			'list' => $cat,
		]);
	}

	/**
	 * @param int $id
	 * @return mixed|string
	 * 拼团商品分类删除
	 */
	public function actionCatDel($id = 0)
	{
		$cat = BrCat::findOne(['id' => $id, 'is_delete' => 0, 'store_id' => $this->store->id]);
		if (!$cat) {
			return [
				'code' => 1,
				'msg' => '分类不存在或已删除',
			];
		}

		$cat->is_delete = 1;
		if ($cat->save()) {
			return [
				'code' => 0,
				'msg' => '删除成功',
			];
		} else {
			return [
				'code' => 1,
				'msg' => '删除失败',
			];
		}
	}

	/**
	 * 拼团商品批量操作
	 */
	public function actionBatch()
	{
		$get = \Yii::$app->request->get();
		$res = 0;
		$goods_group = $get['goods_group'];
		$goods_id_group = [];
		foreach ($goods_group as $index => $value) {
			if ($get['type'] == 0) {
				if ($value['num'] != 0) {
					array_push($goods_id_group, $value['id']);
				}
			} else {
				array_push($goods_id_group, $value['id']);
			}
		}

		$condition = ['and', ['in', 'id', $goods_id_group], ['store_id' => $this->store->id]];
		if ($get['type'] == 0) { //批量上架
			$res = BrGoods::updateAll(['status' => 1], $condition);
		} elseif ($get['type'] == 1) { //批量下架
			$res = BrGoods::updateAll(['status' => 2], $condition);
		} elseif ($get['type'] == 2) { //批量删除
			$res = BrGoods::updateAll(['is_delete' => 1], $condition);
		} elseif ($get['type'] == 3) { //批量设置热销
			$res = BrGoods::updateAll(['is_hot' => 1], $condition);
		} elseif ($get['type'] == 4) { //批量取消热销
			$res = BrGoods::updateAll(['is_hot' => 0], $condition);
		}
		if ($res > 0) {
			return [
				'code' => 0,
				'msg' => 'success',
			];
		} else {
			return [
				'code' => 1,
				'msg' => 'fail',
			];
		}
	}
}

