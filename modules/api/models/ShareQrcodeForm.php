<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 13:39
 */

namespace app\modules\api\models;

use app\utils\GrafikaHelper;
use app\models\Goods;
use app\models\MiaoshaGoods;
use app\models\MsGoods;
use app\models\PtGoods;
use app\models\Qrcode;
use app\models\Store;
use app\models\YyGoods;
use app\models\Share;

use Curl\Curl;
use Grafika\Color;
use Grafika\Grafika;

class ShareQrcodeForm extends ApiModel
{
    public $store_id;
    public $user;
    public $user_id;

    public $goods_id;
    public $type; //0--商城海报 1--秒杀海报 2--拼团海报 3--预约海报 4--分销海报

    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'in', 'range' => [0, 1, 2, 3, 4]],
            [['goods_id'], 'integer']
        ];
    }

    public function search()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }

        $this->user_id = $this->user ? $this->user->id : ($this->user_id ? $this->user_id : 0);
        if ($this->type == 0) {
            return $this->goods_qrcode();
        } elseif ($this->type == 1) {
            return $this->ms_goods_qrcode();
        } elseif ($this->type == 2) {
            return $this->pt_goods_qrcode();
        } elseif ($this->type == 3) {
            return $this->yy_goods_qrcode();
        } elseif ($this->type == 4) {


            return $this->share_qrcode();
        } else {
            return [
                'code' => 1,
                'msg' => 'error'
            ];
        }
    }

    //商城商品海报
    public function goods_qrcode()
    {
        if (!$this->goods_id) {
            return [
                'code' => 1,
                'msg' => '未知的商品'
            ];
        }
        $goods = Goods::findOne($this->goods_id);
        if (!$goods) {
            return [
                'code' => 1,
                'msg' => '商品不存在',
            ];
        }
        $store = Store::findOne($this->store_id);

        $goods_pic_url = $goods->getGoodsPic(0)->pic_url;

        $goods_pic_save_path = \Yii::$app->basePath . '/web/temp/';
        $version = hj_core_version();
        $goods_pic_save_name = md5("v={$version}&goods_id={$goods->id}&goods_name={$goods->name}&store_name={$store->name}&user_id={$this->user_id}&goods_pic_url={$goods_pic_url}") . '.jpg';

        $pic_url = str_replace('http://', 'https://', \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/temp/' . $goods_pic_save_name);

        if (file_exists($goods_pic_save_path . $goods_pic_save_name)) {
            return [
                'code' => 0,
                'data' => [
                    'goods_name' => $goods->name,
                    'pic_url' => $pic_url . '?v=' . time(),
                ],
            ];
        }

        $goods_pic_path = $this->saveTempImage($goods_pic_url);
        if (!$goods_pic_path) {
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：商品图片丢失',
            ];
        }

        $goods_qrcode_dst = \Yii::$app->basePath . '/web/statics/images/goods-qrcode-dst.jpg';
        $font_path = \Yii::$app->basePath . '/web/statics/font/st-heiti-light.ttc';

        $editor = Grafika::createEditor(GrafikaHelper::getSupportEditorLib());
        $editor->open($goods_qrcode, $goods_qrcode_dst);
        $editor->open($goods_pic, $goods_pic_path);

        //获取小程序码图片
        $scene = "gid:{$goods->id},uid:{$this->user_id}";
        $wxapp_qrcode_file_res = $this->getQrcode($scene, 240, "pages/goods/goods");
        if ($wxapp_qrcode_file_res['code'] == 1) {
            unlink($goods_pic_path);
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：获取小程序码失败，' . $wxapp_qrcode_file_res['msg'],
            ];
        }
        $wxapp_qrcode_file_path = $wxapp_qrcode_file_res['file_path'];
        $editor->open($wxapp_qrcode, $wxapp_qrcode_file_path);

        $name_size = 30;
        $name_width = 670;
        //商品名称处理换行
        $name = $this->autowrap($name_size, 0, $font_path, $goods->name, $name_width, 2);
        //加商品名称
        $editor->text($goods_qrcode, $name, $name_size, 40, 750, new Color('#333333'), $font_path, 0);

        //裁剪商品图片
        //$editor->crop($goods_pic, 670, 670, 'smart');
        $editor->resizeFill($goods_pic, 670, 670);
        //附加商品图片
        $editor->blend($goods_qrcode, $goods_pic, 'normal', 1.0, 'top-left', 40, 40);

        //加商品价格
        $editor->text($goods_qrcode, '￥' . $goods->price, 45, 30, 910, new Color('#ff4544'), $font_path, 0);

        //加商城名称
        $editor->text($goods_qrcode, $store->name, 20, 40, 1170, new Color('#888888'), $font_path, 0);

        //调整小程序码图片
        $editor->resizeExactWidth($wxapp_qrcode, 240);
        //附加小程序码图片
        $editor->blend($goods_qrcode, $wxapp_qrcode, 'normal', 1.0, 'top-left', 470, 1040);

        //保存图片
        $editor->save($goods_qrcode, $goods_pic_save_path . $goods_pic_save_name, 'jpeg', 85);

        //删除临时图片
        unlink($goods_pic_path);
        unlink($wxapp_qrcode_file_path);

        return [
            'code' => 0,
            'data' => [
                'goods_name' => $goods->name,
                'pic_url' => $pic_url . '?v=' . time(),
            ],
        ];
    }

    //秒杀商品海报
    public function ms_goods_qrcode()
    {
        $store = Store::findOne($this->store_id);

        $miaosha_goods = MiaoshaGoods::findOne([
            'id' => $this->goods_id,
            'is_delete' => 0,
        ]);
        $goods = MsGoods::findOne($miaosha_goods->goods_id);
        if (!$goods) {
            return [
                'code' => 1,
                'msg' => '商品不存在',
            ];
        }
        $attr_data = json_decode($miaosha_goods->attr, true);
        $miaosha_price = 0.00;
        foreach ($attr_data as $i => $attr_data_item) {
            if ($miaosha_price == 0) {
                $miaosha_price = $attr_data_item['miaosha_price'];
            } else {
                $miaosha_price = min($miaosha_price, $attr_data_item['miaosha_price']);
            }
        }
        $goods_pic_url = $goods->cover_pic;

        $goods_pic_save_path = \Yii::$app->basePath . '/web/temp/';
        $version = hj_core_version();
        $goods_pic_save_name = md5("v={$version}&f=miaosha&goods_id={$miaosha_goods->id}&goods_name={$goods->name}&store_name={$store->name}&user_id={$this->user_id}&goods_pic_url={$goods_pic_url}") . '.jpg';

        $pic_url = str_replace('http://', 'https://', \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/temp/' . $goods_pic_save_name);
        if (file_exists($goods_pic_save_path . $goods_pic_save_name)) {
            return [
                'code' => 0,
                'data' => [
                    'goods_name' => $goods->name,
                    'pic_url' => $pic_url . '?v=' . time(),
                ],
            ];
        }

        $goods_pic_path = $this->saveTempImage($goods_pic_url);
        if (!$goods_pic_path) {
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：商品图片丢失',
            ];
        }

        $goods_qrcode_dst = \Yii::$app->basePath . '/web/statics/images/goods-qrcode-dst.jpg';
        $font_path = \Yii::$app->basePath . '/web/statics/font/st-heiti-light.ttc';

        $editor = Grafika::createEditor(GrafikaHelper::getSupportEditorLib());
        $editor->open($goods_qrcode, $goods_qrcode_dst);
        $editor->open($goods_pic, $goods_pic_path);
       // file_put_contents('/tmp/test.log',  $goods_pic.PHP_EOL,8);

        //获取小程序码图片
        $scene = "gid:{$miaosha_goods->id},uid:{$this->user_id}";
        $wxapp_qrcode_file_res = $this->getQrcode($scene, 240, "pages/miaosha/details/details");
        if ($wxapp_qrcode_file_res['code'] == 1) {
            unlink($goods_pic_path);
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：获取小程序码失败，' . $wxapp_qrcode_file_res['msg'],
            ];
        }
        $wxapp_qrcode_file_path = $wxapp_qrcode_file_res['file_path'];
        $editor->open($wxapp_qrcode, $wxapp_qrcode_file_path);

        $name_size = 30;
        $name_width = 670;
        //商品名称处理换行
        $name = $this->autowrap($name_size, 0, $font_path, $goods->name, $name_width, 2);
        //加商品名称
        $editor->text($goods_qrcode, $name, $name_size, 40, 750, new Color('#333333'), $font_path, 0);

        //裁剪商品图片
        //$editor->crop($goods_pic, 670, 670, 'smart');
        $editor->resizeFill($goods_pic, 670, 670);
        //附加商品图片
        $editor->blend($goods_qrcode, $goods_pic, 'normal', 1.0, 'top-left', 40, 40);

        //加商品价格
        $editor->text($goods_qrcode, '￥' . (!empty($miaosha_price) ? $miaosha_price : $goods->original_price), 45, 30, 910, new Color('#ff4544'), $font_path, 0);

        //加商城名称
        $editor->text($goods_qrcode, $store->name, 20, 40, 1170, new Color('#888888'), $font_path, 0);

        //调整小程序码图片
        $editor->resizeExactWidth($wxapp_qrcode, 240);
        //附加小程序码图片
        $editor->blend($goods_qrcode, $wxapp_qrcode, 'normal', 1.0, 'top-left', 470, 1040);

        //保存图片
        $editor->save($goods_qrcode, $goods_pic_save_path . $goods_pic_save_name, 'jpeg', 85);

        //删除临时图片
        unlink($goods_pic_path);
        unlink($wxapp_qrcode_file_path);

        return [
            'code' => 0,
            'data' => [
                'goods_name' => $goods->name,
                'pic_url' => $pic_url . '?v=' . time(),
            ],
        ];
    }

    //拼团海报
    public function pt_goods_qrcode()
    {
        $goods = PtGoods::findOne($this->goods_id);
        if (!$goods) {
            return [
                'code' => 1,
                'msg' => '商品不存在',
            ];
        }
        $store = Store::findOne($this->store_id);
        $goods_pic_url = $goods->cover_pic;

        $goods_pic_save_path = \Yii::$app->basePath . '/web/temp/';
        if (!file_exists($goods_pic_save_path)) {
            mkdir($goods_pic_save_path);
        }
        $version = hj_core_version();
        $goods_pic_save_name = md5("v={$version}&goods_id={$goods->id}&goods_name={$goods->name}&store_name={$store->name}&user_id={$this->user_id}&goods_pic_url={$goods_pic_url}") . '.jpg';

        $pic_url = str_replace('http://', 'https://', \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/temp/' . $goods_pic_save_name);
        if (file_exists($goods_pic_save_path . $goods_pic_save_name)) {
            return [
                'code' => 0,
                'data' => [
                    'goods_name' => $goods->name,
                    'pic_url' => $pic_url . '?v=' . time(),
                ],
            ];
        }

        $goods_pic_path = $this->saveTempImage($goods_pic_url);
        if (!$goods_pic_path) {
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：商品图片丢失',
            ];
        }

        $goods_qrcode_dst = \Yii::$app->basePath . '/web/statics/images/goods-qrcode-dst-1.png';
        $font_path = \Yii::$app->basePath . '/web/statics/font/st-heiti-light.ttc';

        $editor = Grafika::createEditor(GrafikaHelper::getSupportEditorLib());
        $editor->open($goods_qrcode, $goods_qrcode_dst);
        $editor->open($goods_pic, $goods_pic_path);

        //获取小程序码图片
        $scene = "gid:{$goods->id},uid:{$this->user_id}";
        $page = "pages/pt/details/details";
        $wxapp_qrcode_file_res = $this->getQrcode($scene, 240, $page);
        if ($wxapp_qrcode_file_res['code'] == 1) {
            unlink($goods_pic_path);
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：获取小程序码失败，' . $wxapp_qrcode_file_res['msg'],
            ];
        }
        $wxapp_qrcode_file_path = $wxapp_qrcode_file_res['file_path'];
        $editor->open($wxapp_qrcode, $wxapp_qrcode_file_path);
        //裁剪商品图片
        $editor->resizeFill($goods_pic, 690, 690);
        //附加商品图片
        $editor->blend($goods_qrcode, $goods_pic, 'normal', 1.0, 'top-left', 30, 126);


        if ($this->user) {
            $user = $this->user;
            // 用户头像
            $user_pic_path = $this->saveTempImage($user->avatar_url);
            if (!$user_pic_path) {
                return [
                    'code' => 1,
                    'msg' => '获取商品海报失败：用户头像丢失',
                ];
            }

            list($w, $h) = getimagesize($user_pic_path);
            $user_pic_path = $this->test($user_pic_path, $goods_pic_save_path, $w, $h);
            $editor->open($user_pic, $user_pic_path);
            //调整用户头像图片
            $editor->resizeExactWidth($user_pic, 68);
            //附加用户头像图片
            $editor->blend($goods_qrcode, $user_pic, 'normal', 1.0, 'top-left', 30, 30);

            // 用户名处理
            $username = $this->setName($user->nickname);
            $editor->text($goods_qrcode, $username, 20, 128, 56, new Color('#5b85cf'), $font_path, 0);
            $namewitch = imagettfbbox(20, 0, $font_path, $username);
            $editor->text($goods_qrcode, '分享给你一个商品', 20, (132 + $namewitch[2]), 56, new Color('#353535'), $font_path, 0);
            unlink($user_pic_path);
        } else {
            $editor->text($goods_qrcode, '分享给你一个商品', 20, 30, 56, new Color('#353535'), $font_path, 0);
        }

        $name_size = 20;
        $name_width = 670;
        //商品名称处理换行
        $name = $this->autowrap($name_size, 0, $font_path, $goods->name, $name_width, 2);
        //加商品名称
        $editor->text($goods_qrcode, $name, $name_size, 30, 844, new Color('#353535'), $font_path, 0);

        // 商品价格钱币符
        $editor->text($goods_qrcode, '￥', 20, 30, 962, new Color('#ff5c5c'), $font_path, 0);
        //加商品价格
        $editor->text($goods_qrcode, $goods->price, 34, 48, 950, new Color('#ff5c5c'), $font_path, 0);

        //加商城名称
