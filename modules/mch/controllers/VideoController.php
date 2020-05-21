<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/8/18
 * Time: 14:17
 */

namespace app\modules\mch\controllers;
use yii\web\UploadedFile;
use app\models\Mch;

use app\models\User;
use app\models\Video;
use app\models\VideoClassify;
use app\modules\mch\models\Model;
use yii\data\Pagination;
class VideoController extends Controller
{
    //视频列表
   public function actionIndex($cat_id = 1,$page=1)
   {


       $query = Video::find()->alias('a')->where(['a.is_delete'=>1]);
       $count = $query->count();

       $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);
       $list = $query
           ->leftJoin(['b' => VideoClassify::tableName()], 'a.classify_id = b.id')
           ->select('a.*,b.name,b.id as class_id')
           ->orderBy('a.addtime DESC')
           ->limit($pagination->limit)->offset($pagination->offset)
           ->asArray()
           ->all();
       foreach ($list as $k =>$v){
           if($v['mch_id']==0){
               $v['mch_id']="平台";
           } else{
               $mch=Mch::findOne(['id'=>$v['mch_id']]);
               $user=User::findOne(['id'=>$mch->user_id]);
               $v['mch_id']=$mch->name;
           }
           $list[$k]=$v;
       }



       return $this->render('index', ['list'=>$list,'cat_id' => $cat_id,'row_count'=>$count,'pagination'=>$pagination]);
   }

   /*
    * 视频分类列表
    * */
   public function actionCases($cat_id = 1,$page=1)
   {
       $query = VideoClassify::find()->where(['is_delete'=>1]);
       $list = $query
           ->select('id,pid,name,mch_id,is_delete')
           ->asArray()
           ->all();
       foreach ($list as $k =>$v){
           if($v['mch_id']==0){
               $v['mch_id']="平台";
           } else{
               $mch=Mch::findOne(['id'=>$v['mch_id']]);
               $user=User::findOne(['id'=>$mch->user_id]);
               $v['mch_id']=$mch->name;
           }
           $list[$k]=$v;
       }
       $count = $query->count();

       $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);
       return $this->render('cases' ,['list'=>$list,'cat_id' => $cat_id,'row_count'=>$count,'pagination'=>$pagination]);
   }

   /*
    * 删除视频分类
    * */
   public function actionDel2($id)
   {
       $model = VideoClassify::findOne([
           'id' => $id,
       ]);
       if ($model) {
           if($model['is_delete'] = 1){
               $update=\Yii::$app->db->createCommand()->update('cshopmall_video_classify', ['is_delete' => 2], "id = {$id}")->execute();
               if($update>0){
                   return [
                       'code' => 0,
                       'msg' => '删除成功',
                   ];
               }
           }

       }
   }

   //视频修改/编辑
   public function actionEdit($cat_id,$id=null){

       $classify_id=VideoClassify::find()->all();
       $classify=VideoClassify::findOne(['id'=>$id]);

       $mch1 = Mch::find()->where(['review_status'=>1,'is_delete'=>0])->all();
       $query = Video::find()->alias('a')->where(['a.id'=>$id]);
           $model = $query
           ->leftJoin(['b' => VideoClassify::tableName()], 'a.classify_id = b.id')
           ->select('a.*,b.name,b.id as class_id')
           ->asArray()
           ->all();
       foreach ($model as $k =>$v){
           if($v['mch_id']==0){
               $v['mch_name']="平台";
           } else{
               $mch=Mch::findOne(['id'=>$v['mch_id']]);
               $user=User::findOne(['id'=>$mch->user_id]);
               $v['mch_name']=$mch->name;
           }
           $model[$k]=$v;
       }


       if (\Yii::$app->request->isPost){
           $title=\Yii::$app->request->post('title');
           $content=\Yii::$app->request->post('content');
           $url=\Yii::$app->request->post('url');
           $sort=\Yii::$app->request->post('sort');
           $name=\Yii::$app->request->post('name');
           $classify_id=\Yii::$app->request->post('classify_id');
           $image=\Yii::$app->request->post('image');
           $mch_id=\Yii::$app->request->post('mch_id');

           if($cat_id == 2){
               $add=\Yii::$app->db->createCommand()->update('cshopmall_video',['title' => $title,'content'=>$content,'url'=>$url,'sort'=>$sort,'classify_id'=>$classify_id,'image'=>$image,'update'=>time(),'mch_id'=>$mch_id], "id = {$id}")->execute();

           }elseif($cat_id ==1){

               $add= \Yii::$app->db->createCommand('INSERT INTO `cshopmall_video` (`title`,`content`,`url`,`addtime`,`sort`,`is_delete`,`classify_id`,`image`,`mch_id`) VALUES (:title,:content,:url,:addtime,:sort,:is_delete,:classify_id,:image,:mch_id)', [
                   ':title' =>$title,
                   ':content'=>$content,
                   ':url'=>$url,
                   ':addtime'=>time(),
                   ':sort'=>$sort,
                   ':is_delete'=>1,
                   ':classify_id'=>$classify_id,
                   ':image'=>$image,
                   ':mch_id'=>$mch_id
               ]  )->execute();

           } elseif($cat_id ==3) {
               $add= \Yii::$app->db->createCommand('INSERT INTO `cshopmall_video_classify` (`pid`,`name`,`sort`) VALUES (:pid,:name,:sort)', [
                   ':pid' =>0,
                   ':name'=>$name,
                   ':sort'=>$sort,

               ]  )->execute();

           } elseif($cat_id ==4){
               $add=\Yii::$app->db->createCommand()->update('cshopmall_video_classify',['name'=>$name,'sort'=>$sort,'update'=>time()], "id = {$id}")->execute();


           }
       
           if($add>0){
               return [
                   'code' => 0,
                   'msg' => '保存成功',
                   'data'=>['cat_id'=>$cat_id]
               ];
           }
       }

       return $this->render('edit', ['classify'=>$classify,'mch'=>$mch1,'model'=>$model,'classify_id'=>$classify_id]);

}


//删除视频

   public function actionDel($id,$sj=null){


       $model = Video::findOne([
           'id' => $id,
       ]);
       if ($model) {
           if($sj == 1 ){
               $update=\Yii::$app->db->createCommand()->update('cshopmall_video', ['is_show' => 1], "id = {$id}")->execute();
               if($update>0){
                   return [
                       'code' => 0,
                       'msg' => '已上架',
                   ];
               }

           }elseif ($sj == 2){
               $update=\Yii::$app->db->createCommand()->update('cshopmall_video', ['is_show' => 2], "id = {$id}")->execute();
               if($update>0){
                   return [
                       'code' => 0,
                       'msg' => '已下架',
                   ];
               }
           }
           if($model['is_delete'] = 1){
               $update=\Yii::$app->db->createCommand()->update('cshopmall_video', ['is_delete' => 2], "id = {$id}")->execute();
               if($update>0){
                   return [
                       'code' => 0,
                       'msg' => '删除成功',
                   ];
               }
           }

       }
   }

}