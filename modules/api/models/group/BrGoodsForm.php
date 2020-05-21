<?php
/**
 * Created by PhpStorm.
 * User: peize
 * Date: 2017/11/27
 * Time: 9:32
 */

namespace app\modules\api\models\group;

use app\models\Article;
use app\models\Order;
use app\models\BrGoods;
use app\models\BrGoodsPic;
use app\models\BrOrder;
use app\models\BrOrderComment;
use app\models\BrOrderDetail;
use app\models\User;
use app\modules\api\models\ApiModel;
use yii\data\Pagination;

class BrGoodsForm extends ApiModel
{
	public $page = 0;
	public $store_id;

	public $user_id;

	public $gid;

	public $limit;


	/**
	 * @return array
	 * 拼团商品列表
	 */
	public function getList()
	{
		$page = \Yii::$app->request->get('page')?:1;
		$limit = (int)\Yii::$app->request->get('limit')?:10;
		$cid = \Yii::$app->request->get('cid');    
		$query = BrGoods::find()
			->andWhere(['is_delete' => 0, 'store_id' => $this->store_id, 'status' => 1])
			->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]]);
		if ((int)$cid) {
			// 分类
			$query->andWhere(['cat_id'=>$cid]);
		} else {
			// 热销
			$query->andWhere(['is_hot'=>1]);
		}    
		$count = $query->count();
		$p = new Pagination(['totalCount' => $count, 'pageSize' => $limit, 'page' => $page - 1]);
		$list = $query
			->offset($p->offset)
			->limit($p->limit)
			->orderBy('sort ASC')
			->asArray()
			->all();
//        var_dump($list);exit;
//		foreach ($list as $key => $goods) {
//			$BrGoodsFind = BrGoods::find()->where(['id'=>$goods['id']])->one();
//			$list[$key]['virtual_sales'] += $BrGoodsFind->getSalesVolume();
//		}
		

		return [
			'row_count'     => intval($count),
			'page_count'    => intval($p->pageCount),
			'page'          => intval($page),
			'list'          => $list,
		];
	}


	/**
	 * @return array
	 * 关键字搜索
	 */
	public function search()
	{
		$page = \Yii::$app->request->get('page')?:1;
		$limit = (int)\Yii::$app->request->get('limit')?:4;
		$keyword = \Yii::$app->request->get('keyword');

		if (empty($keyword)) {
			return;
		}


		$query = BrGoods::find()
			->andWhere(['is_delete' => 0, 'store_id' => $this->store_id, 'status' => 1])
			->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]]);

		$query->andWhere(['like','name',$keyword]);

		$count = $query->count();     
		$p = new Pagination(['totalCount' => $count, 'pageSize' => $limit, 'page' => $page - 1]);
		$list = $query
			->select('id,name,original_price,price,cover_pic,unit,cat_id,virtual_sales')
			->offset($p->offset)
			->limit($p->limit)
			->orderBy('sort ASC')
			->asArray()
			->all();
		
		return [
			'row_count'     => intval($count),
			'page_count'    => intval($p->pageCount),
			'page'          => intval($page),
			'list'          => $list,
		];
	}


	/**
	 * @return mixed|string
	 * 拼团商品详情
	 */
	public function getInfo()
	{
		$info = BrGoods::find()
			->andWhere(['is_delete'=>0,'store_id'=>$this->store_id,'status'=>1,'id'=>$this->gid])
			->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]])
			->asArray()
			->one();
		$goods = BrGoods::find()
			->andWhere(['is_delete'=>0,'store_id'=>$this->store_id,'status'=>1,'id'=>$this->gid])
			->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]])
			->one();
		
		if (!$info) {
			return [
				'code'  => 1,
				'msg'   => '商品不存在或已下架',
			];
		}
		$info['pic_list'] = BrGoodsPic::find()
			->select('pic_url')
			->andWhere(['goods_id'=>$this->gid,'is_delete'=>0])
			->column();

		$info['attr'] = json_decode($info['attr'], true);
		$info['service'] = explode(',', $info['service']);
		$attr_group_list = $goods->getAttrGroupList();

		$comment = BrOrderComment::find()
			->alias('c')
			->select([
				'c.score','c.content','c.pic_list','c.addtime',
				'u.nickname','u.avatar_url',
				'od.attr'
			])
			->andWhere(['c.store_id'=>$this->store_id,'c.goods_id'=>$info['id'],'c.is_delete'=>0,'c.is_hide'=>0])
			->leftJoin(['u'=>User::tableName()], 'u.id = c.user_id')
			->leftJoin(['od'=>BrOrderDetail::tableName()], 'od.id=c.order_detail_id')
			->orderBy('c.addtime DESC')
			->limit(2)
			->asArray()
			->all();
		
		foreach ($comment as $k => $v) {
			$comment[$k]['attr'] = json_decode($v['attr'], true);
			$comment[$k]['pic_list'] = json_decode($v['pic_list'], true);
			$comment[$k]['addtime'] = date('m月d日', $v['addtime']);
			$comment[$k]['nickname'] = $this->substr_cut($v['nickname']);
		}
		if (empty($comment)) {
			$comment = false;
		}

		$goods = BrGoods::findOne([
			'id' => $this->gid,
			'is_delete' => 0,
			'status' => 1,
			'store_id' => $this->store_id,
		]);
		$info['num'] = $goods->getNum($goods->id);
		return [
			'code'  => 0,
			'msg'   => 'success',
			'data'  => [
				'info' => $info,
				'attr_group_list' => $attr_group_list,
				'comment'      => $comment,
				'commentNum'      => BrOrderComment::getCount($info['id'], $this->store_id),
			],
		];
	}

	/**
	 * @return array
	 * 评论列表
	 */
	public function comment()
	{
		$query = BrOrderComment::find()
			->alias('c')
			->select([
				'c.score','c.content','c.pic_list','c.addtime',
				'u.nickname','u.avatar_url',
				'od.attr'
			])
			->andWhere(['c.store_id'=>$this->store_id,'c.goods_id'=>$this->gid,'c.is_delete'=>0,'c.is_hide'=>0])
			->leftJoin(['u'=>User::tableName()], 'u.id = c.user_id')
			->leftJoin(['od'=>BrOrderDetail::tableName()], 'od.id=c.order_detail_id');
		$count = $query->count();
		$pagination = new Pagination(['totalCount' => $count, 'page' => $this->page, 'pageSize' => 20]);
		
		$comment = $query
			->limit($pagination->limit)
			->offset($pagination->offset)
			->orderBy('c.addtime DESC')
			->asArray()
			->all();
		foreach ($comment as $k => $v) {
			$comment[$k]['attr']        = json_decode($v['attr'], true);
			$comment[$k]['pic_list']    = json_decode($v['pic_list'], true);
			$comment[$k]['addtime']     = date('m月d日', $v['addtime']);
			$comment[$k]['nickname']    = $this->substr_cut($v['nickname']);
		}
		return [
			'code' => 0,
			'msg' => 'success',
			'data' => [
				'row_count' => $count,
				'page_count' => $pagination->pageCount,
				'comment' => $comment,
			],
		];
	}

	// 将用户名 做隐藏
	private function substr_cut($user_name)
	{
		$strlen     = mb_strlen($user_name, 'utf-8');
		$firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
		$lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
		return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
	}
}
