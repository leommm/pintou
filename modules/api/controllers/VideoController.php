<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23 0023
 * Time: 14:59
 */
namespace app\modules\api\controllers;
use app\modules\api\models\PublishForm;
use app\hejiang\BaseApiResponse;
use app\models\Video;
use app\models\VideoClassify;
use yii\data\Pagination;
class VideoController extends Controller
{

    //视频列表
    public function actionList()
    {

        $mch=\yii::$app->request->get('mch_id');

        if(!is_null($mch)){
            $list=VideoClassify::find()->alias('a')->leftJoin(['b'=>Video::tableName()],'a.id = b.classify_id')
                ->where("a.is_delete = 1 and a.status = 0")
                ->where("b.mch_id = $mch ")
                ->orderBy('sort desc')
                ->asArray()
                ->all();

        }else{
            $list = VideoClassify::find()->where("is_delete = 1 and status = 0")->orderBy('sort desc')->asArray()->all();
        }





        if($list){
            return new BaseApiResponse(['code'=>0,'msg'=>"操作成功",'data'=>$list]);
        }else{
            return new BaseApiResponse(['code'=>0,'msg'=>"暂无分类"]);
        }

    }
    public function actionVideo($page=1,$limit=10){
        $id=\yii::$app->request->get('id');
        $mch_id=\yii::$app->request->get('mch_id');
        if($id==null){
            return new BaseApiResponse(['code'=>1,'msg'=>"id不能为空"]);
        }
        if ($mch_id==null){
            $where=" classify_id = $id and is_delete = 1 and is_show = 1 ";
        }else{
            $where=" classify_id = $id and is_delete = 1 and is_show = 1  and mch_id = $mch_id";
        }

        $query=Video::find()->where($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $page - 1, 'pageSize' => $limit]);

        $list = $query->limit($pagination->limit)->offset($pagination->offset)->orderBy('addtime DESC')->asArray()->all();

        $pagination = new Pagination(['totalCount' => $count, 'page' => $page - 1, 'pageSize' => $limit]);
        foreach ($list as $k =>$v){
            $v['addtime']=date('Y-m-d',$v['addtime']);
            $list[$k]=$v;
        }
        
            return new BaseApiResponse(['code'=>0,'msg'=>"操作成功", 'data' => [
                'row_count' => $count,
                'page_count' => $pagination->pageCount,
                'list' => $list,
            ]]);
        



    }
    //视频详细
    public function actionDetail()
    {
        $id=\yii::$app->request->post('id');
        if($id==null){
            return new BaseApiResponse(['code'=>1,'msg'=>"id不能为空"]);
        }
        $model = Video::findOne([
            'id' => $id,
        ]);
        if ($model){
            return new BaseApiResponse(['code'=>0,'msg'=>"操作成功",'data'=>$model]);
        }

    }
}
