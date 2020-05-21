<?php

namespace app\modules\api\controllers\bargain;

use app\models\Address;
use app\models\BrOrder;
use app\models\Store;
use app\models\User;
use app\models\WechatApp;
use app\modules\api\behaviors\LoginBehavior;
use app\modules\api\controllers\Controller;
use app\modules\api\models\group\OrderSubmitPreviewFormBr;
use app\modules\api\models\group\OrderSubmitFormBr;
use app\utils\PayNotify;

class OrderController extends Controller
{
	public $order;

	public function behaviors()
	{
		return array_merge(parent::behaviors(), [
			'login' => [
				'class' => LoginBehavior::className(),
			],
		]);
	}

	public function actionSubmitPreview()
	{
		$form = new OrderSubmitPreviewFormBr();
		$form->attributes = \Yii::$app->request->get();
		$form->store_id = $this->store->id;
		$form->user_id = \Yii::$app->user->id;
		return new \app\hejiang\BaseApiResponse($form->search());
	}

	//订单提交
	public function actionSubmit()
	{
		$post = \Yii::$app->request->post();
		$address = Address::findOne([
			'id' => $post['address_id'],
			'store_id' => $post['store_id'],
			'user_id' => \Yii::$app->user->id,
		]);
		if (!$address) {
			$data =  [
				'code' => 1,
				'msg' => '收货地址不存在',
			];
			return new \app\hejiang\BaseApiResponse($data);
		}

		if (!in_array($post['payment'], [0, 2, 3]) && $post['payment'] != null) {
			$data = [
				'code' => 1,
				'msg' => '请选择支付方式'
			];
			return new \app\hejiang\BaseApiResponse($data);
		}
		$table = \Yii::$app->db;
		//查看砍价信息
		$bargain = $table->createCommand("select * from {{%bargainirg}} where id = ".$post['br_id']." and is_addorder = 0")->queryOne();
		if (!$bargain)
		{
			$data = [
				'code' => 1,
				'msg' => '此商品已下单或不存在'
			];
			return new \app\hejiang\BaseApiResponse($data);
		}
		//查看砍价商品信息
		$goods = $table->createCommand("select * from {{%br_goods}} where id = ".$bargain['goods_id'])->queryOne();
		if ($goods['is_al_order'] > 0)
		{
			if ($bargain['deal_money'] > $goods['price'])
			{
				$data = [
					'code' => 1,
					'msg' => '此商品未到低价不允许下单'
				];
				return new \app\hejiang\BaseApiResponse($data);
			}
		}

		//插入订单
		$insert = [
			'store_id' => $goods['store_id'],
			'user_id' => \Yii::$app->user->id,
			'order_no' => date('YmdHis') . mt_rand(100000, 999999),
			'total_price' => $bargain['deal_money'],
			'pay_price' => $bargain['deal_money'],
			'name' => $address['name'],
			'mobile' => $address['mobile'],
			'address' => $address['province'] . $address['city'] . $address['district'] . $address['detail'],
			'pay_type' => $post['payment'],
			'addtime' => time(),
			'address_data' => json_encode([
				'province'=> $address['province'],
				'city' => $address['city'],
				'district' => $address['district'],
				'detail' => $address['detail']
			]),
			'shop_id' => $post['shop_id'],
			'mch_id' => $goods['mch_id']
		];
		
		$tran = $table->beginTransaction();
		try{
			$table->createCommand()->insert("{{%br_order}}",$insert)->execute();
			$order_id = $table->getLastInsertID();
			$dinsert = [
				'order_id' => $order_id,
				'goods_id' => $goods['id'],
				'num' => 1,
				'total_price' => $bargain['deal_money'],
				'addtime' => time(),
				'pic' => $goods['cover_pic'],
				'mch_id' => $goods['mch_id']
			];
			$table->createCommand()->insert("{{%br_order_detail}}",$dinsert)->execute();
			$table->createCommand("update {{%bargainirg}} set is_addorder = 1 where id = ".$post['br_id'])->query();
			$tran->commit();

			$wx_app_id = Store::findOne($post['store_id']);
			$weixin_config = WechatApp::findOne($wx_app_id['wechat_app_id']);
			if (isset(\Yii::$app->user->wechat_open_id))
			{
				$openid = \Yii::$app->user->wechat_open_id;
			}else{
				$user = User::findOne(\Yii::$app->user->id);
				$openid = $user['wechat_open_id'];
			}
			$wxpay = new WeixinPay($weixin_config['app_id'], $openid, $weixin_config['mch_id'], $weixin_config['key'],$insert['order_no'],$goods['name'],$insert['pay_price'] * 100);
			$payment = $wxpay->pay();
			$data = [
				'code' => 0,
				'msg' => '下单成功',
				'pay' => $payment
			];
		}catch (\Exception $e){
			$tran->rollBack();
			$data = [
				'code' => 1,
				'msg' => '下单失败'
			];
		}

		return new \app\hejiang\BaseApiResponse($data);
	}
}



