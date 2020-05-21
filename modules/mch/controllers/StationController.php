<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/8/18
 * Time: 14:17
 */

namespace app\modules\mch\controllers;
use yii\web\UploadedFile;

use app\modules\mch\models\Model;
use app\models\Station;
use  app\models\User;
use app\models\District;
use yii\data\Pagination;

use app\models\Paycard;
use app\models\StationRecord;
class StationController extends Controller
{
//路线
    public function actionIndex($cat_id = 1,$id=null,$page=1)
    {


        if($cat_id==1){
            $query = Station::find()->alias('a')->where(['a.pid'=>0,'a.is_delete' => 1]);
            $count = $query->count();
            $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);
            $list = $query

                ->select('a.id,a.name,a.sort,a.create_time,a.depart_time,a.is_show,a.depart_time')
                ->orderBy('a.create_time DESC')
                ->limit($pagination->limit)->offset($pagination->offset)
                ->asArray()
                ->all();
            foreach($list as $k =>$v){
                $v['count']= Station::find()->alias('a')->where(['a.pid'=>$v['id'],'a.is_delete' => 1])->count();
                $list[$k]=$v;

            }


        }else{
            $query = Station::find()->alias('a')->where(['a.pid'=>$id,'a.is_delete' => 1]);
            $count = $query->count();
            $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);
            $list = $query
                ->select('a.id,a.name,a.sort,a.create_time,a.depart_time,a.is_show,a.depart_time')
                ->orderBy('a.create_time DESC')
                ->asArray()
                ->limit($pagination->limit)->offset($pagination->offset)
                ->all();
            foreach($list as $k =>$v){
                $v['count']= Station::find()->alias('a')->where(['a.pid'=>$v['id'],'a.is_delete' => 1])->count();
                $list[$k]=$v;

            }
        /*    $query = User::find()->where(['is_delete' => 0,'type'=>3]);
            $count = $query->count();

            $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);

            $list = $query->asArray()->all();


           foreach($list as $k =>$v){
               if($v['type']==1)$type="管理员";
               if($v['type']==2)$type="普通用户";
               if($v['type']==3)$type="司机";
               if($v['type']==4)$type="站长";
               $v['type'] =$type;

               $list[$k]=$v;
           }*/


        }



        return $this->render('index', ['row_count'=>$count,'pagination'=>$pagination,'list'=>$list,'cat_id' => $cat_id]);
    }

    //司机列表


  //路线编辑/修改
    public function actionEdit($cat_id,$id=null){

        $list = District::find()->where(['parent_id' =>1])->all();

        $model=Station::find()->alias('a')->where(['a.id'=>$id,'a.is_delete' => 1])
            ->leftJoin(['b' => District::tableName()], 'a.province=b.id')
            ->leftJoin(['c' => District::tableName()], 'a.city=c.id')
            ->leftJoin(['d' => District::tableName()], 'a.district=d.id')
            ->select('a.*,b.id as province_id,b.name as province_name,c.id as city_id,c.name as city_name,d.id as district_id,d.name as district_name,')
            ->asArray()
            ->all();

      

        if (\Yii::$app->request->isPost) {

            $name = \Yii::$app->request->post('name');
            $lng = \Yii::$app->request->post('lng');
            $lat = \Yii::$app->request->post('lat');
            $sort = \Yii::$app->request->post('sort');

            $depart_time =strtotime(\Yii::$app->request->post('depart_time'));
            $people_num =\Yii::$app->request->post('people_num');


            $intro = \Yii::$app->request->post('intro');
            $province = \Yii::$app->request->post('province');
            $city = \Yii::$app->request->post('city');
            $district = \Yii::$app->request->post('district');
            $distance= \Yii::$app->request->post('distance');
            $address=\Yii::$app->request->post('address');
            if(empty($sort))$sort=100;

            if($cat_id == 4 || $cat_id == 3){
                if(empty($name))return ['code' => 1, 'msg' => '请填写站点名', 'data' => ['cat_id' => $cat_id]];
                if(empty($lng)&&empty($lat))return ['code' => 1, 'msg' => '请填写站点经纬度', 'data' => ['cat_id' => $cat_id]];
                if(empty($distance))return ['code' => 1, 'msg' => '请填写打卡距离', 'data' => ['cat_id' => $cat_id]];
                if(empty($people_num))return ['code' => 1, 'msg' => '请填写服务人数', 'data' => ['cat_id' => $cat_id]];
                if(empty($address))return ['code' => 1, 'msg' => '请填写具体地点', 'data' => ['cat_id' => $cat_id]];

            }else{
                if(empty($intro))return ['code' => 1, 'msg' => '请填写路线简介', 'data' => ['cat_id' => $cat_id]];
            }

            if ($cat_id == 2) {

                $up = \Yii::$app->db->createCommand()->update('cshopmall_station', [ 'name' => $name, 'sort' => $sort,'intro'=>$intro,'depart_time'=>$depart_time,'update'=>time()], "id = {$id}")->execute();
                if ($up > 0) {
                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'data' => ['cat_id' => $cat_id]
                    ];
                }

            }elseif($cat_id==1){

                $add= \Yii::$app->db->createCommand('INSERT INTO `cshopmall_station` (`name`,`sort`,`pid`,`create_time`,`intro`,`is_delete`,`depart_time`) VALUES (:name,:sort,:pid,:create_time,:intro,:is_delete,:depart_time)', [
                    ':name'=>$name,
                    ':sort'=>$sort,
                    ':pid'=>0,
                    ':create_time'=>time(),
                    ':depart_time'=>$depart_time,
                    ':is_delete'=>1,
                    ':intro'=>$intro,
                ]  )->execute();
                if($add>0){
                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'data'=>['cat_id'=>$cat_id]
                    ];
                }
            }elseif ($cat_id==4) {

                $add = \Yii::$app->db->createCommand('INSERT INTO `cshopmall_station` (`name`,`lng`,`lat`,`sort`,`pid`,`create_time`,`is_delete`,`province`,`city`,`district`,`distance`,`people_num`,`address`) VALUES (:name,:lng,:lat,:sort,:pid,:create_time,:is_delete,:province,:city,:district,:distance,:people_num,:address)', [

                    ':name' => $name,
                    ':lng' => $lng,
                    ':lat' => $lat,
                    ':sort' => $sort,
                    ':pid' => $id,
                    ':create_time' => time(),
                    ':is_delete' => 1,
                    'province'=>$province,
                    'city'=>$city,
                    'district'=>$district,
                    'distance'=>$distance,
                    'people_num'=>$people_num,
                    'address'=>$address
                ])->execute();
                if ($add > 0) {
                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'data' => ['cat_id' => $cat_id]
                    ];
                }
            }elseif ($cat_id==3) {

                $up = \Yii::$app->db->createCommand()->update('cshopmall_station', ['people_num'=>$people_num,'address'=>$address,'distance'=>$distance,'name' => $name, 'lng' => $lng, 'lat' => $lat, 'sort' => $sort,'province'=>$province,'district'=>$district,'city'=>$city,'update'=>time()], "id = {$id}")->execute();
                if ($up > 0) {
                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'data' => ['cat_id' => $cat_id]
                    ];
                }



            }
        }
        return $this->render('edit', ['list'=>$list,'model'=>$model,'cat_id' => $cat_id]);
    }

    //删除路线
    public function actionDel($id,$sj=null){
        $model = Station::findOne([
            'id' => $id,
        ]);
        if ($model) {
            if($sj == 1 ){
                $update=\Yii::$app->db->createCommand()->update('cshopmall_station', ['is_show' => 1], "id = {$id}")->execute();
                if($update>0){
                    return [
                        'code' => 0,
                        'msg' => '已上架',
                    ];
                }

            }elseif ($sj == 2){
                $update=\Yii::$app->db->createCommand()->update('cshopmall_station', ['is_show' => 2], "id = {$id}")->execute();
                if($update>0){
                    return [
                        'code' => 0,
                        'msg' => '已下架',
                    ];
                }
            }
            if($model['is_delete'] = 1){
                $update=\Yii::$app->db->createCommand()->update('cshopmall_station', ['is_delete' => 2], "id = {$id}")->execute();
                if($update>0){
                    return [
                        'code' => 0,
                        'msg' => '删除成功',
                    ];
                }
            }

        }
    }


    public function actionCity(){
        $id=\Yii::$app->request->get('pid');

        $list = District::find()->where(['parent_id' =>$id])->all();
        if($list){
            return ['code'=>0,'msg'=>'发布成功','date'=>$list];
        }
    }

    //获取区
    public function actionDistrict(){
        $id=\Yii::$app->request->get('pid');

        $list = District::find()->where(['parent_id' =>$id])->all();
        if($list){
            return ['code'=>0,'msg'=>'发布成功','date'=>$list];
        }
    }


}