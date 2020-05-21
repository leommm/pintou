<?php
/**
 * @link http://tt.tryine.com/
 * @copyright Copyright (c) 2018 CSHOP
 * @author Lu Wei
 *
 * Created by Adon.
 * User: Adon
 * Date: 2018/3/28
 * Time: 15:21
 */


namespace app\modules\api\models\mch;

use app\models\Goods;
use app\models\Cat;
use app\models\VideoClassify;
use app\models\Video;
use app\models\Mch;
use app\models\MchCat;
use app\models\MchGoodsCat;
use app\models\Order;
use app\models\OrderDetail;
use app\modules\api\models\ApiModel;
use yii\data\Pagination;
use app\models\District;
use app\models\MchVisitLog;
use app\modules\api\models\LocationForm;

class ShopDataForm extends ApiModel
{
    public $mch_id;
    public $tab;
    public $sort;
    public $page;
    public $limit;
    public $cat_id;

    public function rules()
    {
        return [
            ['mch_id', 'required'],
            [['mch_id', 'cat_id',], 'integer'],
            ['tab', 'required'],
            ['sort', 'safe'],
            ['page', 'default', 'value' => 1,],
            ['limit', 'default', 'value' => 20,],
        ];
    }

    public function   search()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $shop = [];
        $coupon_list = [];
        $banner_list = [];
        $goods_list = [];
        $new_list = [];

        $shop = $this->getShop();
        if (isset($shop['code']) && $shop['code'] == 1) {
            return $shop;
        }

        if ($this->tab == 1) {
            $coupon_list = $this->getCouponList();
            $goods_list = $this->getHotList();
        }
        if ($this->tab == 2) {
            $goods_list = $this->getGoodsList();
        }
        if ($this->tab == 3) {
            $new_list = $this->getNewList();
        }

