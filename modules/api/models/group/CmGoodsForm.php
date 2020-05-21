<?php
/**
 * Created by PhpStorm.
 * User: peize
 * Date: 2017/11/27
 * Time: 9:32
 */

namespace app\modules\api\models\group;

use app\models\Article;
use app\models\CmGoods;
use app\models\CmGoodsPic;
use app\models\CmOrder;
use app\models\CmOrderComment;
use app\models\CmOrderDetail;
use app\models\User;
use app\modules\api\models\ApiModel;
use yii\data\Pagination;

class CmGoodsForm extends ApiModel
{
    public $page = 0;
    public $store_id;

    public $user_id;

    public $gid;

    public $limit;


    /**
     * @return array
     * 拼团商品列表
     */
    public function getList()
    {
        $page = \Yii::$app->request->get('page')?:1;
        $limit = (int)\Yii::$app->request->get('limit')?:10;
        $cid = \Yii::$app->request->get('cid');
        $type= \Yii::$app->request->get('type')?:1;
        $sort=\Yii::$app->request->get('sort')?:1;
        $group_num=\Yii::$app->request->get('group_num');
        $query = CmGoods::find()
              ->andWhere(['is_delete' => 0, 'store_id' => $this->store_id, 'status' => 1,'activity_type'=>$type])
              ->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]]);
        if ((int)$cid) {
            // 分类
            $query->andWhere(['cat_id'=>$cid]);
        }
//        else {
//            // 热销
//            $query->andWhere(['is_hot'=>1]);
//        }
        if($sort==2){
            $order=" sort desc";
        }else{
            $order=" sort asc";
        }
        if(isset($group_num) && $group_num==1){
            $order=" group_num asc";
        }elseif (isset($group_num) && $group_num==2){
            $order=" group_num desc";
        }
        $count = $query->count();
        $p = new Pagination(['totalCount' => $count, 'pageSize' => $limit, 'page' => $page - 1]);
        $list = $query
            ->offset($p->offset)
            ->limit($p->limit)
            ->orderBy($order)
            ->asArray()
            ->all();


        return [
            'row_count'     => intval($count),
            'page_count'    => intval($p->pageCount),
            'page'          => intval($page),
            'list'          => $list,
        ];
    }
    //首页获取热销CmGoods
    public function getCmList(){
        $query = CmGoods::find()
            ->andWhere(['is_delete' => 0, 'store_id' => $this->store_id, 'status' => 1,'is_hot'=>1])
            ->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]]);
        $list = $query
            ->orderBy('sort ASC')
            ->asArray()
            ->select('id,cover_pic as pic,name,price,group_num as sale_num,date_start,date_end,activity_type,live_link')
        ->all();
        $goods=array();
        foreach ($list as $k=>$v){
            if($v['activity_type']=="1"){
                $goods['zhuanchang'][]=$v;
            }elseif($v['activity_type']=="2"){
                $goods['shequtuan'][]=$v;
            }elseif($v['activity_type']=="3"){
                $goods['qianggc'][]=$v;
            }
        }

