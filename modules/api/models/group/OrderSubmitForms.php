<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/7/17
 * Time: 11:48
 */

namespace app\modules\api\models\group;

use app\models\Model;
use app\utils\PinterOrder;
use app\utils\PrinterPtOrder;
use app\models\Address;
use app\models\Attr;
use app\models\AttrGroup;
use app\models\FreeDeliveryRules;
use app\models\Goods;
use app\models\Order;
use app\models\PostageRules;
use app\models\PrinterSetting;
use app\models\CmGoods;
use app\models\CmOrder;
use app\models\CmOrderDetail;
use app\models\Store;
use app\models\User;
use app\models\UserCoupon;
use app\modules\api\models\ApiModel;
use app\models\CmGoodsDetail;
use app\models\TerritorialLimitation;
use app\models\PtSetting;

class OrderSubmitForms extends ApiModel
{
    public $store_id;
    public $user_id;

    public $address_id;
    public $goods_info;

    public $shop_id;

    public $use_integral;

    public $type;
    public $parent_id;

    public $content;
    public $offline;
    public $address_name;
    public $address_mobile;

    public $payment;

    public function rules()
    {
        return [
            [['goods_info', 'content', 'address_name', 'address_mobile'], 'string'],
            [['shop_id', 'use_integral'], 'integer'],
            [['parent_id', 'payment'], 'default', 'value' => 0],
            [['address_id',], 'required', 'on' => "EXPRESS"],
            [['address_name', 'address_mobile'], 'required', 'on' => "OFFLINE"],
            [['offline'], 'default', 'value' => 1],
            [['payment'],'integer','message'=>'请选择支付方式'],
            [['address_mobile'],'match','pattern' =>Model::MOBILE_PATTERN , 'message'=>'手机号错误']
        ];
    }

