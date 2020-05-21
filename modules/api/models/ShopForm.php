<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/24
 * Time: 14:44
 */

namespace app\modules\api\models;

use app\hejiang\ApiResponse;
use app\models\Shop;
use app\models\Mch;
use app\models\Video;
use app\models\VideoClassify;
use app\models\Cat;
use app\models\ShopPic;
use yii\data\Pagination;

class ShopForm extends ApiModel
{
    public $store_id;
    public $user;
    public $shop_id;
    public $mch_id;
    public $limit;
    public $page;
    public $cat_id;


    public function rules()
    {
        return [
            [['shop_id'], 'integer']
        ];
    }

    public function search()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $shop = Shop::find()->where([
            'store_id' => $this->store_id, 'id' => $this->shop_id, 'is_delete' => 0
        ])->asArray()->one();
        $cat=Cat::find()->where(['is_delete'=>0,'is_show'=>1,'parent_id'=>0])->all();

        $cat_list=array();
        foreach ($cat as $k=>$v){
                $cat_list[$k]=$v->toArray();
                $cat_list[$k]['child']=Cat::find()->where(['parent_id'=>$v->id])->asArray()->all();
        }
        $shop['cat']=$cat_list;
        if (!$shop) {
            return new ApiResponse(1, '店铺不存在');
        }
        $shop_pic = ShopPic::find()->select(['pic_url'])->where(['store_id' => $this->store_id, 'shop_id' => $shop['id'], 'is_delete' => 0])->column();
        $shop['pic_list'] = $shop_pic;
        if (!$shop_pic) {
            $shop['pic_list'] = [$shop['pic_url']];
        }

        foreach ($shop as $index => &$value) {
            if (!$value) {
                if (in_array($index, ['pic_url', 'cover_url', 'pic_list'])) {
                    continue;
                }
                $shop[$index] = "暂无设置";
            }
            if ($index == 'content') {
                $value = str_replace("&amp;nbsp;", " ", $value);
                $value = str_replace("&nbsp;", " ", $value);
            }
        }
        return new ApiResponse(0, 'success', ['shop'=>$shop]);
    }
    public function getvideo(){
        if (!$this->validate()) {
            return $this->errorResponse;
        }
//        $video=['https://tt.tryine.com/web/uploads/video/7c/7cd988dcbe02a712388c65bca72b9cd8538d21c0.mp4','https://tt.tryine.com/web/uploads/video/7c/7cd988dcbe02a712388c65bca72b9cd8538d21c0.mp4','https://tt.tryine.com/web/uploads/video/7c/7cd988dcbe02a712388c65bca72b9cd8538d21c0.mp4'];
//        $mch=Mch::findOne(['id'=>$this->mch_id]);
        $query=Video::find()
            ->where(['mch_id'=>$this->mch_id]);
        if($this->cat_id){
            $query->andWhere(['classify_id'=>$this->cat_id]);
        }
        $video=$query->asArray()->all();
        if(!$video){
            return new ApiResponse(1, 'error', ['msg'=>"该商户暂无视频"]);
        }
//        var_dump($video);exit;
        $count=count($video);
//        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $this->limit, 'page' => $this->page - 1]);
        $start=($this->page-1)*1;
        $list=array_slice($video,$start,'1');
        foreach ($list as $k=>$v){
            $list[$k]['addtime']=date("Y-m-d",$v['addtime']);
        }
        $data = [
            'row_count' => $count,
            'page_count' => 1,
            'list' => $list,
        ];
        return new ApiResponse(0, 'success', $data);
    }
}