        return [
            'code' => 0,
            'data' => [
                'shop' => $shop,
                'coupon_list' => $coupon_list,
                'banner_list' => $banner_list,
                'goods_list' => $goods_list,
                'new_list' => $new_list,
            ],
        ];
    }

    public function getShop()
    {
        $mch = Mch::findOne([
            'id' => $this->mch_id,
            'review_status' => 1,
            'is_delete' => 0,
        ]);
        if (!$mch) {
            return [
                'code' => 1,
                'msg' => '店铺不存在',
            ];
        }
        if ($mch->is_open == 0 || $mch->is_lock == 1) {
            return [
                'code' => 1,
                'msg' => '店铺打烊了~',
            ];
        }
        $visit_num=MchVisitLog::find()->where(['mch_id'=>$mch->id])->asArray()->count();
        $from=[\Yii::$app->request->get('longitude'),\Yii::$app->request->get('latitude')];
        $to=[$mch->longitude,$mch->latitude];
        $location=new LocationForm();
        if(!empty($from)){
//            var_dump($to);exit;
            if(!empty($mch->longitude) && !empty($mch->latitude)){
                $distance=$location->get_distance($from,$to);//获取用户和商户的距离km
            }else{
                $distance="未知";//获取用户和商户的距离km
            }

        }
        $ids=array();
        $ids=[$mch->province_id,$mch->city_id,$mch->district_id];
        $loca_info=District::find()->where(["in","id",$ids])->asArray()->all();
        $mch_address=$mch->address;
        if($loca_info){
            $address="";
            foreach ($loca_info as $k=>$v){
                $address.=$v['name'];
            }
            $mch_address=$address.$mch_address;
        }
        $cat=Cat::find()->where(['is_delete'=>0,'is_show'=>1,'parent_id'=>0])->all();
        $cat_list=array();
        foreach ($cat as $k=>$v){
//            $cat_list[$k]=$v->toArray();
//            $cat_list[$k]['child']=Cat::find()->where(['parent_id'=>$v->id])->asArray()->all();
            //查询分类下面是否有商品
//            $cat_list_one = $v->toArray();
            $cat_list_cates = Cat::find()->where(['parent_id'=>$v->id])->asArray()->column();
            $or = ['in','cat_id',$cat_list_cates];
            if ( Goods::find()->where(['mch_id'=>$this->mch_id,'status'=>1,'is_delete'=>0])->andWhere($or)->count() > 0 ) {
             //一级分类下面有商品
                $cat_list[$k]=$v->toArray();
                $result_cate = [];
                foreach ( $cat_list_cates as $kk=>$vv ) {
                    if ( Goods::find()->where(['mch_id'=>$this->mch_id,'status'=>1,'is_delete'=>0,'cat_id'=>$vv])->count() > 0 ) {
                        $result_cate[$kk] = Cat::find()->where(['id'=>$vv])->asArray()->one();
                    }
                }
                $cat_list[$k]['child'] = $result_cate;

            }
        }

        $video_cat=VideoClassify::find()->alias('a')->leftJoin(['b'=>Video::tableName()],'a.id = b.classify_id')
            ->where("b.mch_id = {$this->mch_id} and b.is_delete = 1 and b.is_show = 1")
            ->orderBy('a.sort desc')
            ->asArray()
            ->all();

     //   $video_cat=VideoClassify::find()->asArray()->all();
//        var_dump($video_cat);exit;
        $shop = [
            'id' => $mch->id,
            'name' => $mch->name,
            'logo' => $mch->logo ? $mch->logo : \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/shop/img/shop-logo.png',
            'header_bg' => $mch->header_bg ? $mch->header_bg : \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/shop/img/shop-header-bg.jpg',
            'goods_num' => $this->getGoodsNum(),
            'sale_num' => $this->getSaleNum(),
            'tel' => $mch->tel,
            'wechat_name' => $mch->wechat_name,
            'visit_num'=>$visit_num,
            'mch_address'=>$mch_address,
            'distance'=>$distance?$distance:"未知",
            'lat'=>$mch->latitude?$mch->latitude:"未知",
            'lng'=>$mch->longitude?$mch->longitude:"未知",
            'remark'=>$mch->remark,
            'intr'=>$mch->intr,
        ];
        $shop['cat']=$cat_list;
        $shop['video_cat']=$video_cat;
        return $shop;
    }

    public function getCouponList()
    {
        return [
        ];
    }

    public function getHotList()
    {
        //有设置热销的优先返回设置热销的
        $query = Goods::find()->alias('g')->where([
            'g.is_delete' => 0,
            'g.mch_id' => $this->mch_id,
            'g.status' => 1,
            'g.hot_cakes' => 1,
        ]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $query->limit($pagination->limit)->offset($pagination->offset);
        $list = $query->select('g.id,g.name,g.cover_pic,g.price')->orderBy('g.mch_sort,g.addtime DESC')->asArray()->all();
        if (is_array($list) && count($list)) {
            return $list;
        }

        //没有热销的按销量排序
        $query = Goods::find()->alias('g')
            ->leftJoin(['od' => OrderDetail::find()->select('goods_id,SUM(num) sale_num')], 'g.id=od.goods_id')
            ->where([
                'g.is_delete' => 0,
                'g.mch_id' => $this->mch_id,
                'g.status' => 1,
            ]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $query->limit($pagination->limit)->offset($pagination->offset);

        $list = $query->select('g.id,g.name,g.cover_pic,g.price')->orderBy('od.sale_num DESC,g.mch_sort,g.addtime DESC')->asArray()->all();
        if (is_array($list) && count($list)) {
            return $list;
        }
        return [];
    }

    public function getGoodsList()
    {
        $query = Goods::find()->alias('g')->where([
            'g.is_delete' => 0,
            'g.mch_id' => $this->mch_id,
            'g.status' => 1,
        ]);
        if ($this->sort == 0) {
            $query->orderBy('g.mch_sort,g.addtime DESC');
            if ($this->cat_id) {
                $query->leftJoin(['mgc' => MchGoodsCat::tableName()], 'mgc.goods_id=g.id')->andWhere([
                    'mgc.cat_id' => MchCat::find()->alias('mc')->select('id')->where([
                        'OR',
                        ['parent_id' => $this->cat_id],
                        ['id' => $this->cat_id],
                    ])
                ]);
                $query->groupBy('g.id');
            }
        }
        if ($this->sort == 1) {
            $query->orderBy('g.mch_sort,g.addtime DESC');
        }
        if ($this->sort == 2) {
            $query->leftJoin(['od' => OrderDetail::find()->select('goods_id,SUM(num) sale_num')], 'g.id=od.goods_id');
            $query->orderBy('od.sale_num DESC,g.mch_sort,g.addtime');
        }
        if ($this->sort == 3) {
            $query->orderBy('g.price,g.mch_sort,g.addtime DESC');
        }
        if ($this->sort == 4) {
            $query->orderBy('g.price DESC,g.mch_sort,g.addtime DESC');
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $query->limit($pagination->limit)->offset($pagination->offset);
        $list = $query->select('g.id,g.name,g.cover_pic,g.price')->asArray()->all();
        if (is_array($list) && count($list)) {
            return $list;
        }
        return [];
    }

    public function getNewList()
    {
        $query = Goods::find()->alias('g')->where([
            'g.is_delete' => 0,
            'g.mch_id' => $this->mch_id,
            'g.status' => 1,
        ])->andWhere(['>=', 'g.addtime', time() - 86400 * 60]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => 50,]);
        $query->limit($pagination->limit)->offset($pagination->offset);
        $list = $query->select('g.id,g.name,g.cover_pic,g.price,g.addtime')->orderBy('g.addtime DESC')->asArray()->all();
        $new_list = [];
        foreach ($list as $item) {
            $date = date('m月d日', $item['addtime']);
            if (empty($new_list[$date])) {
                $new_list[$date] = [];
            }
            $new_list[$date][] = $item;
        }
        $new_list2 = [];
        foreach ($new_list as $date => $item) {
            $new_list2[] = [
                'date' => $date,
                'list' => $new_list[$date],
            ];
        }
        return $new_list2;
    }

    public function getGoodsNum($format = true)
    {
        $count = Goods::find()->where(['mch_id' => $this->mch_id, 'is_delete' => 0, 'status' => 1,])->count('1');
        $count = $count ? $count : 0;
        if ($count >= 10000 && $format) {
            $count = sprintf('%.2f', $count) . '万';
        }
        return $count;
    }

    public function getSaleNum($format = true)
    {
        $count = OrderDetail::find()->alias('od')
            ->leftJoin(['o' => Order::tableName()], 'od.order_id=o.id')
            ->where([
                'o.is_pay' => 1,
                'o.mch_id' => $this->mch_id,
            ])
            ->sum('od.num');
        $count = $count ? $count : 0;
        if ($count >= 10000 && $format) {
            $count = sprintf('%.2f', $count) . '万';
        }
        return $count;
    }
}