//        $editor->text($goods_qrcode, $store->name, 20, 40, 1170, new Color('#888888'), $font_path, 0);

        //调整小程序码图片
        $editor->resizeExactWidth($wxapp_qrcode, 160);
        //附加小程序码图片
        $editor->blend($goods_qrcode, $wxapp_qrcode, 'normal', 1.0, 'top-left', 536, 948);
//        $editor->blend($goods_qrcode, $wxapp_qrcode, 'normal', 1.0, 'top-left', 470, 1040);

        //保存图片
        $editor->save($goods_qrcode, $goods_pic_save_path . $goods_pic_save_name, 'jpeg', 85);

        //删除临时图片
        unlink($goods_pic_path);
        unlink($wxapp_qrcode_file_path);

        return [
            'code' => 0,
            'data' => [
                'goods_name' => $goods->name,
                'pic_url' => $pic_url . '?v=' . time(),
            ],
        ];
    }

    //预约海报
    public function yy_goods_qrcode()
    {
        $goods = YyGoods::findOne($this->goods_id);
        if (!$goods) {
            return [
                'code' => 1,
                'msg' => '商品不存在',
            ];
        }
        $store = Store::findOne($this->store_id);
        $goods_pic_url = $goods->cover_pic;

        $goods_pic_save_path = \Yii::$app->basePath . '/web/temp/';
        if (!file_exists($goods_pic_save_path)) {
            mkdir($goods_pic_save_path);
        }
        $version = hj_core_version();
        $goods_pic_save_name = md5("v={$version}&goods_id={$goods->id}&goods_name={$goods->name}&store_name={$store->name}&user_id={$this->user_id}&goods_pic_url={$goods_pic_url}") . '.jpg';

        $pic_url = str_replace('http://', 'https://', \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/temp/' . $goods_pic_save_name);
        if (file_exists($goods_pic_save_path . $goods_pic_save_name)) {
            return [
                'code' => 0,
                'data' => [
                    'goods_name' => $goods->name,
                    'pic_url' => $pic_url . '?v=' . time(),
                ],
            ];
        }

        $goods_pic_path = $this->saveTempImage($goods_pic_url);
        if (!$goods_pic_path) {
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：商品图片丢失',
            ];
        }

        $goods_qrcode_dst = \Yii::$app->basePath . '/web/statics/images/goods-qrcode-dst-1.png';
        $font_path = \Yii::$app->basePath . '/web/statics/font/st-heiti-light.ttc';

        $editor = Grafika::createEditor(GrafikaHelper::getSupportEditorLib());
        $editor->open($goods_qrcode, $goods_qrcode_dst);
        $editor->open($goods_pic, $goods_pic_path);

        //获取小程序码图片
        $scene = "gid:{$goods->id},uid:{$this->user_id}";
        $page = "pages/book/details/details";
        $wxapp_qrcode_file_res = $this->getQrcode($scene, 240, $page);
        if ($wxapp_qrcode_file_res['code'] == 1) {
            unlink($goods_pic_path);
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：获取小程序码失败，' . $wxapp_qrcode_file_res['msg'],
            ];
        }
        $wxapp_qrcode_file_path = $wxapp_qrcode_file_res['file_path'];
        $editor->open($wxapp_qrcode, $wxapp_qrcode_file_path);
        //裁剪商品图片
        //$editor->crop($goods_pic, 670, 670, 'smart');
        $editor->resizeFill($goods_pic, 690, 690);
        //附加商品图片
        $editor->blend($goods_qrcode, $goods_pic, 'normal', 1.0, 'top-left', 30, 126);

        if ($this->user) {
            $user = $this->user;
            // 用户头像
            $user_pic_path = $this->saveTempImage($user->avatar_url);
            if (!$user_pic_path) {
                return [
                    'code' => 1,
                    'msg' => '获取商品海报失败：用户头像丢失',
                ];
            }

            list($w, $h) = getimagesize($user_pic_path);
            $user_pic_path = $this->test($user_pic_path, $goods_pic_save_path, $w, $h);
            $editor->open($user_pic, $user_pic_path);
            //调整用户头像图片
            $editor->resizeExactWidth($user_pic, 68);
            //附加用户头像图片
            $editor->blend($goods_qrcode, $user_pic, 'normal', 1.0, 'top-left', 30, 30);

            // 用户名处理
            $username = $this->setName($user->nickname);
            $editor->text($goods_qrcode, $username, 15, 128, 56, new Color('#5b85cf'), $font_path, 0);
            $namewitch = imagettfbbox(15, 0, $font_path, $username);
//        var_dump($namewitch[2]);die();
            $editor->text($goods_qrcode, '分享给你一个商品', 15, (132 + $namewitch[2]), 56, new Color('#353535'), $font_path, 0);
            unlink($user_pic_path);
        } else {
            $editor->text($goods_qrcode, '分享给你一个商品', 15, 30, 56, new Color('#353535'), $font_path, 0);
        }

        $name_size = 15;
        $name_width = 670;
        //商品名称处理换行
        $name = $this->autowrap($name_size, 0, $font_path, $goods->name, $name_width, 2);
        //加商品名称
        $editor->text($goods_qrcode, $name, $name_size, 30, 844, new Color('#353535'), $font_path, 0);

        // 商品价格钱币符
        $editor->text($goods_qrcode, '￥', 15, 30, 962, new Color('#ff5c5c'), $font_path, 0);
        //加商品价格
        $editor->text($goods_qrcode, $goods->price, 29, 48, 950, new Color('#ff5c5c'), $font_path, 0);

        //加商城名称
