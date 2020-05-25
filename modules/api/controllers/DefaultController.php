<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 15:15
 */

namespace app\modules\api\controllers;

use app\modules\api\models\LiveForm;
use app\modules\api\models\NewIndexForm;
use app\modules\api\models\ProtocolForm;
use Yii;
use app\hejiang\ApiResponse;
use app\hejiang\BaseApiResponse;
use app\models\AppNavbar;
use app\models\Article;
use app\models\Banner;
use app\models\Cat;
use app\models\FormId;
use app\models\Goods;
use app\models\Option;
use app\models\Setting;
use app\models\Store;
use app\models\StorePermission;
use app\models\UploadConfig;
use app\models\UploadForm;
use app\modules\api\behaviors\LoginBehavior;
use app\modules\api\models\CatListForm;
use app\modules\api\models\CommentListForm;
use app\modules\api\models\CouponListForm;
use app\modules\api\models\DistrictForm;
use app\modules\api\models\GoodsAttrInfoForm;
use app\modules\api\models\GoodsForm;
use app\modules\api\models\GoodsListForm;
use app\modules\api\models\GoodsQrcodeForm;
use app\modules\api\models\IndexForm;
use app\modules\api\models\SearchForm;
use app\modules\api\models\ShareQrcodeForm;
use app\modules\api\models\ShopListForm;
use app\modules\api\models\StoreFrom;
use app\modules\api\models\TopicForm;
use app\modules\api\models\TopicListForm;
use app\modules\api\models\VideoForm;
use app\modules\api\models\ShopForm;
use Curl\Curl;


