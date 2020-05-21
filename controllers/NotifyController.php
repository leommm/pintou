<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/7/19
 * Time: 9:57
 */

namespace app\controllers;

use app\models\BrOrder;

class NotifyController extends Controller
{
	public function actionIndex()
	{

		$postXml = $GLOBALS["HTTP_RAW_POST_DATA"]; //接收微信参数
		if (empty($postXml)) {
			return false;
		}

		$attr = $this->xmlToArray($postXml);

		$total_fee = $attr['total_fee'];
		$open_id = $attr['openid'];
		$out_trade_no = $attr['out_trade_no'];
		$time = $attr['time_end'];

		$order = BrOrder::findOne(['order_no'=>$out_trade_no]);
		$order->is_pay = 1;
		$order->pay_time = time();
		$order->save();
		
	}

	public function xmlToArray($xml) {

		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);

		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

		$val = json_decode(json_encode($xmlstring), true);

		return $val;
	}
}