/*
 * 小程序微信支付
 */


class WeixinPay {


	protected $appid;
	protected $mch_id;
	protected $key;
	protected $openid;
	protected $out_trade_no;
	protected $body;
	protected $total_fee;
	function __construct($appid, $openid, $mch_id, $key,$out_trade_no,$body,$total_fee) {
		$this->appid = $appid;
		$this->openid = $openid;
		$this->mch_id = $mch_id;
		$this->key = $key;
		$this->out_trade_no = $out_trade_no;
		$this->body = $body;
		$this->total_fee = $total_fee;
	}


	public function pay() {
		//统一下单接口
		$return = $this->weixinapp();
		return $return;
	}


	//统一下单接口
	private function unifiedorder() {
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$parameters = array(
			'appid' => $this->appid, //小程序ID
			'mch_id' => $this->mch_id, //商户号
			'nonce_str' => $this->createNoncestr(), //随机字符串
//            'body' => 'test', //商品描述
			'body' => $this->body,
//            'out_trade_no' => '2015450806125348', //商户订单号
			'out_trade_no'=> $this->out_trade_no,
//            'total_fee' => floatval(0.01 * 100), //总金额 单位 分
			'total_fee' => $this->total_fee,
//            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], //终端IP
			'spbill_create_ip' => '39.106.214.242', //终端IP
			'notify_url' => 'http://' . $_SERVER ['HTTP_HOST'] . '/notify.php', //通知地址  确保外网能正常访问
			'openid' => $this->openid, //用户id
			'trade_type' => 'JSAPI'//交易类型
		);
		//统一下单签名
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
		return $return;
	}


	private static function postXmlCurl($xml, $url, $second = 30)
	{
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);


		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);
		set_time_limit(0);


		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			throw new WxPayException("curl出错，错误码:$error");
		}
	}



	//数组转换成xml
	private function arrayToXml($arr) {
		$xml = "<root>";
		foreach ($arr as $key => $val) {
			if (is_array($val)) {
				$xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
			} else {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			}
		}
		$xml .= "</root>";
		return $xml;
	}


	//xml转换成数组
	private function xmlToArray($xml) {


		//禁止引用外部xml实体


		libxml_disable_entity_loader(true);


		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);


		$val = json_decode(json_encode($xmlstring), true);


		return $val;
	}


	//微信小程序接口
	private function weixinapp() {
		//统一下单接口
		$unifiedorder = $this->unifiedorder();
//        print_r($unifiedorder);
		$parameters = array(
			'appId' => $this->appid, //小程序ID
			'timeStamp' => '' . time() . '', //时间戳
			'nonceStr' => $this->createNoncestr(), //随机串
			'package' => 'prepay_id=' . $unifiedorder['prepay_id'], //数据包
			'signType' => 'MD5'//签名方式
		);
		//签名
		$parameters['paySign'] = $this->getSign($parameters);
		return $parameters;
	}


	//作用：产生随机字符串，不长于32位
	private function createNoncestr($length = 32) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}


	//作用：生成签名
	private function getSign($Obj) {
		foreach ($Obj as $k => $v) {
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//签名步骤二：在string后加入KEY
		$String = $String . "&key=" . $this->key;
		//签名步骤三：MD5加密
		$String = md5($String);
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		return $result_;
	}


	///作用：格式化参数，签名过程需要使用
	private function formatBizQueryParaMap($paraMap, $urlencode) {
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar = "";
		if (strlen($buff) > 0) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		}
		return $reqPar;
	}


}
