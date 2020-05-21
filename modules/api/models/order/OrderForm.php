<?php
/**
 * @copyright ©2018 Lu Wei
 * @author Lu Wei
 * @link http://www.luweiss.com/
 * Created by Adon
 * Date Time: 2018/7/26 15:17
 */


namespace app\modules\api\models\order;


use app\models\Attr;
use app\models\AttrGroup;
use app\models\Cart;
use app\models\Form;
use app\models\Goods;
use app\models\Mch;
use app\models\Option;
use app\models\User;
use app\modules\api\models\ApiModel;

class OrderForm extends ApiModel
{

    public $store_id;
    public $user_id;
    public $mch_list;

    protected $user;

    public function rules()
    {
        return [
            ['mch_list', 'required'],
            ['mch_list', function ($attr, $params) {
                $data = \Yii::$app->serializer->decode($this->mch_list);
                if (!$data) {
                    $this->addError($attr, "{$attr}数据格式错误。");
                }
                $this->mch_list = $data;
            }],
            ['mch_list', function ($attr, $params) {
                foreach ($this->mch_list as $i => &$mch) {
                    if (!is_array($mch['goods_list'])) {
                        $this->addError($attr, "{$attr}[{$i}]['goods_list']必须是一个数组。");
                        return;
                    }
                }
            }],
        ];
    }

    protected function getMchListData()
    {
        //dd($this->mch_list);
        $this->user = User::findOne($this->user_id);
        foreach ($this->mch_list as $i => &$mch) {
            if ($mch['mch_id'] == 0) {
                $mch['name'] = '平台自营';
                $mch['form'] = $this->getFormData();
            } else {
                $_mch = Mch::findOne([
                    'store_id' => $this->store_id,
                    'id' => $mch['mch_id'],
                ]);
                if (!$_mch) {
                    unset($this->mch_list[$i]);
                    continue;
                }
                $mch['name'] = $_mch->name;
                $mch['form'] = null;
            }
            $this->getGoodsList($mch['goods_list']);
            $total_price = 0;
            foreach ($mch['goods_list'] as $_goods) {
                $total_price += doubleval($_goods['price']);
            }
            $mch['total_price'] = sprintf('%.2f', $total_price);
        }
        return $this->mch_list;
    }

    protected function getGoodsList(&$goods_list)
    {
        foreach ($goods_list as $i => &$item) {
            if ($item['cart_id']) {
                $cart = Cart::findOne([
                    'store_id' => $this->store_id,
                    'id' => $item['cart_id'],
                ]);
                if (!$cart) {
                    unset($goods_list[$i]);
                    continue;
                }
                $item['num'] = $cart->num;
                $attr_id_list = (array)\Yii::$app->serializer->decode($cart->attr);
                $goods = Goods::findOne($cart->goods_id);
            } elseif ($item['goods_id']) {
                $attr_id_list = [];
                foreach ($item['attr'] as $_a) {
                    array_push($attr_id_list, $_a['attr_id']);
                }
                $goods = Goods::findOne([
                    'store_id' => $this->store_id,
                    'id' => $item['goods_id'],
                ]);
            } else {
                unset($goods_list[$i]);
                continue;
            }
            if (!$goods) {
                unset($goods_list[$i]);
                continue;
            }
            $attr_info = $goods->getAttrInfo($attr_id_list);
            if ($item['num'] > $attr_info['num']) { //库存不足
                unset($goods_list[$i]);
                continue;
            }
            $attr_list = Attr::find()->alias('a')
                ->select('ag.id AS attr_group_id,ag.attr_group_name,a.id AS attr_id,a.attr_name')
                ->leftJoin(['ag' => AttrGroup::tableName()], 'a.attr_group_id=ag.id')
                ->where(['a.id' => $attr_id_list, 'ag.store_id' => $this->store_id,])
                ->asArray()->all();
            //$item['$attr_info'] = $attr_info;
            $item['attr_list'] = $attr_list;
            $item['goods_id'] = $goods->id;
            $item['goods_name'] = $goods->name;
            $item['goods_pic'] = $goods->cover_pic;
            $item['price'] = sprintf('%.2f', ($attr_info['price'] * $item['num']));
            $item['single_price'] = sprintf('%.2f', $attr_info['price']);
            $item['weight'] = $goods->weight;
            $item['integral'] = $goods->integral ? $goods->integral : 0;
            $item['freight'] = $goods->freight;
        }
    }


    //自定义表单
    private function getFormData()
    {
        $new_list = [];
        $new_list['is_form'] = Option::get('is_form', $this->store_id, 'admin', 0);
        $form_list = [];
        if ($new_list['is_form'] == 1) {
            $new_list['name'] = Option::get('form_name', $this->store_id, 'admin', '表单信息');
            $form_list = Form::find()->where([
                'store_id' => $this->store_id, 'is_delete' => 0,
            ])->asArray()->all();
            foreach ($form_list as $index => $value) {
                if (in_array($value['type'], ['radio', 'checkbox'])) {
                    $default = str_replace("，", ",", $value['default']);
                    $list = explode(',', $default);
                    $default_list = [];
                    foreach ($list as $k => $v) {
                        $default_list[$k]['name'] = $v;
                        if ($k == 0) {
                            $default_list[$k]['is_selected'] = 1;
                        } else {
                            $default_list[$k]['is_selected'] = 0;
                        }
                    }
                    $form_list[$index]['default_list'] = $default_list;
                }
            }
        }
        $new_list['list'] = $form_list;
        return $new_list;
    }
}