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
use app\models\Publish;
use app\models\User;

class PublishController extends Controller{

 //发布需求
    public function actionIndex(){
        $form=new PublishForm();
        $form->title =\Yii::$app->request->post('title');
        $form->content =\Yii::$app->request->post('content');

        $form->province=\Yii::$app->request->post('province');
        $form->city=\Yii::$app->request->post('city');
        $form->district=\Yii::$app->request->post('district');
        $form->type =\Yii::$app->request->post('type');
        $form->image =\Yii::$app->request->post('image');
        $form->id =\Yii::$app->request->post('id');
      /*  $form->update_type =\Yii::$app->request->get('update_type');*/
        $form->user_id = \Yii::$app->user->id;
        return new BaseApiResponse($form->save());
    }
//发布详情
    public function actionDetail(){
        $id=\yii::$app->request->get('id');
        if($id==null){
            return new BaseApiResponse(['code'=>1,'msg'=>"id不能为空"]);
        }

        $model = Publish::find()->where(['id' => $id])->asArray()->all();
        $use=User::findOne(['id'=>$model['0']['user_id']]);
        $time=(time()-$model['0']['create_time'])/3600;

        $model['time']=round($time,2);
        $model['nickname']=$use->nickname;
        $model['avatar_url']=$use->avatar_url;
       if ($model){
           return new BaseApiResponse(['code'=>0,'msg'=>"操作成功",'data'=>$model]);
       }else{
           return new BaseApiResponse(['code'=>0,'msg'=>"操作失败"]);
       }
    }

    //发布列表
    public  function actionList(){

        $form = new PublishForm();
        $form->attributes = \Yii::$app->request->get();
        $form->user_id = \Yii::$app->user->id;
        $form->type = \Yii::$app->request->post('type');
        $form->status = \Yii::$app->request->post('status');

        return new BaseApiResponse($form->search());

    }


    //删除需求
    public  function actionDel(){

        $form = new PublishForm();

        $form->id =\Yii::$app->request->get('id');


        return new BaseApiResponse($form->Del());


    }

}