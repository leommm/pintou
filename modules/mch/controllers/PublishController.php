<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/8/18
 * Time: 14:17
 */

namespace app\modules\mch\controllers;

use app\models\Publish;
use app\models\User;
use app\models\Station;
use app\modules\mch\models\Model;
use yii\data\Pagination;
class PublishController extends Controller
{
    public function actionIndex($cat_id = 1,$page=1)
    {

        $nickname=\Yii::$app->request->get('nickname');
        $contact_way=\Yii::$app->request->get('contact_way');
        $where=" a.type = $cat_id and a.is_delete = 0";
        if(!empty($nickname))$where.=" and b.nickname like '%{$nickname}%'";
        if(!empty($contact_way))$where.=" and b.contact_way  = $contact_way";
        $quest = Publish::find()->alias('a')->where($where);

        $count=$quest->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);
        $list=$quest->leftJoin(['b' => User::tableName()], 'a.user_id = b.id')
            ->leftJoin(['s'=>Station::tableName()],'a.station_id = s.id')
            ->select('s.name,a.*,b.nickname,b.contact_way')
            ->limit($pagination->limit)->offset($pagination->offset)
            ->asArray()
            ->orderBy('a.create_time DESC')->all();


        foreach ($list as $k =>$v){
            if($v['type']==1)$type="买入";
            if($v['type']==2)$type="卖出";
            if($v['type']==3)$type="服务";

            if($v['state']==1)$state="已接受";
            if($v['state']==2)$state="未接受";
            if($v['state']==3)$state="处理中";
            if($v['state']==4)$state="已完成";
            $v['type']=$type;
            $v['state']=$state;
            $list[$k]=$v;
        }


        return $this->render('index', [
            'list' => $list,
            'cat_id' => $cat_id,
            'pagination'=>$pagination,
            'row_count'=>$count
        ]);
    }
//保存
    public function actionEdit($id)
    {


        $model = Publish::findOne([
            'id' => $id,
        ]);
        if (\Yii::$app->request->isPost) {
            $state = \Yii::$app->request->post('state');
            $update=\Yii::$app->db->createCommand()->update('cshopmall_publish', ['state' => $state], "id = {$id}")->execute();
            if($update>0){
                return [
                    'code' => 0,
                    'msg' => '保存成功',
                ];
            }else{
                return [
                    'code' => 1,
                    'msg' => '保存失败',
                ];
            }
        }


        return $this->render('edit', [
            'model' => $model,
        ]);


    }


    /*
     * 删除
     * */
    public function actionDel($id)
    {
        $model = Publish::find()->where(['id'=>$id])->asArray()->one();
        if ($model) {
            if(intval($model['is_delete']) == 0){
                $update=\Yii::$app->db->createCommand()->update('cshopmall_publish', ['is_delete' => 1], "id = {$id}")->execute();
                if($update>0){
                    return [
                        'code' => 0,
                        'msg' => '删除成功',
                    ];
                }
            }

        }
    }


    //审核
    public function actionAudit($id){
        $model = Publish::findOne([
            'id' => $id,
        ]);
        if ($model) {
            if($model['audit'] = 2){
                $update=\Yii::$app->db->createCommand()->update('cshopmall_publish', ['audit' => 1], "id = {$id}")->execute();
              if($update>0){
                  return [
                      'code' => 0,
                      'msg' => '审核通过',
                  ];
              }
            }

        }

    }

}
