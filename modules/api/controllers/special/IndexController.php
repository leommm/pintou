<?php

namespace app\modules\api\controllers\community;

use app\modules\api\controllers\Controller;
use app\models\CmCat;
use app\modules\api\models\group\CmGoodsForm;

class IndexController extends Controller
{
	/**
		社区团购列表
	 **/
	public function actionIndex()
	{
		// 获取导航分类
		$cat = CmCat::find()
			->select('id,name')
			->andWhere(['is_delete'=>0,'store_id'=>$this->store_id])
			->orderBy('sort ASC')
			->asArray()
			->all();

		$cmGoods = new CmGoodsForm();
		$cmGoods->store_id = $this->store_id;
		$cmGoods->user_id = \Yii::$app->user->id;
		$goods = $cmGoods->getList();    

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
		$cmGoods = new CmGoodsForm();
		$cmGoods->store_id = $this->store_id;
		$cmGoods->user_id = \Yii::$app->user->id;
		$goods = $cmGoods->getList();
		return new \app\hejiang\ApiResponse(0, 'success', $goods);
	}

	/**
	 * @return string
	 * 搜索
	 */
	public function actionSearch()
	{
		$cmGoods = new CmGoodsForm();
		$cmGoods->store_id = $this->store_id;
		$cmGoods->user_id = \Yii::$app->user->id;
		$goods = $cmGoods->search();
		return new \app\hejiang\ApiResponse(0, 'success', $goods);
	}

	/**     * @param int $gid
	 * @return mixed|string
	 * 商品详情
	 */
	public function actionGoodDetails($gid = 0)
	{
		$ptGoods = new CmGoodsForm();
		$ptGoods->store_id = $this->store_id;
		$ptGoods->gid = $gid;
		$ptGoods->user_id = \Yii::$app->user->id;
		return new \app\hejiang\BaseApiResponse($ptGoods->getInfo());
	}

	/**
	 * @param int $gid
	 * @return mixed|string
	 * 商品评价
	 */
	public function actionGoodsComment($gid = 0, $page = 0)
	{
		$ptGoods = new CmGoodsForm();
		$ptGoods->store_id = $this->store_id;
		$ptGoods->gid = $gid;
		$ptGoods->page = $page;
		$ptGoods->user_id = \Yii::$app->user->id;
		return new \app\hejiang\BaseApiResponse($ptGoods->comment());
	}

	public function actionGoodsAttrInfo()
	{
		$form = new CmGoodsAttrInfoForm();
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