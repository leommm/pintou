<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pt_goods}}".
 *
 * @property string $id
 * @property integer $store_id
 * @property string $name
 * @property string $original_price
 * @property string $price
 * @property string $detail
 * @property string $cat_id
 * @property integer $status
 * @property string $attr
 * @property string $sort
 * @property string $virtual_sales
 * @property string $cover_pic
 * @property string $weight
 * @property string $unit
 * @property string $addtime
 * @property integer $is_delete
 * @property string $total_num
 * @property string $is_hot
 * @property string $limit_time
 * @property string $is_more
 * @property string $buy_limit
 * @property string $use_attr
 * @property string $fav_num
 * @property string $payment
 */
class BrGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%br_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'name', 'original_price', 'price', 'detail', 'attr'], 'required'],
            [['store_id', 'cat_id', 'status', 'sort', 'virtual_sales', 'weight', 'addtime', 'is_delete', 'total_num', 'is_hot', 'limit_time', 'is_more', 'buy_limit', 'use_attr', 'fav_num', 'mch_id', 'date_start', 'date_end', 'is_show_price', 'is_al_order', 'is_me_br', 'goods_num'], 'integer'],
            [['original_price', 'price'], 'number'],
            [['detail', 'attr', 'cover_pic', 'qj_content'], 'string'],
            [['name', 'unit', 'payment'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'name' => '商品名称',
            'original_price' => '商品原价',
            'price' => '团购价',
            'detail' => '商品详情，图文',
            'cat_id' => '商品分类',
            'status' => '上架状态【1=> 上架，2=> 下架】',
            'attr' => '规格的库存及价格',
            'sort' => '商品排序 升序',
            'virtual_sales' => '虚拟销量',
            'cover_pic' => '商品缩略图',
            'weight' => '重量',
            'unit' => '单位',
            'addtime' => '添加时间',
            'is_delete' => '是否删除',
            'total_num' => '可砍总数',
            'is_hot' => '是否热卖【0=>热卖1=>不是】',
            'limit_time' => '砍价限时',
            'is_more' => '是否允许多件购买',
            'buy_limit' => '每人可砍次数',
            'use_attr' => '是否使用规格：0=不使用，1=使用',
            'fav_num' => '活动发起次数',
            'payment' => '支付方式',
			'mch_id' => '商户id',
			'date_start' => '活动开始时间',
			'date_end' => '活动结束时间',
			'is_show_price' => '是否显示低价',
			'is_al_order' => '是否允许未砍到低价下单',
			'is_me_br' => '允许自己砍价',
			'qj_content' => '区间',
			'goods_num' => '商品库存'
        ];
    }


    /**
     * @return static[]
     * 获取商品图集
     */
    public function goodsPicList()
    {
        return BrGoodsPic::findAll(['goods_id'=>$this->id,'is_delete'=>0]);
    }

    /**
 * 获取商品总库存
 * @param int $id 商品id
 */
    public static function getNum($id = null)
    {
        $goods = null;
        if (!$id) {
            $goods = $this;
        } else {
            $goods = static::findOne($id);
            if (!$goods) {
                return 0;
            }
        }
        if (!$goods->attr) {
            return 0;
        }
        $num = 0;
        $attr_rows = json_decode($goods->attr, true);
        foreach ($attr_rows as $attr_row) {
            $num += intval($attr_row['num']);
        }
        return $num;
    }
    /**
     * 获取商品总库存
     * @param int $id 商品id
     */
    public function getDNum($id = null)
    {
        $goods = null;
        if (!$id) {
            $goods = $this;
        } else {
            $goods = static::findOne($id);
            if (!$goods) {
                return 0;
            }
        }
        if (!$goods->attr) {
            return 0;
        }
        $num = 0;
        $attr_rows = json_decode($goods->attr, true);
        foreach ($attr_rows as $attr_row) {
            $num += intval($attr_row['num']);
        }
        return $num;
    }
    /**
     * 商品默认规格
     */
    public function getAttrGroupListnum()
    {

        $goodsdetail = BrGoodsDetail::find()->where(['store_id'=>$this->store_id])->andWhere('goods_id=:goods_id', [':goods_id'=>$this->id])->all();
        $goods = (object)null;
        $goods->attr_group_name = '拼团人数';
        $goods->attr_group_id = $this->id;

        foreach ($goodsdetail as $k => $v) {
            $goods->attr_list[$k] = [
            'id'=>$v->id,
            'total_num'=>$v->total_num,
            'attr' => $v->attr,
            'group_time' => $v->group_time
            ];
        }
        return $goods;
    }

    /**
     * 获取商品可选的规格列表
     */
    public function getAttrGroupList()
    {
        $attr_rows = json_decode($this->attr, true);
        if (empty($attr_rows)) {
            return [];
        }
        $attr_group_list = [];
        foreach ($attr_rows as $attr_row) {
            foreach ($attr_row['attr_list'] as $i => $attr) {
                $attr_id = $attr['attr_id'];
                $attr = Attr::findOne(['id' => $attr_id, 'is_delete' => 0]);
                if (!$attr) {
                    continue;
                }
                $in_list = false;
                foreach ($attr_group_list as $j => $attr_group) {
                    if ($attr_group->attr_group_id == $attr->attr_group_id) {
                        $attr_obj = (object)[
                            'attr_id' => $attr->id,
                            'attr_name' => $attr->attr_name,
                        ];
                        if (!in_array($attr_obj, $attr_group_list[$j]->attr_list)) {
                            $attr_group_list[$j]->attr_list[] = $attr_obj;
                        }
                        $in_list = true;
                        continue;
                    }
                }
                if (!$in_list) {
                    $attr_group = AttrGroup::findOne(['is_delete' => 0, 'id' => $attr->attr_group_id]);
                    if ($attr_group) {
                        $attr_group_list[] = (object)[
                            'attr_group_id' => $attr_group->id,
                            'attr_group_name' => $attr_group->attr_group_name,
                            'attr_list' => [
                                (object)[
                                    'attr_id' => $attr->id,
                                    'attr_name' => $attr->attr_name,
                                ],
                            ],
                        ];
                    }
                }
            }
        }
        return $attr_group_list;
    }

    /**
     * 根据规格获取商品的库存及规格价格信息
     * @param array $attr_id_list 规格id列表 eg. [1,4,9]
     * @return array|null eg.
     */
    public function getAttrInfo($attr_id_list, $id = null)
    {
        sort($attr_id_list);
            $attr_rows = json_decode($this->attr, true);
        $attr = $this->info($attr_rows, $attr_id_list);
        if (empty($attr)) {
            return null;
        }
        if (!$attr['price']) {
            $attr['price'] = $this->price;
        }
        if (!$attr['single']) {
            $attr['single'] = $this->original_price;
        }

        if ($id) {
            $list = BrGoodsDetail::find()->where(['store_id'=>$this->store_id])->andWhere('id=:id', [':id'=>$id])->one();
            $attr_rows = json_decode($list->attr, true);

            $new = $this->info($attr_rows, $attr_id_list);

            if ($new['price']) {
                $attr['price'] = sprintf("%.2f", $new['price']);
            }
        }

        return $attr;
    }

    /*

     */
    private function info($attr_rows, $attr_id_list)
    {
        foreach ($attr_rows as $i => $attr_row) {
            $key = [];
            foreach ($attr_row['attr_list'] as $j => $attr) {
                $key[] = $attr['attr_id'];
            }
            sort($key);
            if (!array_diff($attr_id_list, $key)) {
                return $attr_rows[$i];
            }
        }
        return null;
    }
    /**
     * 库存减少操作
     * @param array $attr_id_list eg. [1,4,2]
     */
    public function numSub($attr_id_list, $num)
    {
        sort($attr_id_list);
        $attr_group_list = json_decode($this->attr);
        $sub_attr_num = false;
        foreach ($attr_group_list as $i => $attr_group) {
            $group_attr_id_list = [];
            foreach ($attr_group->attr_list as $attr) {
                array_push($group_attr_id_list, $attr->attr_id);
            }
            sort($group_attr_id_list);
            if (!array_diff($attr_id_list, $group_attr_id_list)) {
                if ($num > intval($attr_group_list[$i]->num)) {
                    return false;
                }
                $attr_group_list[$i]->num = intval($attr_group_list[$i]->num) - $num;
                $sub_attr_num = true;
                break;
            }
        }
        if (!$sub_attr_num) {
            return false;
        }
        $this->attr = json_encode($attr_group_list, JSON_UNESCAPED_UNICODE);
        $this->save();
        return true;
    }

    /**
     * 获取商品销量
     */
    public function getSalesVolume()
    {
        $res = BrOrderDetail::find()->alias('od')
            ->select('SUM(od.num) AS sales_volume')
            ->leftJoin(['o' => BrOrder::tableName()], 'od.order_id=o.id')
            ->where(['od.is_delete' => 0, 'od.goods_id' => $this->id, 'o.is_delete' => 0, 'o.is_pay' => 1,])
            ->asArray()->one();
        return empty($res['sales_volume']) ? 0 : intval($res['sales_volume']);
    }
    /**
     * 验证限时拼团是否超时
     */
    public function checkLimitTime($id = null)
    {
        $goods = null;
        if (!$id) {
            $goods = $this;
        } else {
            $goods = static::findOne($id);
        }
        if (!$goods) {
            return false;
        }
        if (!empty($goods->limit_time) && $goods->limit_time < time()) {
            return false;
        } else {
            return true;
        }
    }

    public static function getGoodsPicStatic($goods_id, $index = 0)
    {
        $goods = BrGoods::findOne($goods_id);
        if (!$goods) {
            return null;
        }
        return $goods->cover_pic;
    }