//        $editor->text($goods_qrcode, $store->name, 20, 40, 1170, new Color('#888888'), $font_path, 0);

        //调整小程序码图片
        $editor->resizeExactWidth($wxapp_qrcode, 160);
        //附加小程序码图片
        $editor->blend($goods_qrcode, $wxapp_qrcode, 'normal', 1.0, 'top-left', 536, 948);
//        $editor->blend($goods_qrcode, $wxapp_qrcode, 'normal', 1.0, 'top-left', 470, 1040);

        //保存图片
        $editor->save($goods_qrcode, $goods_pic_save_path . $goods_pic_save_name, 'jpeg', 85);

        //删除临时图片
        unlink($goods_pic_path);
        unlink($wxapp_qrcode_file_path);

        return [
            'code' => 0,
            'data' => [
                'goods_name' => $goods->name,
                'pic_url' => $pic_url . '?v=' . time(),
            ],
        ];
    }

    //分销海报
    public function share_qrcode()
    {
        $share=Share::findOne(['user_id'=>$this->user->id,'status'=>1,'is_delete'=>0]);
        $general_id=0;
        if($share->general_id==1) $general_id=$share->user_id;
        if ($this->user->general_id !=0)$general_id=$this->user->general_id;
        file_put_contents('/tmp/test.log',  "111".PHP_EOL,8);
        $save_root = \Yii::$app->basePath . '/web/temp/';
        if (!is_dir($save_root)) {
            mkdir($save_root);
            file_put_contents($save_root . '.gitignore', "*\r\n!.gitignore");
        }
        $version = hj_core_version();
        $save_name = md5("v={$version}&store_id={$this->store_id}&user_id={$this->user->id}&general_id={$general_id}") . '.jpg';
        $pic_url = str_replace('http://', 'https://', \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/temp/' . $save_name);


        if (file_exists($save_root . $save_name)) {
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => $pic_url . '?v=' . time()
            ];
        }

        $store_qrcode = Qrcode::findOne(['store_id' => $this->store_id, 'is_delete' => 0]);
        if (!$store_qrcode) {
            return [
                'code' => 1,
                'msg' => '请先在后台设置分销海报'
            ];
        }

        //昵称位置
        $font_position = json_decode($store_qrcode->font_position, true);
        //小程序码位置
        $qrcode_position = json_decode($store_qrcode->qrcode_position, true);
        //头像位置
        $avatar_position = json_decode($store_qrcode->avatar_position, true);
        //头像大小
        $avatar_size = json_decode($store_qrcode->avatar_size, true);
        //小程序码大小
        $qrcode_size = json_decode($store_qrcode->qrcode_size, true);
        //昵称大小
        $font_size = json_decode($store_qrcode->font, true);
        //背景图下载到临时目录
        $qrcode_bg = self::saveTempImage($store_qrcode->qrcode_bg);
        if (!$qrcode_bg) {
            return [
                'code' => 1,
                'msg' => '获取背景图片失败'
            ];
        }
        //用户头像下载到临时目录
        $user_avatar = self::saveTempImage($this->user->avatar_url);
        if (!$user_avatar) {
            return [
                'code' => 1,
                'msg' => '获取用户头像失败'
            ];
        }
        //背景图宽高
        list($qrcode_bg_w, $qrcode_bg_h) = getimagesize($qrcode_bg);
        if ($qrcode_bg_w == 0) {
            return [
                'code' => 1,
                'msg' => '获取背景图片失败'
            ];
        }
        //文字字体
        $font_path = \Yii::$app->basePath . '/web/statics/font/st-heiti-light.ttc';

        //比例尺
        $percent = 750 / 300;

        //获取小程序码图片
        $width = doubleval($qrcode_size['w'] * $percent);
        $scene = "{$this->user_id}";
        $wxapp_qrcode_file_res = $this->getQrcode($scene);
        file_put_contents('/tmp/test.log',  json_encode($wxapp_qrcode_file_res).PHP_EOL,8);
        if ($wxapp_qrcode_file_res['code'] == 1) {
            unlink($qrcode_bg);
            return [
                'code' => 1,
                'msg' => '获取商品海报失败：获取小程序码失败，' . $wxapp_qrcode_file_res['msg'],
            ];
        }
        $wxapp_qrcode_file_path = $wxapp_qrcode_file_res['file_path'];

        $editor = Grafika::createEditor(['Gd']);
        //获取背景图
        $editor->open($qrcode_bg_dst, $qrcode_bg);
        $editor->resizeExact($qrcode_bg_dst, 750, 1200);
        //获取小程序码
        if (isset($qrcode_size['c']) && $qrcode_size['c'] == 'true') {
            list($w,$h) = getimagesize($wxapp_qrcode_file_path);

            $wxapp_qrcode_file_path = $this->test($wxapp_qrcode_file_path, $save_root, $w, $w);
        }
        $editor->open($wxapp_qrcode_dst, $wxapp_qrcode_file_path);
        $editor->resizeExact($wxapp_qrcode_dst, $width, $width);
        //将小程序码添加到背景图
        $qrcode_x = $qrcode_position['x'] * $percent;
        $qrcode_y = $qrcode_position['y'] * $percent;
        $editor->blend($qrcode_bg_dst, $wxapp_qrcode_dst, 'normal', 1.0, 'top-left', $qrcode_x, $qrcode_y);
        if ($avatar_size['w'] > 0) {
            //获取头像
            $avatar_w = $avatar_size['w'] * $percent;
            $avatar_h = $avatar_size['h'] * $percent;
            $avatar_x = $avatar_position['x'] * $percent;
            $avatar_y = $avatar_position['y'] * $percent;
            if ($avatar_x < $qrcode_bg_w || $avatar_y < $qrcode_bg_h) {
                list($w, $h) = getimagesize($user_avatar);
                $user_avatar = $this->test($user_avatar, $save_root, $w, $h);
                $editor->open($avatar_dst, $user_avatar);
                //裁剪头像
                $editor->resizeExact($avatar_dst, $avatar_w, $avatar_h);
                //将头像添加到背景图
                $editor->blend($qrcode_bg_dst, $avatar_dst, 'normal', 1.0, 'top-left', $avatar_x, $avatar_y);
            }
        }
        if ($font_size['size'] > 0) {
            $color = \app\models\Color::find()->andWhere(['id' => (int)$font_size['color']])->asArray()->one();
            //附加用户昵称
            $font = $font_size['size'] * $percent * 0.74;
            $font_x = $font_position['x'] * $percent;
            $font_y = $font_position['y'] * $percent + 1;
            if ($font_x < $qrcode_bg_w || $font_y < $qrcode_bg_h) {
                $editor->text($qrcode_bg_dst, $this->user->nickname, $font, $font_x, $font_y, new Color($color['color']), $font_path, 0);
            }
        }

        //保存图片
        $editor->save($qrcode_bg_dst, $save_root . $save_name, 'jpeg', 85);

        //删除临时图片
        unlink($qrcode_bg);
        unlink($user_avatar);
        unlink($wxapp_qrcode_file_path);
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $pic_url . '?v=' . time()
        ];
    }

    private function getQrcode($scene, $width = 430, $page = null)
    {
        $wechat = $this->getWechat();
        $access_token = $wechat->getAccessToken();
        if (!$access_token) {
            return [
                'code' => 1,
                'msg' => $wechat->errMsg,
            ];
        }
        $api = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token={$access_token}";
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $data = [
            'scene' => $scene,
            'width' => $width,
        ];
        if ($page) {
            $data['page'] = $page;
        }
        $data = json_encode($data);
        \Yii::trace("GET WXAPP QRCODE:" . $data);
        $curl->post($api, $data);
        if (in_array('Content-Type: image/jpeg', $curl->response_headers)) {
            //返回图片
            return [
                'code' => 0,
                'file_path' => $this->saveTempImageByContent($curl->response),
            ];
        } else {
            //返回文字
            $res = json_decode($curl->response, true);
            return [
                'code' => 1,
                'msg' => $res['errmsg'],
            ];
        }
    }

    //获取网络图片到临时目录
    private function saveTempImage($url)
    {
        $wdcp_patch = false;
        $wdcp_patch_file = \Yii::$app->basePath . '/patch/wdcp.json';
        if (file_exists($wdcp_patch_file)) {
            $wdcp_patch = json_decode(file_get_contents($wdcp_patch_file), true);
            if ($wdcp_patch && in_array(\Yii::$app->request->hostName, $wdcp_patch)) {
                $wdcp_patch = true;
            } else {
                $wdcp_patch = false;
            }
        }
        if ($wdcp_patch) {
            $url = str_replace('http://', 'https://', $url);
        }

        if (!is_dir(\Yii::$app->runtimePath . '/image')) {
            mkdir(\Yii::$app->runtimePath . '/image');
        }
        $save_path = \Yii::$app->runtimePath . '/image/' . md5($url) . '.jpg';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $img = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($save_path, 'w');
        fwrite($fp, $img);
        fclose($fp);
        return $save_path;
    }

    //保存图片内容到临时文件
    private function saveTempImageByContent($content)
    {
        $save_path = \Yii::$app->runtimePath . '/image/' . md5(base64_encode($content)) . '.jpg';
        $fp = fopen($save_path, 'w');
        fwrite($fp, $content);
        fclose($fp);
        return $save_path;
    }

    //生成圆角图片
    public function test($url, $path = './', $w, $h, $is_true = 'true')
    {
//        $w = 110; $h=110; // original size
        $original_path = $url;
        $dest_path = $path . uniqid('r', true) . '.png';
        $src = imagecreatefromstring(file_get_contents($original_path));
        if ($is_true == 'true') {
            $newpic = imagecreatetruecolor($w, $h);
            imagealphablending($newpic, false);
            $transparent = imagecolorallocatealpha($newpic, 0, 0, 0, 127);
            $r = $w / 2;
            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    $c = imagecolorat($src, $x, $y);
                    $_x = $x - $w / 2;
                    $_y = $y - $h / 2;
                    if ((($_x * $_x) + ($_y * $_y)) < ($r * $r)) {
                        imagesetpixel($newpic, $x, $y, $c);
                    } else {
                        imagesetpixel($newpic, $x, $y, $transparent);
                    }
                }
            }
            imagesavealpha($newpic, true);
            // header('Content-Type: image/png');
            imagepng($newpic, $dest_path);
            imagedestroy($newpic);
            imagedestroy($src);
            unlink($url);
        } else {
            imagesavealpha($src, true);
            // header('Content-Type: image/png');
            imagepng($src, $dest_path);
            unlink($url);
        }
        return $dest_path;
    }


    /**
     * @param integer $fontsize 字体大小
     * @param integer $angle 角度
     * @param string $fontface 字体名称
     * @param string $string 字符串
     * @param integer $width 预设宽度
     */
    private function autowrap($fontsize, $angle, $fontface, $string, $width, $max_line = null)
    {
        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $content = "";
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        $letter = [];
        for ($i = 0; $i < mb_strlen($string, 'UTF-8'); $i++) {
            $letter[] = mb_substr($string, $i, 1, 'UTF-8');
        }
        $line_count = 0;
        foreach ($letter as $l) {
            $teststr = $content . " " . $l;
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $line_count++;
                if ($max_line && $line_count >= $max_line) {
                    $content = mb_substr($content, 0, -1, 'UTF-8') . "...";
                    break;
                }
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }

    /**
     * @param integer $fontsize 字体大小
     * @param integer $angle 角度
     * @param string $fontface 字体名称
     * @param string $string 字符串
     * @param integer $width 预设宽度
     */
    public function setName($text)
    {
        if (strlen($text) > 10) {
            $text = substr_replace($text, '...', 10);
        }
        return $text;
    }
}