    public function attributeLabels()
    {
        return [
            'address_id' => '收货地址',
            'address_name' => '收货人',
            'address_mobile' => '联系电话'
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $t = \Yii::$app->db->beginTransaction();
        $express_price = 0;
		
        if ($this->offline == 1) {    
            $address = Address::findOne([
                'id' => $this->address_id,
                'store_id' => $this->store_id,
                'user_id' => $this->user_id,
            ]);
            if (!$address) {
                return [
                    'code' => 1,
                    'msg' => '收货地址不存在',
                ];
            }

            $area = TerritorialLimitation::findOne([
                'store_id' => $this->store_id,
                'is_delete' => 0,
                'is_enable' => 1,
            ]);
            if ($area) {
                $city_id = [];  //限制的地区ID
                $detail = json_decode($area->detail);
                foreach ($detail as $key => $value) {
                    foreach ($value->province_list as $key2 => $value2) {
                        $city_id[] = $value2->id;
                    }
                }
                $setting = PtSetting::findOne([
                    'store_id' => $this->store_id,
                    'is_area' => 1,
                ]);
                if ($setting) {
                    if (!in_array($address->city_id, $city_id)) {
                        return [
                            'code' => 1,
                            'msg' => '所选地区无货'
                        ];
                    }
                }
            }
//            $express_price = PostageRules::getExpressPrice($this->store_id, $address->province_id);
        } else {
        }
        if (!in_array($this->payment, [0, 2, 3])) {
            return [
                'code' => 1,
                'msg' => '请选择支付方式'
            ];
        }
		
        $parentOrder = null;

        $goods_list = [];
        $total_price = 0;
        $data = $this->getGoodsListByGoodsInfo($this->goods_info);
        if (isset($data['code'])) {
            return $data;
        }
        $goods_list = empty($data['list']) ? [] : $data['list'];
        $total_price = empty($data['total_price']) ? 0 : $data['total_price'];
        foreach ($goods_list as $k => $val) {
            $goods = CmGoods::findOne([
                'id' => $val->goods_id,
                'is_delete' => 0,
                'store_id' => $this->store_id,
                'status' => 1,
            ]);

            $goods_info = json_decode($this->goods_info);

            if ($this->offline == 1) {
                $express_price = PostageRules::getExpressPrice($this->store_id, $address->city_id, $goods, $val->num, $address->province_id);
            }
        }
        if (empty($goods_list)) {
            return [
                'code' => 1,
                'msg' => '订单提交失败，所选商品库存不足或已下架',
            ];
        }
        $order = new CmOrder();

        $total_price_1 = $total_price + $express_price; // 总价
        //包邮规则
        if ($express_price != 0) {
            $free = FreeDeliveryRules::find()->where(['store_id' => $this->store_id])->asArray()->all();
            foreach ($free as $k => $v) {
                $city = json_decode($v['city'], true);
                foreach ($city as $v1) {
                    if ($address->city == $v1['name'] && $total_price >= $v['price']) {
                        $order->express_price_1 = $express_price;
                        $express_price = 0;
                        break;
                    }
                }
            }
        }

        $total_price_2 = $total_price + $express_price; // 实际付款价
        $order->store_id = $this->store_id;
        $order->user_id = $this->user_id;
        $order->order_no = $this->getOrderNo();
        $order->total_price = $total_price_1;
        $order->pay_type = $this->payment;
        $order->pay_price = $total_price_2 < 0.01 ? 0.01 : $total_price_2;
        $order->express_price = $express_price;
        $order->addtime = time();
        $order->offline = $this->offline;

        if ($this->offline == 1) {
            $order->address = $address->province . $address->city . $address->district . $address->detail;
            $order->mobile = $address->mobile;
            $order->name = $address->name;
            $order->address_data = json_encode([
                'province' => $address->province,
                'city' => $address->city,
                'district' => $address->district,
                'detail' => $address->detail,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            $order->name = $this->address_name;
            $order->mobile = $this->address_mobile;
            $order->shop_id = $this->shop_id;
        }
        $order->content = $this->content;
        $order->status = 1; // 待付款

        $order->parent_id = $this->parent_id;
		$order->is_group = 0;

        if ($order->save()) {
            foreach ($goods_list as $goods) {
                $order_detail = new CmOrderDetail();
                $order_detail->order_id = $order->id;
                $order_detail->goods_id = $goods->goods_id;
                $order_detail->num = $goods->num;
                $order_detail->total_price = round($order->pay_price - $order->express_price, 2);
                $order_detail->addtime = time();
                $order_detail->is_delete = 0;
                $order_detail->attr = json_encode($goods->attr_list, JSON_UNESCAPED_UNICODE);
                $order_detail->pic = $goods->goods_pic;
				$order_detail->mch_id = $goods->mch_id;

                $attr_id_list = [];
                foreach ($goods->attr_list as $item) {
                    array_push($attr_id_list, $item['attr_id']);
                }
                $_goods = CmGoods::findOne($goods->goods_id);
                if (!$_goods->numSub($attr_id_list, $order_detail->num)) {
                    $t->rollBack();
                    return [
                        'code' => 1,
                        'msg' => '订单提交失败，商品“' . $_goods->name . '”库存不足',
                        'attr_id_list' => $attr_id_list,
                        'attr_list' => $goods->attr_list,
                    ];
                }
                if (!$order_detail->save()) {
                    $t->rollBack();
                    return [
                        'code' => 1,
                        'msg' => '订单提交失败，请稍后再重试',
                    ];
                }
            }
            $printer_order = new PinterOrder($this->store_id, $order->id, 'order', 2);
            $res = $printer_order->print_order();
            $t->commit();
            return [
                'code' => 0,
                'msg' => '订单提交成功',
                'data' => (object)[
                    'order_id' => $order->id,
                ],
            ];
        } else {
            $t->rollBack();
            return $this->getErrorResponse($order);
        }
    }

    /**
     * @param string $goods_info
     * eg.{"goods_id":"22","attr":[{"attr_group_id":1,"attr_group_name":"颜色","attr_id":3,"attr_name":"橙色"},{"attr_group_id":2,"attr_group_name":"尺码","attr_id":2,"attr_name":"M"}],"num":1}
     */
    private function getGoodsListByGoodsInfo($goods_info)
    {
        $goods_info = json_decode($goods_info);
        $goods = CmGoods::findOne([
            'id' => $goods_info->goods_id,
            'is_delete' => 0,
            'store_id' => $this->store_id,
            'status' => 1,
        ]);
        if (!$goods) {
            return [
                'total_price' => 0,
                'list' => [],
            ];
        }
        
        if (!is_int($goods_info->num) || $goods_info->num < 1) {
            return [
                'code' => 1,
                'msg' => '商品数量不正确',
            ];
        }
        if ($goods->buy_limit > 0) {
            $orderNum = CmOrder::find()
                ->alias('o')
                ->select([
                    'o.*',
                ])
                ->andWhere(['o.user_id'=>$this->user_id,'o.is_delete'=>0,'o.is_pay'=>1,'o.is_group'=>1])
                ->andWhere(['OR',['o.status'=>2],['o.status'=>3]])
                ->leftJoin(['od'=>CmOrderDetail::tableName()], 'od.order_id = o.id')
                ->andWhere(['od.goods_id'=>$goods->id])
                ->andWhere(['!=','o.order_no','robot'])
                ->count();
            $orderNum = CmOrder::getCount($goods->id,$this->user_id);
            if ($orderNum >= $goods->buy_limit) {
                return [
                    'code'  => 1,
                    'msg'   => '您已超过该商品购买次数',
                ];
            }
        }
        if ($goods->one_buy_limit > 0) {
            if ($goods_info->num > $goods->one_buy_limit) {
                return [
                    'code'=>1,
                    'msg'=>'您已超过该商品可购买数量'
                ];
            }
        }

        $attr_id_list = [];
        foreach ($goods_info->attr as $item) {
            array_push($attr_id_list, $item->attr_id);
        }
        $total_price = 0;
        $goods_attr_info = $goods->getAttrInfo($attr_id_list, $goods_info->group_id);

        $attr_list = Attr::find()->alias('a')
            ->select('a.id attr_id,ag.attr_group_name,a.attr_name')
            ->leftJoin(['ag' => AttrGroup::tableName()], 'a.attr_group_id=ag.id')
            ->where(['a.id' => $attr_id_list])
            ->asArray()->all();

        foreach ($attr_list as &$i) {
            $i['no'] = isset($goods_attr_info['no']) ? $goods_attr_info['no'] : 0;
        }
        unset($i);
        $goods_pic = isset($goods_attr_info['pic']) ? $goods_attr_info['pic'] ?: $goods->cover_pic : $goods->cover_pic;

        $price = doubleval(empty($goods_attr_info['price']) ? $goods->price : $goods_attr_info['price']) * $goods_info->num;

        $goods_item = (object)[
            'goods_id' => $goods->id,
            'goods_name' => $goods->name,
            'goods_pic' => $goods_pic,
			'mch_id' => $goods->mch_id,
            'num' => $goods_info->num,
            'price' => $price,
            'attr_list' => $attr_list,
        ];
        $total_price += $goods_item->price;
        return [
            'total_price' => $total_price,
            'list' => [$goods_item],
        ];
    }

    /**
     * @return null|string
     * 生成订单号
     */
    public function getOrderNo()
    {
        $store_id = empty($this->store_id) ? 0 : $this->store_id;
        $order_no = null;
        while (true) {
            $order_no = date('YmdHis') . mt_rand(100000, 999999);
            $exist_order_no = Order::find()->where(['order_no' => $order_no])->exists();
            if (!$exist_order_no) {
                break;
            }
        }
        return $order_no;
    }
}
