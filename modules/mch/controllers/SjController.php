<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/8/18
 * Time: 14:17
 */

namespace app\modules\mch\controllers;

use app\models\Station;
use app\models\User;
use app\models\StationRecord;
use app\modules\mch\models\Model;
use yii\data\Pagination;
use app\models\Paycard;


class SjController extends Controller
{
    public function actionIndex($cat_id=1,$page=1)
    {






        $lx=Station::find()->where('pid = 0')->all();
        $quest = User::find()->alias('a');
        $nickname=\Yii::$app->request->post('nickname');
        $name=\Yii::$app->request->post('name');
        $where=" a.type = 3 and a.is_delete = 0 ";

        if(!empty($nickname)) $where.=" and a.nickname like '%{$nickname}%'";
        if(!empty($name)) $where.=" and c.id  = $name";
        $count = $quest->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);

        $list=$quest->leftJoin(['b' => StationRecord::tableName()], 'a.id=b.user_id')
            ->leftJoin(['c' => Station::tableName()], 'b.name=c.id')
            ->select('a.contact_way,a.id as user_id,a.nickname,b.id,c.name')
            ->where($where)
            ->limit($pagination->limit)->offset($pagination->offset)
            ->asArray()
            ->all();


        $count = $quest->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);


        return $this->render('index', ['lx'=>$lx,'list'=>$list,'cat_id'=>$cat_id,'row_count'=>$count,'pagination'=>$pagination]);

    }

    public function actionEdit($cat_id,$user_id=null,$id=null){


        if($cat_id == 1){
            $model = Station::find()->where('pid = 0')->all();
            $user=User::findOne(['id'=>$user_id]);
            $record=StationRecord::findOne(['id'=>$id]);
            $station=Station::findOne(['id'=>$record->name]);


        }else{
            $t=time();
            $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));//开始
            $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));//结束

            $model = Station::find()->alias('a')
                ->leftJoin(['d' => StationRecord::tableName()], 'a.id=d.name')
                ->leftJoin(['b' => User::tableName()], 'b.id=d.user_id')
                ->leftJoin(['c' => Paycard::tableName()], "a.name=c.paysite and {$start}<=c.addtime and c.addtime < {$end}")
                ->where(['a.pid'=>$id])
                ->select('a.id,a.name,c.addtime')
                ->orderBy('a.sort desc')
                ->asArray()
                ->all();


        }



        if (\Yii::$app->request->isPost){
            $nickname=\Yii::$app->request->post('nickname');
            $contact_way=\Yii::$app->request->post('contact_way');
            $name=\Yii::$app->request->post('name');
               if(empty($name) )return ['code' => 1, 'msg' => '请选择路线'];
            if(empty($nickname) )return ['code' => 1, 'msg' => '请填写司机名称'];
            if(empty($contact_way) )return ['code' => 1, 'msg' => '请填写手机号'];
         


            $sort=\Yii::$app->request->post('sort');
             if(empty($sort))$sort=10;
            $go_off_time=strtotime(\Yii::$app->request->post('go_off_time'));
            $go_over_time=strtotime(\Yii::$app->request->post('go_over_time'));
            if(empty($go_over_time) or empty($go_off_time))return ['code' => 1, 'msg' => '请填写司机工作时间'];
            $record=StationRecord::find()->where(" {$go_off_time} <= go_over_time and go_off_time < {$go_over_time}  and name = {$name}")->asArray()->all();
           if($record){
               return [
                   'code' => 1,
                   'msg' => '司机安排时间冲突',
                   'data'=>['cat_id'=>$cat_id]
               ];
           }


            if(!is_null($id)){


                $up=\Yii::$app->db->createCommand()->update('cshopmall_station_record',['name'=>$name,'sort'=>$sort,'update_time'=>time(),'go_off_time'=>$go_off_time,'go_over_time'=>$go_over_time], "id = {$id}")->execute();
                \Yii::$app->db->createCommand()->update('cshopmall_user',['nickname'=>$nickname,'contact_way'=>$contact_way], "id = {$user_id}")->execute();

                if($up>0){
                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'data'=>['cat_id'=>$cat_id]
                    ];
                }else{
                    return [
                        'code' => 1,
                        'msg' => '保存失败',
                        'data'=>['cat_id'=>$cat_id]
                    ];
                }
            }else{

                \Yii::$app->db->createCommand()->update('cshopmall_user',['nickname'=>$nickname,'contact_way'=>$contact_way], "id = {$user_id}")->execute();

                $add= \Yii::$app->db->createCommand('INSERT INTO `cshopmall_station_record` (`user_id`,`name`,`create_time`,`sort`,`is_delete`,`go_off_time`,`go_over_time`) VALUES (:user_id,:name,:create_time,:sort,:is_delete,:go_off_time,:go_over_time)', [
                    ':user_id'=>$user_id,
                    ':name' =>$name,
                    ':create_time'=>time(),
                    ':sort' =>$sort,
                    ':go_off_time'=>$go_off_time,
                    ':go_over_time'=>$go_over_time,
                    ':is_delete' =>0
                ]  )->execute();
                if($add>0){
                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'data'=>['cat_id'=>$cat_id]
                    ];
                }
            }



        }

        return $this->render('edit', ['model'=>$model,'station'=>$station,'record'=>$record,'user'=>$user]);




    }
    public function actionDel($user_id){
        $up=\Yii::$app->db->createCommand()->update('cshopmall_user',['is_delete'=>1], "id = {$user_id}")->execute();
        if($up>0){
            return [
                'code' => 0,
                'msg' => '保存成功',
            ];
        }



    }

}