use yii\data\Pagination;
use yii\helpers\VarDumper;
use app\modules\api\models\TopicTypeForm;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * 首页接口
     */
    public function actionIndex()
    {

//        $form = new IndexForm();
        $form = new NewIndexForm();
        return $form->search();
    }

    public function actionGetLive()
    {
        $form = new LiveForm();
        return $form->getLiveList();
    }

    /**
     *获取协议
     */
    public function actionProtocol() {
        $form = new ProtocolForm();
        $form->attributes = Yii::$app->request->post();
        return $form->search();
    }

    /**
     * 分类列表
     */
    public function actionCatList()
    {
        $form = new CatListForm();
        $form->attributes = \Yii::$app->request->get();
        $form->store_id = $this->store->id;
        return $form->search();
    }

    /**
     * 购买数据
     */
    public function actionBuyData()
    {
        $key = "buy_data";
        $cache = \Yii::$app->cache;
        $data = $cache->get($key);
        $cha_time = time() - (int)json_decode($data)->time;
        $data = json_decode($data);
        unset($data->time);

        if ($data && $data->store_id == $this->store_id) {
            return new BaseApiResponse([
                'code' => 0,
                'data' => $data,
                'cha_time' => $cha_time,
                'md5' => md5(json_encode($data)),
            ]);
        } else {
            return new ApiResponse(1, 'Null');
        }
    }

    /**
     * 商品列表
     */
    public function actionGoodsList()
    {
        $form = new GoodsListForm();
        $form->attributes = \Yii::$app->request->get();
        $form->type = \Yii::$app->request->get('type');
        $form->p = \Yii::$app->request->get('p');
        $form->user_id = \Yii::$app->user->id;
        $form->store_id = $this->store->id;
        return $form->search();
    }


    /**
     * 商品推荐
     */
    public function actionGoodsRecommend()
    {
        if (!$this->store->is_recommend) {
            return new ApiResponse(1, 'error');
        }

        $form = new GoodsListForm();
        $form->attributes = \Yii::$app->request->get();
        $form->goods_id = \Yii::$app->request->get('goods_id');
        $form->recommend_count = $this->store->recommend_count;
        $form->limit = 6;
        $form->store_id = $this->store->id;
        return $form->recommend();
    }

    /**
     * 商品详情
     */
    public function actionGoods()
    {
        $form = new GoodsForm();
        $form->attributes = \Yii::$app->request->get();
        if (!\Yii::$app->user->isGuest) {
            $form->user_id = \Yii::$app->user->id;
        }
        $form->store_id = $this->store->id;
        return $form->search();
    }

    /**
     * 省市区数据
     */
    public function actionDistrict()
    {
        $form = new DistrictForm();
        return $form->search();
    }


    public function actionGoodsAttrInfo()
    {
        $form = new GoodsAttrInfoForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->search();
    }

    public function actionStore()
    {
        //分销设置
        $share_setting = Setting::find()->alias('s')
            ->where(['s.store_id' => $this->store_id])
            ->leftJoin('{{%qrcode}} q', 'q.store_id=s.store_id and q.is_delete=0')
            ->select(['s.*', 'q.qrcode_bg'])
            ->asArray()->one();
        if (is_null($share_setting)) {
            $share_setting = new Setting();
            $share_setting->store_id = $this->store_id;
            $share_setting->save();
            $share_setting = Setting::find()->alias('s')
                ->where(['s.store_id' => $this->store_id])
                ->leftJoin('{{%qrcode}} q', 'q.store_id=s.store_id and q.is_delete=0')
                ->select(['s.*', 'q.qrcode_bg'])
                ->asArray()->one();
        }
        $form = new StoreFrom();
        $form->store = $this->store;
        $wxappImg = $form->search();
        $data = [
            'store_name' => $this->store->name,
            'contact_tel' => $this->store->contact_tel,
            'show_customer_service' => $this->store->show_customer_service,
            'share_setting' => $share_setting,
            'store' => (object)[
                'id' => $this->store->id,
                'name' => $this->store->name,
                'copyright' => $this->store->copyright,
                'copyright_pic_url' => $this->store->copyright_pic_url,
                'copyright_url' => $this->store->copyright_url,
                'contact_tel' => $this->store->contact_tel,
                'show_customer_service' => $this->store->show_customer_service,
                'cat_style' => $this->store->cat_style,
                'address' => $this->store->address,
                'is_offline' => $this->store->is_offline,
                'is_coupon' => $this->store->is_coupon,
                'service' => Option::get('service', $this->store->id, 'admin', '/images/icon-service.png'),
            ],
            'permission_list' => StorePermission::getOpenPermissionList($this->store),
            'wxapp_img' => $wxappImg
        ];
        return new ApiResponse(0, 'success', $data);
    }

    public function actionUploadImage()
    {
        $form = new UploadForm();
        $upload_config = UploadConfig::findOne(['store_id' => $this->store->id]);
        $form->upload_config = $upload_config;
        return new BaseApiResponse($form->saveImage('image'));
    }

    //商品评价列表
    public function actionCommentList()
    {
        $form = new CommentListForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->search();
    }

    //文章列表
    public function actionArticleList($page = 1, $limit = 10)
    {

        $query = Article::find()->where([
            'store_id' => $this->store->id,
            'is_delete' => 0,
            'article_cat_id' => \Yii::$app->request->get('cat_id'),
        ]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $page - 1, 'pageSize' => $limit]);
        $list = $query->orderBy('sort DESC,addtime DESC')
            ->limit($pagination->limit)->offset($pagination->offset)
            ->select('id,title,content,image,addtime,detail')->asArray()->all();

        return new BaseApiResponse(['code' => 0, 'msg' => "操作成功", 'data' => [
            'row_count' => $count, +
            'page_count' => $pagination->pageCount,
            'list' => $list,
        ]]);

    }

    //文章详情
    public function actionArticleDetail()
    {
        $id = \Yii::$app->request->get('id');
        if ($id == 'about_us') {
            $model = Article::findOne([
                'store_id' => $this->store->id,
                'article_cat_id' => 1,
            ]);
            if (!$model) {
                $model = new Article();
            }

            $data = [
                'id' => $model->id,
                'title' => $model->title,
                'content' => $model->content,
            ];
            return new ApiResponse(0, 'success', $data);
        } else {
            $model = Article::find()->where([
                'is_delete' => 0,
                'id' => $id,
            ])->select('id,title,content')->asArray()->one();
            if (empty($model)) {
                return new ApiResponse(1, '内容不存在');
            }
            return new ApiResponse(0, 'success', $model);
        }
    }

    //核销二维码  已废弃
    public function actionQrcode($path)
    {
        include \Yii::$app->basePath . '/extensions/phpqrcode/phpqrcode.php';
        \QRcode::png($path);
    }

    public function actionVideoList()
    {
        $form = new VideoForm();
        $form->store_id = $this->store_id;
        $form->attributes = \Yii::$app->request->get();
        $form->limit = 10;
        return $form->getList();
    }

    public function actionCouponList()
    {
        $form = new CouponListForm();
        $form->store_id = $this->store_id;
        $form->user_id = \Yii::$app->user->identity->id;
        $list = $form->getList();
        return new ApiResponse(0, 'success', ['list' => $list]);
    }

    //获取商品二维码海报
    public function actionGoodsQrcode()
    {
        $form = new ShareQrcodeForm();
        $form->attributes = \Yii::$app->request->get();

        $form->store_id = $this->store_id;
        $form->type = 0;
        if (!\Yii::$app->user->isGuest) {
            $form->user = \Yii::$app->user->identity;
            $form->user_id = \Yii::$app->user->id;
        }
        return new BaseApiResponse($form->search());
    }

    //专题列表
    public function actionTopicList()
    {
        $form = new TopicListForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->search();
    }

    //专题详情
    public function actionTopic()
    {
        $form = new TopicForm();
        $form->attributes = \Yii::$app->request->get();

        return $form->search();
    }

    //专题分类
    public function actionTopicType()
    {
        $form = new TopicTypeForm();
        return $form->search();
    }

    //底部导航栏
    public function actionNavbar()
    {

        $navbar = AppNavbar::getNavbar($this->store->id);
        // TODO 版本兼容旧版
        $version = \Yii::$app->request->get('_version');
        if (empty($version)) {
            foreach ($navbar['navs'] as &$item) {
                if (isset($item['params']) && count($item['params'] > 0)) {
                    $params = [];
                    foreach ($item['params'] as $k => $i) {
                        if ($item['open_type'] === 'wxapp') {
                            if ($i['key'] === 'path') {
                                $item['path'] = $i['value'];
                            } else {
                                $item['appid'] = $i['appid'];
                            }
                        }
                        if ($item['open_type'] === 'web') {
                            $item['web'] = $i['value'];
                        }
                        if ($item['open_type'] === 'tel') {
                            $item['tel'] = $i['value'];
                        }
                    }
                }
            }
        } else {
            // 新版
            foreach ($navbar['navs'] as &$item) {
                $newNavs = [];
                if (isset($item['params']) && $item['open_type'] !== 'redirect' && $item['open_type'] !== '') {
                    foreach ($item['params'] as $k => $v) {
                        $newNavs[$v['key']] = $v['value'];
                    }
                    $item['params'] = $newNavs;
                } else {
                    if (isset($item['params']) && !empty($item['params'])) {
                        $param = '';
                        foreach ($item['params'] as $k => $v) {
                            if ($k === 0) {
                                $param .= '?' . $v['key'] . '=' . $v['value'];
                            } else {
                                $param .= '&' . $v['key'] . '=' . $v['value'];
                            }
                        }
                        $item['new_url'] = $item['url'] . $param;
                    } else {
                        $item['new_url'] = $item['url'];
                    }
                }
            }
        }

        return new ApiResponse(0, 'success', $navbar);
    }

    //顶部导航栏颜色
    public function actionNavigationBarColor()
    {
        $navigation_bar_color = Option::get('navigation_bar_color', $this->store->id, 'app', [
            'frontColor' => '#000000',
            'backgroundColor' => '#ffffff',
        ]);
        return new ApiResponse(0, 'success', $navigation_bar_color);
    }

    //门店列表
    public function actionShopList()
    {
        $form = new ShopListForm();
        $form->store_id = $this->store->id;
        $form->user = \Yii::$app->user->identity;
        $form->user_id = \Yii::$app->user->id;
        $form->attributes = \Yii::$app->request->get();
        return $form->search();
    }

    //我关注的店铺
    public function actionShoption()
    {
        $form = new ShopListForm();
        $form->store_id = $this->store->id;
        $form->user_id = \Yii::$app->user->id;
        return $form->attention();
    }

    //关注店铺
    public function actionPayshop()
    {
        $form = new ShopListForm();
        $form->shop_id = \Yii::$app->request->get('shop_id');
        $form->type = \Yii::$app->request->get('type');
        $form->user_id = \Yii::$app->user->id;
//            var_dump(\Yii::$app->user->id);exit;
        return $form->payshop();
    }

    //门店详情
    public function actionShopDetail()
    {
        $form = new ShopForm();
        $form->store_id = $this->store->id;
        $form->user = \Yii::$app->user->identity;
//        var_dump($form->user);exit;
        $form->attributes = \Yii::$app->request->get();
        return $form->search();
    }

    /**
     * 商品列表
     */
    public function actionSearch()
    {
        $form = new SearchForm();
        $form->attributes = \Yii::$app->request->get();
        $form->store_id = $this->store->id;
        return $form->search();
    }


    //将商品添加到历史记录
    public function actionPaygoods()
    {
        $form = new SearchForm();
        $form->user_id = \Yii::$app->user->id;
        $form->goods_id = \Yii::$app->request->get('goods_id');
        return $form->paygoods();


    }

    //浏览记录商品列表
    public function actionVisitlist()
    {
        $form = new SearchForm();
        $form->attributes = \Yii::$app->request->get();
        $form->user_id = \Yii::$app->user->id;
        return $form->goodslist();


    }

    //商户商品列表
    public function actionMchGoods()
    {
        $form = new SearchForm();
        $form->attributes = \Yii::$app->request->get();
        $form->store_id = $this->store->id;
        $form->type = \Yii::$app->request->get('type');
        $form->is_sale = \Yii::$app->request->get('is_sale') ? \Yii::$app->request->get('is_sale') : 0;
        $form->mch_id = \Yii::$app->request->get('mch_id');
        $form->cat_id = \Yii::$app->request->get('cat_id');
        return $form->getMchGoods();
    }

    //获取店铺视频
    public function actionMchVideo()
    {
        $form = new ShopForm();
        $form->store_id = $this->store->id;
        $form->mch_id = \Yii::$app->request->get('mch_id');
        $form->page = \Yii::$app->request->get('page');
        $form->cat_id = \Yii::$app->request->get('cat_id');
        return $form->getvideo();
    }
}
