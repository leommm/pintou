<?php

namespace app\modules\api\controllers\bargain;

use app\modules\api\controllers\Controller;
use app\modules\api\models\group\BrCatForm;
use app\modules\api\models\group\BrGoodsForm;
use app\models\BrCat;

class IndexController extends Controller
{
	/**
	 * @return mixed|string
	 * 拼团首页
	 */
	public function actionIndex()
	{
		// 获取导航分类
//        $this->store_id=\Yii::$app->request->get('store_id');
		$cat = BrCat::find()
			->select('id,name')
			->andWhere(['is_delete'=>0,'store_id'=>$this->store_id])
//			->andWhere(['is_delete'=>0,'store_id'=>2])      //需换上一句
			->orderBy('sort ASC')
			->asArray()
			->all();

		$brGoods = new BrGoodsForm();
		$brGoods->store_id = $this->store_id;
		$brGoods->user_id = \Yii::$app->user->id;
//		$brGoods->store_id = 2;  //需换上一句
//		$brGoods->user_id = 5;   //需换上一句
		$goods = $brGoods->getList();

		$data = array(
			'cat'     => $cat,
			'goods'   => $goods
		);
		return new \app\hejiang\ApiResponse(0, 'success', $data);
	}

	/**
	 * @return mixed|string
	 * 数据加载
	 */
	public function actionGoodList()
	{              
		$BrGoods = new BrGoodsForm();
		$BrGoods->store_id = $this->store_id;
		$BrGoods->user_id = \Yii::$app->user->id;
		$goods = $BrGoods->getList();
		return new \app\hejiang\ApiResponse(0, 'success', $goods);
	}

	/**
	/**
	 * @return string
	 * 搜索
	 */
	public function actionSearch()
	{
		$BrGoods = new BrGoodsForm();
//		$BrGoods->store_id = $this->store_id;
		$BrGoods->store_id = 2;
//		$BrGoods->user_id = \Yii::$app->user->id;
		$BrGoods->user_id = 5;
		$goods = $BrGoods->search();       echo json_encode($goods);die;
		return new \app\hejiang\ApiResponse(0, 'success', $goods);
	}

	/**     * @param int $gid
	 * @return mixed|string
	 * 商品详情
	 */
	public function actionGoodDetails($gid = 0)
	{
		$BrGoods = new BrGoodsForm();
		$BrGoods->store_id = $this->store_id;
		$BrGoods->gid = $gid;
		$BrGoods->user_id = \Yii::$app->user->id;
		return new \app\hejiang\BaseApiResponse($BrGoods->getInfo());
	}
	/**
	 * @param int $gid
	 * @return mixed|string
	 * 商品评价
	 */
	public function actionGoodsComment($gid = 0, $page = 0)
	{
		$BrGoods = new BrGoodsForm();
		$BrGoods->store_id = $this->store_id;
		$BrGoods->gid = $gid;
		$BrGoods->page = $page;
		$BrGoods->user_id = \Yii::$app->user->id;
		return new \app\hejiang\BaseApiResponse($BrGoods->comment());
	}

	public function actionGoodsAttrInfo()
	{
		$form = new BrGoodsAttrInfoForm();
		$form->attributes = \Yii::$app->request->get();
		return new \app\hejiang\BaseApiResponse($form->search());
	}

	//获取商品二维码海报
	public function actionGoodsQrcode()
	{
		$form = new ShareQrcodeForm();
		$form->attributes = \Yii::$app->request->get();
		$form->store_id = $this->store_id;
		$form->type = 2;
		if (!\Yii::$app->user->isGuest) {
			$form->user = \Yii::$app->user->identity;
			$form->user_id = \Yii::$app->user->id;
		}
		return new \app\hejiang\BaseApiResponse($form->search());
	}
}