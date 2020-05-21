<?php
/**
 * Created by PhpStorm.
 * User: 风哀伤
 * Date: 2018/6/28
 * Time: 15:20
 */

namespace app\modules\api\models;


use app\hejiang\ApiResponse;
use app\models\Goods;
use app\models\MiaoshaGoods;
use app\models\MsGoods;
use app\models\PtGoods;
use app\models\YyGoods;
use app\models\GoodsVisitLog;
use yii\data\Pagination;
use yii\db\Expression;
use yii\db\Query;

class SearchForm extends ApiModel
{
    public $store_id;
    public $goods_id;
    public $limit;
    public $page;
    public $user_id;
    public $keyword;
    public $mch_id;
    public $type;
    public $is_sale;
    public $cat_id;
    public function rules()
    {
        return [
            [['limit', 'page'], 'integer'],
            [['limit'], 'default', 'value' => 20],
            [['keyword'], 'trim'],
            [['keyword'], 'string']
        ];
    }

    public function search()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $select = ['id', 'name', 'sort', 'addtime', 'price', 'cover_pic pic_url', 'store_id', 'status', 'is_delete'];
        $query_m = (new Query())->from([Goods::tableName()])
            ->select($select)->addSelect(new Expression("'m' goods_type"));
        $query_pt = (new Query())->from([PtGoods::tableName()])
            ->select($select)->addSelect(new Expression("'pt' goods_type"));
        $query_yy = (new Query())->from([YyGoods::tableName()])
            ->select($select)->addSelect(new Expression("'yy' goods_type"));
        $query_ms = (new Query())->from(['ms' => MsGoods::tableName()])->innerJoin(['mg' => MiaoshaGoods::tableName()], 'mg.goods_id=ms.id')
            ->where([
                'mg.open_date' => date('Y-m-d'),
                'mg.start_time' => date('H'),
                'mg.is_delete' => 0
            ])->select(['mg.id', 'ms.name', 'ms.sort', 'ms.addtime', 'ms.original_price price', 'ms.cover_pic pic_url', 'ms.store_id', 'ms.status', 'ms.is_delete'])
            ->addSelect(new Expression("'ms' goods_type"));
        $query_table = $query_m->union($query_pt, true)->union($query_yy, true)->union($query_ms, true);
        $query = (new Query())->from(['g' => $query_table])->andWhere(['g.status' => 1, 'g.is_delete' => 0]);
        if ($this->store_id)
            $query->andWhere(['g.store_id' => $this->store_id]);

        if ($this->keyword)
            $query->andWhere(['LIKE', 'g.name', $this->keyword]);
        $count = $query->count(1);

        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $this->limit, 'page' => $this->page - 1]);
        //综合，自定义排序+时间最新
        $query->orderBy('g.sort ASC, g.addtime DESC');

        $list = $query
            ->limit($pagination->limit)
            ->offset($pagination->offset)
            ->groupBy('g.goods_type,g.id')->all();

        foreach ($list as $i => $item) {
            switch ($item['goods_type']) {
                case 'm':
                    $list[$i]['url'] = "/pages/goods/goods?id=" . $item['id'];
                    break;
                case 'pt':
                    $list[$i]['url'] = "/pages/pt/details/details?gid=" . $item['id'];
                    break;
                case 'yy':
                    $list[$i]['url'] = "/pages/book/details/details?id=" . $item['id'];
                    break;
                case 'ms':
                    $list[$i]['url'] = "/pages/miaosha/details/details?id=" . $item['id'];
                    break;
                default:
                    $list[$i]['url'] = "/pages/goods/goods?id=" . $item['id'];
                    break;
            }
        }
        $data = [
            'row_count' => $count,
            'page_count' => $pagination->pageCount,
            'list' => $list,
        ];
        return new ApiResponse(0, 'success', $data);

    }
    public function getMchGoods()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
//        var_dump($this->mch_id);exit;
        $select = ['id', 'name', 'sort', 'addtime', 'price', 'cover_pic pic_url', 'store_id', 'status', 'is_delete','cover_pic'];
        $query = Goods::find()->select($select)->andWhere(['status' => 1, 'is_delete' => 0,'mch_id'=>$this->mch_id]);
        if ($this->store_id)
            $query->andWhere(['store_id' => $this->store_id]);
        if($this->cat_id && $this->cat_id!="undefined")
            $query->andWhere(['cat_id' => $this->cat_id]);
        if ($this->keyword)
            $query->andWhere(['LIKE', 'name', $this->keyword]);
        $count = $query->count(1);

        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $this->limit, 'page' => $this->page - 1]);
        //综合，自定义排序+时间最新
        switch ((int)$this->type) {

            case 1:
                $query->orderBy('price DESC, addtime DESC');
                break;
            case 2:
                $query->orderBy('price ASC, addtime DESC');
                break;
            default:
                $query->orderBy('sort ASC, addtime DESC');
                break;
        }
        $list = $query
            ->limit($pagination->limit)
            ->offset($pagination->offset)
            ->all();
        $goods_list=[];
        $sort=[];
        foreach ($list as $i => $item) {
                $goods_list[$i]=$item->toArray();
                $goods_list[$i]['sale']=$item->getSalesVolume();
                $goods_list[$i]['url'] = "/pages/goods/goods?id=" . $item->id;
                $sort[$i]=$goods_list[$i]['sale'];
            }
//        var_dump($goods_list);exit;
        if((int)$this->is_sale==1){
            array_multisort($sort,SORT_DESC,$goods_list,SORT_DESC);
        }elseif ((int)$this->is_sale==2){
            array_multisort($sort,SORT_ASC,$goods_list,SORT_ASC);

        }

        $data = [
            'row_count' => $count,
            'page_count' => $pagination->pageCount,
            'list' => $goods_list,
        ];
        return new ApiResponse(0, 'success', $data);

    }
    //添加商品收藏
    public function paygoods(){
       if(is_null($this->goods_id))return new ApiResponse(0, 'errot', ['code'=>1,'msg'=>"缺少商品"]);
       $a=GoodsVisitLog::find()->alias('a')
         ->leftJoin(['b' => Goods::tableName()], "a.goods_id = b. id")
         ->where("a.user_id = {$this->user_id} and a.goods_id = {$this->goods_id}")
         ->all();
         if(empty($a)){
            $add= \Yii::$app->db->createCommand('INSERT INTO `cshopmall_goods_visit_log` (`user_id`,`goods_id`,`addtime`,`visit_date`) VALUES (:user_id,:goods_id,:addtime,:visit_date)', [
                ':user_id' =>$this->user_id,
                ':goods_id'=>$this->goods_id,
                ':addtime'=>time(),
                ':visit_date'=>date('Y-m-d',time()),

            ]  )->execute(); 
            if($add>0)  return new ApiResponse(0, 'success', ['code'=>0,'msg'=>"已收藏"]);
         }else{
            return new ApiResponse(0, 'success', ['code'=>0,'msg'=>"已收藏"]);
         }

    }
    //浏览记录商品列表
    public function goodslist(){

         $query=GoodsVisitLog::find()->alias('a')
         ->leftJoin(['b' => Goods::tableName()], "a.goods_id = b. id")
         ->where("a.user_id = {$this->user_id} ")
         ->select("b.*,a.*")
         ->asArray();
         $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $this->limit, 'page' => $this->page - 1]);
         $count = $query->count();
         $list=$query->all();
         $data = [
            'row_count' => $count,
            'page_count' => $pagination->pageCount,
            'list' => $list,
         ];
        return new ApiResponse(0, 'success', $data);

    }



}