//        echo "<pre>";
//        var_dump($goods);
//        exit;
        return $goods;
    }

    /**
     * @return array
     * 关键字搜索
     */
    public function search()
    {
        $page = \Yii::$app->request->get('page')?:1;
        $limit = (int)\Yii::$app->request->get('limit')?:4;
        $keyword = \Yii::$app->request->get('keyword');

        if (empty($keyword)) {
            return;
        }


        $query = CmGoods::find()
            ->andWhere(['is_delete' => 0, 'store_id' => $this->store_id, 'status' => 1])
            ->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]]);

        $query->andWhere(['like','name',$keyword]);

        $count = $query->count();
        $p = new Pagination(['totalCount' => $count, 'pageSize' => $limit, 'page' => $page - 1]);
        $list = $query
            ->select('id,name,original_price,price,cover_pic,unit,cat_id,virtual_sales,group_num,')
            ->offset($p->offset)
            ->limit($p->limit)
            ->orderBy('sort ASC')
            ->asArray()
            ->all();

        foreach ($list as $key => $goods) {
            $list[$key]['groupList'] = CmOrderDetail::find()
                ->select([
                    'u.avatar_url'
                ])
                ->alias('od')
                ->andWhere(['od.goods_id'=>$goods['id']])
                ->leftJoin(['o'=>CmOrder::tableName()], 'o.id=od.order_id')
                ->andWhere(['o.is_delete'=>0,'o.is_pay' => 1,'o.is_group' => 1,'o.parent_id' => 0,'o.is_success' => 0])
                ->andWhere(['>','o.limit_time',time()])
                ->leftJoin(['u'=>User::tableName()], 'o.user_id=u.id')
                ->andWhere(['!=','o.user_id',$this->user_id])
                ->orderBy(['o.addtime' => 'DESC'])
                ->limit(3)
                ->asArray()
                ->all();
            $ptGoodsFind = CmGoods::find()->where(['id'=>$goods['id']])->one();
            $list[$key]['virtual_sales'] += $ptGoodsFind->getSalesVolume();
        }


        return [
            'row_count'     => intval($count),
            'page_count'    => intval($p->pageCount),
            'page'          => intval($page),
            'list'          => $list,
        ];
    }


    /**
     * @return mixed|string
     * 拼团商品详情
     */
    public function getInfo()
    {
        $info = CmGoods::find()
            ->andWhere(['is_delete'=>0,'store_id'=>$this->store_id,'status'=>1,'id'=>$this->gid])
            ->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]])
            ->asArray()
            ->one();
        $goods = CmGoods::find()
            ->andWhere(['or',['>','limit_time',time()],['limit_time'=>0]])
            ->andWhere(['is_delete'=>0,'store_id'=>$this->store_id,'status'=>1,'id'=>$this->gid])->one();
       
        if (!$info) {
            return [
                'code'  => 1,
                'msg'   => '商品不存在或已下架',
            ];
        }
        $info['pic_list'] = CmGoodsPic::find()
            ->select('pic_url')
            ->andWhere(['goods_id'=>$this->gid,'is_delete'=>0])
            ->column();

        $info['attr'] = json_decode($info['attr'], true);
        $info['service'] = explode(',', $info['service']);
        $attr_group_list = $goods->getAttrGroupList();

        $attr_group_num = $goods->getAttrGroupListnum();

        $groupList = CmOrderDetail::find()
            ->select([
                'u.avatar_url','u.nickname',
                'o.limit_time','o.id',
            ])
            ->alias('od')
            ->andWhere(['od.goods_id'=>$goods->id])
            ->leftJoin(['o'=>CmOrder::tableName()], 'o.id=od.order_id')
            ->andWhere(['o.is_delete'=>0,'o.is_group' => 1,'o.parent_id' => 0,'o.is_success'=>0])
            ->andWhere(['or',['o.is_pay'=>1],['o.pay_type'=>2]])
            ->andWhere(['>','o.limit_time',time()])
            ->leftJoin(['u'=>User::tableName()], 'o.user_id=u.id')
            ->andWhere(['!=','o.user_id',$this->user_id])
            ->orderBy(['o.addtime' => 'DESC'])
            ->limit(3)
            ->asArray()
            ->all();
        foreach ($groupList as $key => $val) {
            $limit_time1 = [
                'days'  => '00',
                'hours' => '00',
                'mins'  => '00',
                'secs'  => '00',
            ];

            $order = CmOrder::findOne($val['id']);
            $groupList[$key]['surplus'] = $order->getSurplusGruop();

            $timediff1 = $order->limit_time - time();
            $limit_time1['days'] = intval($timediff1/86400);
            //计算小时数
            $remain1 = $timediff1%86400;
            $limit_time1['hours'] = intval($remain1/3600);
            //计算分钟数
            $remain1 = $remain1%3600;
            $limit_time1['mins'] = intval($remain1/60);
            //计算秒数
            $limit_time1['secs'] = $remain1%60;

            $groupList[$key]['limit_time'] = $limit_time1;
            $groupList[$key]['limit_time_ms'] = explode('-', date('Y-m-d-H-i-s', $order->limit_time));
        }

        $limit_time = [
            'days'  => '00',
            'hours' => '00',
            'mins'  => '00',
            'secs'  => '00',
        ];
        if (!empty($goods->limit_time)) {
            $timediff = $goods->limit_time - time();
            if ($timediff < 0) {
                return [
                    'code'  => 1,
                    'msg'   => '该商品拼团活动已经结束',
                ];
            }
        }

        $limit_time['days'] = intval($timediff/86400);
        //计算小时数
        $remain = $timediff%86400;
        $limit_time['hours'] = intval($remain/3600);
        //计算分钟数
        $remain = $remain%3600;
        $limit_time['mins'] = intval($remain/60);
        //计算秒数
        $limit_time['secs'] = $remain%60;
        $info['limit_time_ms'] = explode('-', date('Y-m-d-H-i-s', $info['limit_time']));
        // 销量
        $info['virtual_sales'] += $goods->getSalesVolume();

        // 获取拼团规则id
        $groupRuleId = Article::find()->andWhere([
            'store_id' => $this->store_id,
            'article_cat_id' => 3,
        ])->scalar();

        $comment = CmOrderComment::find()
            ->alias('c')
            ->select([
                'c.score','c.content','c.pic_list','c.addtime',
                'u.nickname','u.avatar_url',
                'od.attr'
            ])
            ->andWhere(['c.store_id'=>$this->store_id,'c.goods_id'=>$info['id'],'c.is_delete'=>0,'c.is_hide'=>0])
            ->leftJoin(['u'=>User::tableName()], 'u.id = c.user_id')
            ->leftJoin(['od'=>CmOrderDetail::tableName()], 'od.id=c.order_detail_id')
            ->orderBy('c.addtime DESC')
            ->limit(2)
            ->asArray()
            ->all();

        foreach ($comment as $k => $v) {
            $comment[$k]['attr'] = json_decode($v['attr'], true);
            $comment[$k]['pic_list'] = json_decode($v['pic_list'], true);
            $comment[$k]['addtime'] = date('m月d日', $v['addtime']);
            $comment[$k]['nickname'] = $this->substr_cut($v['nickname']);
        }
        if (empty($comment)) {
            $comment = false;
        }

        $goods = CmGoods::findOne([
            'id' => $this->gid,
            'is_delete' => 0,
            'status' => 1,
            'store_id' => $this->store_id,
        ]);
        $info['num'] = $goods->getNum($goods->id);
        return [
            'code'  => 0,
            'msg'   => 'success',
            'data'  => [
                'info' => $info,
                'attr_group_list' => $attr_group_list,
                'attr_group_num' => $attr_group_num,
                'limit_time'      => $limit_time,
                'groupList'      => $groupList,
                'groupRuleId'      => $groupRuleId,
                'comment'      => $comment,
                'commentNum'      => CmOrderComment::getCount($info['id'], $this->store_id),
            ],
        ];
    }

    /**
     * @return array
     * 评论列表
     */
    public function comment()
    {
        $query = CmOrderComment::find()
            ->alias('c')
            ->select([
                'c.score','c.content','c.pic_list','c.addtime',
                'u.nickname','u.avatar_url',
                'od.attr'
            ])
            ->andWhere(['c.store_id'=>$this->store_id,'c.goods_id'=>$this->gid,'c.is_delete'=>0,'c.is_hide'=>0])
            ->leftJoin(['u'=>User::tableName()], 'u.id = c.user_id')
            ->leftJoin(['od'=>CmOrderDetail::tableName()], 'od.id=c.order_detail_id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page, 'pageSize' => 20]);

        $comment = $query
            ->limit($pagination->limit)
            ->offset($pagination->offset)
            ->orderBy('c.addtime DESC')
            ->asArray()
            ->all();
        foreach ($comment as $k => $v) {
            $comment[$k]['attr']        = json_decode($v['attr'], true);
            $comment[$k]['pic_list']    = json_decode($v['pic_list'], true);
            $comment[$k]['addtime']     = date('m月d日', $v['addtime']);
            $comment[$k]['nickname']    = $this->substr_cut($v['nickname']);
        }
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'row_count' => $count,
                'page_count' => $pagination->pageCount,
                'comment' => $comment,
            ],
        ];
    }

    // 将用户名 做隐藏
    private function substr_cut($user_name)
    {
        $strlen     = mb_strlen($user_name, 'utf-8');
        $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }
}
