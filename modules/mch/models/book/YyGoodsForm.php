<?php
/**
 * Created by PhpStorm.
 * User: peize
 * Date: 2017/11/22
 * Time: 17:35
 */

namespace app\modules\mch\models\book;

use app\models\GoodsShare;
use app\models\YyForm;
use app\models\YyGoods;
use app\models\YyGoodsPic;
use app\modules\mch\models\MchModel;
use yii\data\Pagination;

/**
 * @property YyGoods $goods;
 * @property GoodsShare $goods_share;
 */
class YyGoodsForm extends MchModel
{
    public $goods;
    public $goods_share;

    public $goods_pic_list;

    public $name;
    public $store_id;
    public $original_price;
    public $price;
    public $detail;
    public $cat_id;

    public $service;
    public $sort;
    public $virtual_sales;
    public $cover_pic;
    public $buy_limit;
    public $stock;

    public $shop_id;

    public $form_list = [];

    public $individual_share;
    public $share_commission_first;
    public $share_commission_second;
    public $share_commission_third;
    public $share_type;
    public $rebate;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price', 'original_price', 'detail', 'service', 'store_id', 'buy_limit', 'stock'], 'required'],
            [['detail', 'cover_pic'], 'string'],
            [['cat_id', 'store_id','share_type'], 'integer'],
            [['name','shop_id'], 'string', 'max' => 255],
            [['service'], 'string', 'max' => 2000],
            [['goods_pic_list','form_list',], 'safe',],
            [['share_commission_first', 'share_commission_second', 'share_commission_third','individual_share','rebate','virtual_sales','buy_limit','stock','sort'], 'default', 'value' => 0],
            [['price', 'original_price','share_commission_first', 'share_commission_second', 'share_commission_third','rebate',], 'number', 'min' => 0,'max'=>999999],
            [['buy_limit','stock','virtual_sales','sort'],'integer','min'=>0,'max'=>99999]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_pic_list' => '商品图集',
            'id' => 'ID',
            'name' => '商品名称',
            'price' => '预约金额',
            'original_price' => '原价',
            'detail' => '商品详情，图文',
            'cat_id' => '商品分类',
            'status' => '上架状态【1=> 上架，2=> 下架】',
            'service' => '服务内容',
            'sort' => '商品排序',
            'virtual_sales' => '虚拟销量',
            'cover_pic' => '商品缩略图',
            'addtime' => '添加时间',
            'is_delete' => '是否删除',
            'sales' => '实际销量',
            'shop_id' => '门店id',
            'store_id' => 'Store ID',
            'buy_limit' => '限购次数',
            'stock' => '库存',
            'share_commission_first' => '一级佣金比例',
            'share_commission_second' => '二级佣金比例',
            'share_commission_third' => '三级佣金比例',
            'rebate'=>'自购返利'
        ];
    }

    /**
     * @param $store_id
     * @return array
     * 商品列表
     */
    public function getList($store_id)
    {
        $query = YyGoods::find()
            ->alias('g')
            ->andWhere(['g.is_delete' => 0, 'g.store_id' => $store_id])
            ->select(['g.*', 'c.name AS cname'])
            ->leftJoin('{{%yy_cat}} c', 'g.cat_id=c.id');
        $cat = \Yii::$app->request->get('cat');
        if ($cat) {
            $query->andWhere(['cat_id'=>$cat]);
        }
        $keyword = \Yii::$app->request->get('keyword');
        if (trim($keyword)) {
            $query->andWhere([
                'OR',
                ['LIKE', 'g.name', $keyword],
                ['LIKE', 'c.name', $keyword],
            ]);
        }
        $count = $query->count();
        $p = new Pagination(['totalCount' => $count, 'pageSize' => 20]);

        $list = $query
            ->orderBy('g.sort ASC')
            ->offset($p->offset)
            ->asArray()
            ->limit($p->limit)
            ->all();
        return [$list, $p];
    }

    /**
     * @return array
     * 商品编辑
     */
    public function save()
    {
        if ($this->validate()) {
            if (!is_array($this->goods_pic_list) || empty($this->goods_pic_list) || count($this->goods_pic_list) == 0) {
                return [
                    'code' => 1,
                    'msg' => '商品图片不能为空',
                ];
            }
            if (!$this->original_price) {
                $this->original_price = $this->price;
            }

            if ($this->original_price > 99999999.99) {
                return [
                    'code' => 1,
                    'msg' => '商品原价超过限制',
                ];
            }
            if ($this->price > 99999999.99) {
                return [
                    'code' => 1,
                    'msg' => '商品售价超过限制',
                ];
            }

            $goods = $this->goods;
            if ($goods->isNewRecord) {
                $goods->is_delete = 0;
                $goods->addtime = time();
                $goods->status = 2;
                $goods->sales = 0;
            }

            $goods->attributes = $this->attributes;

            if ($goods->save()) {
                YyGoodsPic::updateAll(['is_delete' => 1], ['goods_id' => $goods->id]);
                foreach ($this->goods_pic_list as $pic_url) {
                    $goods_pic = new YyGoodsPic();
                    $goods_pic->goods_id = $goods->id;
                    $goods_pic->pic_url = $pic_url;
                    $goods_pic->is_delete = 0;
                    $goods_pic->save();
                }

                YyForm::updateAll(['is_delete' => 1], ['goods_id'=>$goods->id]);
                foreach ($this->form_list as $form) {
                    $form_list = new YyForm();
                    $form_list->goods_id = $goods->id;
                    $form_list->store_id = $this->store_id;
                    $form_list->name = $form['name'];
                    $form_list->type = $form['type'];
                    $form_list->required = $form['required'];
                    $form_list->default = $form['default'];
                    $form_list->tip = $form['tip'];
                    $form_list->sort = $form['sort'];
                    $form_list->is_delete = 0;
                    $form_list->addtime = time();
                    $form_list->save();
                }

                //单商品分销设置
                $this->goods_share->store_id = $this->store_id;
                $this->goods_share->type = 1;
                $this->goods_share->goods_id = $goods->id;
                $this->goods_share->individual_share = $this->individual_share;
                $this->goods_share->share_commission_first = $this->share_commission_first;
                $this->goods_share->share_commission_second = $this->share_commission_second;
                $this->goods_share->share_commission_third = $this->share_commission_third;
                $this->goods_share->share_type = $this->share_type;
                $this->goods_share->rebate = $this->rebate;
                $this->goods_share->save();


                return [
                    'code' => 0,
                    'msg' => '保存成功',
                ];
            } else {
                return $this->getErrorResponse($goods);
            }
        } else {
            return $this->errorResponse;
        }
    }
}
