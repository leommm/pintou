<?php

namespace app\modules\api\controllers\bargain;

use app\modules\api\behaviors\LoginBehavior;
use app\modules\api\controllers\Controller;

class BargainController extends Controller
{
	public function behaviors()
	{
		return array_merge(parent::behaviors(), [
			'login' => [
				'class' => LoginBehavior::className(),
			],
		]);
	}

	//砍价详情
	public function actionToDo()
	{
		$get = \Yii::$app->request->get();
		$goods_id = $get['goods_id'];
		$table = \Yii::$app->db;  
		$goods = $table->createCommand("select * from {{%br_goods}} where id = ".
			$goods_id." and is_delete = 0 and store_id = ".$this->store->id." and status = 1 limit 1")->queryOne();

		$doirg = $table->createCommand("select * from {{%bargainirg}} where goods_id = ".
			$goods_id." and user_id = ".\Yii::$app->user->id)->queryOne();

		$dolist = [];
		if ($doirg)
		{
			$dolist = $table->createCommand("select * from {{%bargain_list}} where br_id = ".$doirg['id'])->queryAll();
		}
		
		$data = [
			'goods' => $goods,
			'doirg' => $doirg ? $doirg : [],
			'dolist' => $dolist
		];
		return new \app\hejiang\ApiResponse(0, 'success', $data);
	}

	public function actionDoing()
	{
		$get = \Yii::$app->request->get();
		$goods_id = $get['goods_id'];

		$table = \Yii::$app->db;
		//砍价产品详情
		$brgain = $table->createCommand("select * from {{%br_goods}} where id = ".
			$goods_id." and is_delete = 0 and status = 1 and store_id = ".
			$this->store->id." and date_start <= ".time()." and date_end > ".time())->queryOne();
	
		//查看是否已存在此商品砍价记录
		$is_do = intval($table->createCommand("select count(1) from {{%bargainirg}} where goods_id = ".
			$goods_id." and store_id = ".$this->store->id)->queryScalar());

		if (!$brgain)
		{
			$data = [
				'code' => 1,
				'msg' => '此砍价商品已下架或已过期'
			];
			return new \app\hejiang\BaseApiResponse($data);
		}

		if (intval($brgain['fav_num']) <= $is_do)
		{
			$data = [
				'code' => 1,
				'msg' => '活动发起次数已达上限'
			];
			return new \app\hejiang\BaseApiResponse($data);
		}

		$insert = [
			'goods_id' => $goods_id,
			'user_id' => \Yii::$app->user->id,
			'br_count' => 0,
			'deal_money' => $brgain['original_price'],
			'create_time' => time(),
			'is_addorder' => 0,
			'store_id' => $this->store->id
		];
		
		$res = $table->createCommand()->insert("{{%bargainirg}}",$insert)->execute();
		if ($res)
		{
			$data = [
				'code' => 0,
				'msg' => '提交成功'
			];
		}else{
			$data = [
				'code' => 1,
				'msg' => '提交失败'
			];
		}
		return new \app\hejiang\BaseApiResponse($data);
	}

	//帮砍
	public function actionUserBar()
	{
		$get = \Yii::$app->request->get();
		$br_id = $get['br_id'];

		$table = \Yii::$app->db;
		//发起人订单详情
		$doirg = $table->createCommand("select * from {{%bargainirg}} where id = ".$br_id)->queryOne();

		//砍价产品详情
		$brgain = $table->createCommand("select * from {{%br_goods}} where id = ".$doirg['goods_id']." and store_id = ".$this->store->id)->queryOne();
		$buy_limit = intval($brgain['buy_limit']);

		//查看是否参与过此次砍价
		$is_do = intval($table->createCommand("select count(1) from {{%bargain_list}} where br_id = ".$br_id)->queryScalar());

		if (($doirg['br_count'] + 1) >= $brgain['total_num'])
		{
			$data = [
				'code' => 1,
				'msg' => '此商品帮砍次数已达上限'
			];
			return new \app\hejiang\BaseApiResponse($data);
		}

		if ($buy_limit <= $is_do)
		{   
			$data = [
				'code' => 1,
				'msg' => '您的帮砍次数已达上线'
			];
			return new \app\hejiang\BaseApiResponse($data);
		}

		if ($doirg)
		{
			$limit_time = intval($brgain['limit_time']);
			if ($limit_time > 0)
			{
				$end_time = $doirg['create_time'] + $limit_time * 3600;
				if ($end_time <= time())
				{
					$data =  [
						'code' => 1,
						'msg' => '此次砍价已超限定时间'
					];
					return new \app\hejiang\BaseApiResponse($data);
				}

				$qf = rand(1,100);
				$qj = unserialize($brgain['qj_content']);  	
				$qjz = 0;
				for ($i = 0;$i < count($qj);$i++)
				{
					$qjz += $qj[$i]['br_lv'];
					if ($i == 0)
					{
						$qjs = 0;
					}else{
						$qjs = $qj[$i - 1]['br_lv'];
					}
					if ($qf > $qjs && $qf <= $qjz)
					{
						$qzf = $i;
						break;
					}
				}

				$qjv = $qj[$qzf];
				$val = sprintf("%.1f",$qjv['bot_amount'] + mt_rand() / mt_getrandmax() * ($qjv['top_amount'] - $qjv['bot_amount']));
				if (($doirg['deal_money'] - $val) < $brgain['price'])
				{
					$val = $doirg['deal_money'] - $brgain['price'];
				}

				$insert = [
					'br_id' => $br_id,
					'assistor_id' => \Yii::$app->user->id,
					'create_time' => time(),
					'bargain_money' => $val
				];
				$transaction = $table->beginTransaction();
				try {
					$table->createCommand()->insert("{{%bargain_list}}",$insert)->execute();
					$table->createCommand("update {{%bargainirg}} set br_count = br_count + 1,deal_money = deal_money - ".$val." where id = ".$br_id)->execute();
					$transaction->commit();
					$data = [
						'code' => 0,
						'msg' => '帮砍成功',
						'val' => $val
					];
				}catch (Exception $e){
					$transaction->rollBack();
					$data = [
						'code' => 1,
						'msg' => '帮砍失败'
					];
				};
				return new \app\hejiang\BaseApiResponse($data);
			}
		}else{
			$data = [
				'code' => 1,
				'msg' => '无此砍价信息'
			];
			return new \app\hejiang\BaseApiResponse($data);
		}
	}
}