//    public function getGoodsPic($index = 0)
//    {
//        $list = $this->goodsPicList;
//        if (!$list)
//            return null;
//        return isset($list[$index]) ? $list[$index] : null;
//    }

    public function getAttrData()
    {
        if ($this->isNewRecord) {
            return [];
        }
        if (!$this->use_attr) {
            return [];
        }
        if (!$this->attr) {
            return [];
        }
        $attr_group_list = [];

        $attr_data = json_decode($this->attr, true);
        foreach ($attr_data as $i => $attr_data_item) {
            foreach ($attr_data[$i]['attr_list'] as $j => $attr_list) {
                $attr_group = $this->getAttrGroupByAttId($attr_data[$i]['attr_list'][$j]['attr_id']);
                if ($attr_group) {
                    $in_list = false;
                    foreach ($attr_group_list as $k => $exist_attr_group) {
                        if ($exist_attr_group['attr_group_name'] == $attr_group->attr_group_name) {
                            $attr_item = [
                                'attr_name' => $attr_data[$i]['attr_list'][$j]['attr_name'],
                            ];
                            if (!in_array($attr_item, $attr_group_list[$k]['attr_list'])) {
                                $attr_group_list[$k]['attr_list'][] = $attr_item;
                            }
                            $in_list = true;
                        }
                    }
                    if (!$in_list) {
                        $attr_group_list[] = [
                            'attr_group_name' => $attr_group->attr_group_name,
                            'attr_list' => [
                                [
                                    'attr_name' => $attr_data[$i]['attr_list'][$j]['attr_name'],
                                ],
                            ],
                        ];
                    }
                }
            }
        }
        return $attr_group_list;
    }

    public function getCheckedAttrData()
    {
        if ($this->isNewRecord) {
            return [];
        }
        if (!$this->use_attr) {
            return [];
        }
        if (!$this->attr) {
            return [];
        }
        $attr_data = json_decode($this->attr, true);
        foreach ($attr_data as $i => $attr_data_item) {
            if (!isset($attr_data[$i]['no'])) {
                $attr_data[$i]['no'] = '';
            }
            if (!isset($attr_data[$i]['pic'])) {
                $attr_data[$i]['pic'] = '';
            }
            foreach ($attr_data[$i]['attr_list'] as $j => $attr_list) {
                $attr_group = $this->getAttrGroupByAttId($attr_data[$i]['attr_list'][$j]['attr_id']);
                $attr_data[$i]['attr_list'][$j]['attr_group_name'] = $attr_group ? $attr_group->attr_group_name : null;
            }
        }
        return $attr_data;
    }

    private function getAttrGroupByAttId($att_id)
    {
        $cache_key = 'get_attr_group_by_attr_id_' . $att_id;
        $attr_group = Yii::$app->cache->get($cache_key);
        if ($attr_group) {
            return $attr_group;
        }
        //$attr_group = AttrGroup::find()->alias('ag')
        //    ->leftJoin(['a' => Attr::tableName()], 'a.attr_group_id=ag.id')
        //    ->where(['a.id' => $att_id])
        //    ->one();
        $attr_group = AttrGroup::find()->alias('ag')
            ->where(['ag.id' => Attr::find()->select('attr_group_id')->distinct()->where(['id' => $att_id])])
            ->limit(1)->one();
        if (!$attr_group) {
            return $attr_group;
        }
        Yii::$app->cache->set($cache_key, $attr_group, 10);
        return $attr_group;
    }


    /**
     * 库存增加操作
     */
    public function numAdd($attr_id_list, $num)
    {
        sort($attr_id_list);
        $attr_group_list = json_decode($this->attr);
        $add_attr_num = false;
        foreach ($attr_group_list as $i => $attr_group) {
            $group_attr_id_list = [];
            foreach ($attr_group->attr_list as $attr) {
                array_push($group_attr_id_list, $attr->attr_id);
            }
            sort($group_attr_id_list);
            if (!array_diff($attr_id_list, $group_attr_id_list)) {
                $attr_group_list[$i]->num = intval($attr_group_list[$i]->num) + $num;
                $add_attr_num = true;
                break;
            }
        }
        if (!$add_attr_num) {
            return false;
        }
        $this->attr = json_encode($attr_group_list, JSON_UNESCAPED_UNICODE);
        $this->save();
        return true;
    }

    public function getShare()
    {
        return $this->hasOne(GoodsShare::className(), ['goods_id'=>'id'])->where(['store_id'=>$this->store_id]);
    }

    // 获取默认规格商品的货号
    public function getGoodsNo($id = null)
    {
        $goods = null;
        if (!$id) {
            $goods = $this;
        } else {
            $goods = static::findOne($id);
            if (!$goods) {
                return 0;
            }
        }
        if (!$goods->attr) {
            return 0;
        }
        $num = 0;
        $attr_rows = json_decode($goods->attr, true);
        foreach ($attr_rows as $attr_row) {
            $num = $attr_row['no'];
        }
        return $num;
    }
}
