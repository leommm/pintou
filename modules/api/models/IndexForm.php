<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/7/5
 * Time: 16:00
 */

namespace app\modules\api\models;


use app\models\BrGoods;
use app\models\Article;
use app\models\Video;
use app\modules\api\models\group\BrGoodsForm;
use app\utils\GetInfo;
use app\hejiang\ApiResponse;
use app\models\Banner;
use app\models\Cat;
use app\models\Coupon;
use app\models\FxhbHongbao;
use app\models\FxhbSetting;
use app\models\Goods;
use app\models\GoodsPic;
use app\models\HomeBlock;
use app\models\HomeNav;
use app\models\HomePageModule;
use app\models\Mch;
use app\models\MiaoshaGoods;
use app\models\MsGoods;
use app\models\Option;
use app\models\PtGoods;
use app\models\PtOrder;
use app\models\PtOrderDetail;
use app\models\Store;
use app\models\Topic;
use app\models\User;
use app\models\UserCoupon;
use app\models\YyGoods;
use app\models\District;
use app\hejiang\ApiCode;
use yii\helpers\VarDumper;
use Yii;
use app\modules\api\models\group\CmGoodsForm;

class IndexForm extends ApiModel
{
    public $store_id;
    public $user_id;

    public function search()
    {


        
         $use=User::findone(['id'=>$this->user_id]);
         $province=District::findOne(['id'=>$use->province]);   
         $city=District::findOne(['id'=>$use->city]);
         $district=District::findOne(['id'=>$use->district]);   

         if(is_null($use->district)){
            $district=['code'=>1,'msg'=>"尚未绑定地区"];
        }else{
             $district=['code'=>0,'msg'=>"已绑定地区",'data'=>['province'=>$province->name,'city'=>$city->name,'district'=>$district->name]];
        }
        $mch=Mch::findOne(['user_id'=>$this->user_id]);
        
         $store = Store::findOne($this->store_id);
        if (!$store)
            return new ApiResponse(1, 'Store不存在');



        $this->getMiaoshaData();


        $xtcount=Article::find()->where('article_cat_id = 3 and is_delete = 0')->count();

        $article=Article::find()->where('article_cat_id = 2 and is_delete = 0')->asArray()->all();
        $notice=Option::get('notice', $this->store_id, 'admin');
        $video=Video::find()->where('classify_id = 5 and is_delete =1 and is_show = 1')->asArray()->all();
        foreach ($video as $k =>$v){
            $v['addtime']=date('Y/m/d',$v['addtime']);
            $video[$k]=$v;
        }

        $banner_list = Banner::find()->where([
            'is_delete' => 0,
            'store_id' => $this->store_id,
            'type' => 1,
        ])->orderBy('sort ASC')->asArray()->all();
        foreach ($banner_list as $i => $banner) {
            if (!$banner['open_type']) {
                $banner_list[$i]['open_type'] = 'navigate';
            }
        }

        $nav_icon_list = HomeNav::find()->where([
            'is_delete' => 0,
            'store_id' => $this->store_id,
        ])->orderBy('sort ASC,addtime DESC')->select('name,pic_url,url,name,open_type')->asArray()->all();
        foreach ($nav_icon_list as &$value) {
            if ($value['open_type'] == 'wxapp') {
                $res = $this->getUrl($value['url']);
                $value['appId'] = $res[2];
                $value['path'] = urldecode($res[4]);
            }
        }
        unset($value);

        $module_list = $this->getModuleList($store);
//                echo "<pre>";
//        var_dump($module_list);exit;
        $cat_str = '';
        foreach ($module_list as $index => $value) {
            if ($value['name'] == 'cat') {
                $cat_str = 'all';
                break;
            }
            if ($value['name'] == 'single_cat') {
                $cat_str .= "cat_id={$value['cat_id']}&";
                $cat_id[] = $value['cat_id'];
            }
        }
        $cat_list_cache_key = md5('cat_list_cache_key&store_id=' . $this->store_id . $cat_str);
        $cat_list = \Yii::$app->cache->get($cat_list_cache_key);

        if (!$cat_list) {
            $query = Cat::find()->where([
                'is_delete' => 0,
                'parent_id' => 0,
                'store_id' => $this->store_id,
            ]);
            if ($cat_str != 'all') {
                $query->andWhere(['id' => $cat_id]);
            }
            $cat_list = $query->orderBy('sort ASC')->asArray()->all();
            foreach ($cat_list as $i => $cat) {
                $cat_list[$i]['page_url'] = '/pages/list/list?cat_id=' . $cat['id'];
                $cat_list[$i]['open_type'] = 'navigate';
                $cat_list[$i]['cat_pic'] = $cat['pic_url'];
                $goods_list_form = new GoodsListForm();
                $goods_list_form->store_id = $this->store_id;
                $goods_list_form->cat_id = $cat['id'];
                $goods_list_form->limit = $store->cat_goods_count?$store->cat_goods_count:4;
                $goods_list_form_res = $goods_list_form->search();
                if ($goods_list_form_res->code == 0) {
                    $goods_list_data = new \ArrayObject($goods_list_form_res->data, \ArrayObject::ARRAY_AS_PROPS);
                    $goods_list = $goods_list_data['list'];
                } else {
                    $goods_list = [];
                }
                $cat_list[$i]['goods_list'] = $goods_list;
            }
            \Yii::$app->cache->set($cat_list_cache_key, $cat_list, 60 * 10);
        }

        $block_list = HomeBlock::find()->where(['store_id' => $this->store_id, 'is_delete' => 0])->all();
        $new_block_list = [];
        foreach ($block_list as $item) {
            $data = json_decode($item->data, true);
            foreach ($data['pic_list'] as &$value) {
                if ($value['open_type'] == 'wxapp') {
                    $res = $this->getUrl($value['url']);
                    $value['appId'] = $res[2];
                    $value['path'] = urldecode($res[4]);
                }
            }
            unset($value);
            $new_block_list[] = [
                'id' => $item->id,
                'name' => $item->name,
                'data' => $data,
                'style' => $item->style
            ];
        }
        $user_id = \Yii::$app->user->identity->id;
        $coupon_form = new CouponListForm();
        $coupon_form->store_id = $this->store_id;
        $coupon_form->user_id = $user_id;
        $coupon_list = $coupon_form->getList();

        $topic_list = Topic::find()->where(['store_id' => $this->store_id, 'is_delete' => 0])->orderBy('sort ASC,addtime DESC')->limit(6)->select('id,title')->asArray()->all();
        $option = Option::getList('service,web_service,web_service_url,wxapp', $this->store_id, 'admin', '');
        foreach ($option as $index => $value) {
            if (in_array($index, ['wxapp'])) {
                $option[$index] = json_decode($value, true);
            }
        }
        $update_form = new HomePageModule();
        $update_form->store_id = $this->store_id;
        $update_list = $update_form->search_1();
        foreach ($update_list as $index => $value) {
            if ($index == 'video') {
                foreach ($value as $k => $v) {
                    $res = GetInfo::getVideoInfo($v['url']);
                    if ($res && $res['code'] == 0) {
                        $update_list[$index][$k]['url'] = $res['url'];
                    }
                }
            }
        }

        $mch_list = [];
        foreach ($module_list as $m) {
            if ($m['name'] == 'mch') {
                $mch_list = $this->getMchList();
                break;
            }
        }
        $activity_goods=$this->getCmList();
        $data = [
            'module_list' => $module_list,
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'is_coupon' => $store->is_coupon,
                'show_customer_service' => $store->show_customer_service,
                'dial' => $store->dial,
                'dial_pic' => $store->dial_pic,
                'service' => $option['service'],
                'copyright' => $store->copyright,
                'copyright_pic_url' => $store->copyright_pic_url,
                'copyright_url' => $store->copyright_url,
                'contact_tel' => $store->contact_tel,
                'cat_style' => $store->cat_style,
                'cut_thread' => $store->cut_thread,
                'address' => $store->address,
                'is_offline' => $store->is_offline,
                'option' => $option,
                'purchase_frame' => $store->purchase_frame,
            ],
            'article'=>$article,
            'banner_list' => $banner_list,
            'nav_icon_list' => $nav_icon_list,
            'cat_goods_cols' => $store->cat_goods_cols,
            'cat_list' => $cat_list,
            'block_list' => $new_block_list,
            'coupon_list' => $coupon_list,
            'topic_list' => $topic_list,
            'nav_count' => $store->nav_count,
            'notice' =>$notice,
           'miaosha' => $this->getMiaoshaData(),
            'pintuan' => $this->getPintuanData(),
            /*  'nongchanpin'=>['goods_list'=>$activity_goods['zhuanchang']],
             'gongyiping'=>['goods_list'=>$activity_goods['shequtuan']],*/
            'video'=>$video,
          /*  'qianggc'=>['goods_list'=>$activity_goods['qianggc']],*/
         /*   'bargain'=>$this->getBrList(),*/
            'yuyue' => $this->getYuyueData(),
            'district'=>$district,
            'update_list' => $update_list,
            'act_modal_list' => $this->getActModalList(),
            'mch_list' => $mch_list,
            'user_type'=>$use->type,
            'review_status'=>$mch->review_status,
            'xtcount'=>$xtcount
        ];
//        echo "<pre>";
//        var_dump($data);exit;
        return new ApiResponse(0, 'success', $data);
    }

    private function getBlockList()
    {

    }
    //获取Cm_goods
    private function getCmList()
    {
        $cmGoods = new CmGoodsForm();

        $cmGoods->store_id = $this->store_id;
        $cmGoods->user_id = \Yii::$app->user->id;

        $goods = $cmGoods->getCmList();
        return $goods;
    }
    //获取首页砍价商品
    private function getBrList(){
        $brGoods=new BrGoodsForm();
        $brGoods->store_id=$this->store_id;
        $brGoods->user_id = \Yii::$app->user->id;
        $mch_id=0;
        $query = BrGoods::find()
            ->alias('g')
            ->andWhere(['g.is_delete' => 0, 'g.store_id' => $this->store_id,'g.is_hot'=>1])
            ->select(['g.id', 'g.name','g.cover_pic as pic','g.price','g.total_num as sale_num','g.date_start','g.date_end','g.live_link','g.original_price'])
            ->leftJoin('{{%br_cat}} c', 'g.cat_id=c.id');
        if ($mch_id < 0)
        {
            $query->andWhere(['>', 'g.mch_id', 0]);
        }else if ($mch_id >= 0) {
            $query->andWhere(['g.mch_id' => $mch_id]);
        }

        $list = $query
            ->orderBy('g.sort ASC')
            ->asArray()
            ->limit(5)
            ->all();
        return ['goods_list'=>$list];

    }
    /**
     * @param Store $store
     */
    private function getModuleList($store)
    {
        $list = json_decode($store->home_page_module, true);
//        echo "<pre>";
//        var_dump($list);exit;
        if (!$list) {
            $list = [
                [
                    'name' => 'notice',
                ],
                [
                    'name' => 'banner',
                ],
                [
                    'name' => 'search',
                ],
                [
                    'name' => 'nav',
                ],
                [
                    'name' => 'topic',
                ],
                [
                    'name' => 'coupon',
                ],
                [
                    'name' => 'cat',
                ],
            ];
        } else {
            $new_list = [];
            foreach ($list as $item) {
                if (stripos($item['name'], 'block-') !== false) {
                    $names = explode('-', $item['name']);
                    $new_list[] = [
                        'name' => $names[0],
                        'block_id' => $names[1],
                    ];
                } elseif (stripos($item['name'], 'single_cat-') !== false) {
                    $names = explode('-', $item['name']);
                    $new_list[] = [
                        'name' => $names[0],
                        'cat_id' => $names[1],
                    ];
                } elseif (stripos($item['name'], 'video-') !== false) {
                    $names = explode('-', $item['name']);
                    $new_list[] = [
                        'name' => $names[0],
                        'video_id' => $names[1],
                    ];
                } else {
                    $new_list[] = $item;
                }
            }
            $list = $new_list;
        }
        return $list;
    }

    public function getMiaoshaData()
    {
        $list = MiaoshaGoods::find()->alias('mg')
            ->select('mg.id,g.name,g.cover_pic AS pic,g.original_price AS price,mg.attr,mg.start_time')
            ->leftJoin(['g' => MsGoods::tableName()], 'mg.goods_id=g.id')
            ->where([
                'AND',
                [
                    'mg.is_delete' => 0,
                    'g.is_delete' => 0,
                    'mg.open_date' => date('Y-m-d'),
                    'g.status' => 1,
                    'mg.start_time' => date('H'),
                    'mg.store_id' => $this->store_id,
                ],
            ])
            ->orderBy('g.sort ASC,g.addtime DESC')
            ->limit(10)
            ->asArray()->all();

        if (empty($list)) {
            $lastMsStartTime = MiaoshaGoods::find()->alias('mg')
                ->select('start_time')->andWhere([
                    'mg.is_delete' => 0,
                    'mg.open_date' => date('Y-m-d'),
                    'mg.store_id' => $this->store_id,
                    'g.is_delete' => 0,
                    'g.status' => 1,
                ])->leftJoin(['g' => MsGoods::tableName()], 'mg.goods_id=g.id')
                ->andWhere(['>', 'mg.start_time', date('H')])->orderBy('mg.start_time ASC')->scalar();

            $list = MiaoshaGoods::find()->alias('mg')
                ->select('mg.id,g.name,g.cover_pic AS pic,g.original_price AS price,mg.attr,mg.start_time')
                ->leftJoin(['g' => MsGoods::tableName()], 'mg.goods_id=g.id')
                ->where([
                    'AND',
                    [
                        'mg.is_delete' => 0,
                        'g.is_delete' => 0,
                        'mg.open_date' => date('Y-m-d'),
                        'g.status' => 1,
                        'mg.start_time' => $lastMsStartTime,
                        'mg.store_id' => $this->store_id,
                    ],
                ])
                ->orderBy('g.sort ASC,g.addtime DESC')
                ->limit(10)
                ->asArray()->all();
        }
        $startTime = intval(date('H'));
        foreach ($list as $i => $item) {
            $item['attr'] = json_decode($item['attr'], true);
            $list[$i] = $item;
            $price_list = [];
            foreach ($item['attr'] as $attr) {
                if ($attr['miaosha_price'] <= 0) {
                    $price_list[] = doubleval($item['price']);
                } else {
                    $price_list[] = doubleval($attr['miaosha_price']);
                }
            }
            $list[$i]['price'] = number_format($list[$i]['price'], 2, '.', '');
            $list[$i]['miaosha_price'] = number_format(min($price_list), 2, '.', '');
            unset($list[$i]['attr']);
            $startTime = $item['start_time'];
        }
        if (count($list) == 0)
            return [
                'name' => '暂无秒杀活动',
                'rest_time' => 0,
                'goods_list' => null,
            ];
        return [
//            'name' => intval(date('H')) . '点场',
            'name' => $startTime . '点场',
            'rest_time' => max(intval(strtotime(date('Y-m-d ' . $startTime . ':59:59')) - time()), 0),
            'goods_list' => $list,
        ];
    }

    public function getPintuanData()
    {
        $mch_id=$_GET['mch_id']?$_GET['mch_id']:0;
        $num_query = PtOrderDetail::find()->alias('pod')
            ->select('pod.goods_id,SUM(pod.num) AS sale_num')
            ->leftJoin(['po' => PtOrder::tableName()], 'pod.order_id=po.id')
            ->where([
                'AND',
                [
                    'pod.is_delete' => 0,
                    'po.is_delete' => 0,
                    'po.is_pay' => 1,
                ],
            ])->groupBy('pod.goods_id');
        $list = PtGoods::find()->alias('pg')
            ->select('pg.*,pod.sale_num')
            ->leftJoin(['pod' => $num_query], 'pg.id=pod.goods_id')
            ->where([
                'AND',
                [
                    'pg.is_delete' => 0,
                    'pg.status' => 1,
                    'pg.store_id' => $this->store_id,
                    'mch_id'=>$mch_id,
                    'is_hot'=>1,
                ],
            ])->orderBy('pg.is_hot DESC,pg.sort ASC,pg.addtime DESC')
            ->limit(10)
            ->asArray()->all();
        $new_list = [];
        foreach ($list as $item) {
            $new_list[] = [
                'id' => $item['id'],
                'pic' => $item['cover_pic'],
                'name' => $item['name'],
                'price' => number_format($item['price'], 2, '.', ''),
                'sale_num' => intval($item['sale_num'] ? $item['sale_num'] : 0) + intval($item['virtual_sales'] ? $item['virtual_sales'] : 0),
            ];
        }
//        echo "<pre>";
//        var_dump($new_list);exit;
        return [
            'goods_list' => $new_list,
        ];
    }

    /**
     * 获取首页活动弹窗列表
     */
    public function getActModalList()
    {
        $act_list = [];
        $fxhb_act = $this->getFxhbAct();
        if ($fxhb_act) {
            $act_list[] = $fxhb_act;
        }
        foreach ($act_list as $i => $item) {
            if ($i == 0)
                $act_list[$i]['show'] = true;
            else
                $act_list[$i]['show'] = false;
        }
        return $act_list;
    }

    private function getFxhbAct()
    {
        $act_data = [
            'name' => '一起拆红包',
            'pic_url' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/fxhb/act_modal.png',
            'pic_width' => 750,
            'pic_height' => 696,
            'url' => '/pages/fxhb/open/open',
            'open_type' => 'navigate',
        ];
        $fxhb_setting = FxhbSetting::findOne([
            'store_id' => $this->store_id,
        ]);
        if (!$fxhb_setting || $fxhb_setting->game_open != 1) {
            return null;
        }
        if ($user = \Yii::$app->user->isGuest) {
            return $act_data;
        }
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        /** @var FxhbHongbao $hongbao */
        $hongbao = FxhbHongbao::find()->where([
            'user_id' => $user->id,
            'store_id' => $this->store_id,
            'parent_id' => 0,
            'is_finish' => 0,
            'is_expire' => 0,
        ])->one();
        if (!$hongbao)
            return $act_data;
        if (time() > $hongbao->expire_time) {
            $hongbao->is_expire = 1;
            $hongbao->save();
            return $act_data;
        }
        return null;
    }

    public function getYuyueData()
    {
        $list = YyGoods::find()->where(['store_id' => $this->store_id, 'is_delete' => 0, 'status' => 1])
            ->select(['id', 'name', 'cover_pic', 'price'])
            ->limit(10)->orderBy(['sort' => SORT_ASC])->asArray()->all();
        return $list;
    }

    public function getMchList()
    {
        $list = Mch::find()->where([
            'store_id' => $this->store_id,
            'is_delete' => 0,
            'is_open' => 1,
            'is_lock' => 0,
        ])->select('id,name,logo')
            ->orderBy('sort ASC,addtime DESC')->limit(10)
            ->asArray()->all();
        return $list ? $list : [];
    }

    private function getUrl($url)
    {
        preg_match('/^[^\?+]\?([\w|\W]+)=([\w|\W]*?)&([\w|\W]+)=([\w|\W]*?)$/', $url, $res);
        return $res;
    }

}