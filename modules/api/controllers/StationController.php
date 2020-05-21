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
use app\models\Station;
use app\models\StationRecord;
use app\models\User;
use app\models\Paycard;


use app\hejiang\ApiCode;
use app\modules\api\models\scratch\ScratchForm;
use yii\data\Pagination;


class StationController extends Controller
{

    //Â·ÏßÍ¼
    public function actionIndex($page=1,$limit=20)
    {

       $user_id = \Yii::$app->user->id;
       $user=User::findOne(['id'=>$user_id]);
       if($user->type==3){
        $query = StationRecord::find()->alias('a')
               ->leftJoin(['b' => Station::tableName()], 'a.name=b.id')
               ->where("a.user_id = {$user_id} and b.is_show = 1 and b.is_delete = 1")
               ->select('a.*,b.*,b.id as sta_id')
               ->asArray()
               ->all();
           foreach ($query as $k =>$v){
            $v['count']= Station::find()->alias('a')->where(['a.pid'=>$v['sta_id'],'a.is_delete' => 1])->count();
               $v['depart_time']=date('Y-m-d H:i:s',$v['depart_time']);
            $query[$k]=$v;
        }  
       return new BaseApiResponse(['code'=>0,'msg'=>"²Ù×÷³É¹¦", 'data' => [
               
                'list' => $query,
            ]]);
       }else{

        $query = Station::find()->where([
            'pid'=>0,
            'is_delete' => 1,
            'is_show'=>1,
        ]);
        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'page' => $page - 1, 'pageSize' => $limit]);
        $list=$query->limit($pagination->limit)->offset($pagination->offset)->orderBy('sort DESC')->asArray()->all();
        foreach ($list as $k =>$v){
            $v['count']= Station::find()->alias('a')->where(['a.pid'=>$v['id'],'a.is_delete' => 1])->count();
            $list[$k]=$v;
        }

        foreach ($list as $k => $v) {
          $v['depart_time']=date('Y-m-d H:i:s',$v['depart_time']);
          $list[$k]=$v;
        }
        
        if($list){
            return new BaseApiResponse(['code'=>0,'msg'=>"²Ù×÷³É¹¦", 'data' => [
                'row_count' => $count,
                'page_count' => $pagination->pageCount,
                'list' => $list,
            ]]);
        }else{

            return new BaseApiResponse(['code'=>1,'msg'=>"²Ù×÷Ê§°Ü",'data'=>$list]);
        }
}


   

    }
     public function actionPayrecord(){
        $user_id = \Yii::$app->user->id;
        $id=\Yii::$app->request->get('id');
        $station=Station::find()->where(['pid'=>$id,'is_delete'=>1,'is_show'=>1])->count();


       $array=['date'=>date('d',time())];
       $typeArr = array();

       for ($i=1; $i <$array['date'] ; $i++) { 
           $a=date("Y-m-{$i}",time());
       
           $record = Station::find()->alias('a')
            ->leftJoin(['d' => Paycard::tableName()], "a.name=d.paysite ")
            ->where(['a.pid'=>$id,'d.time'=>$a,'d.user_id'=>$user_id,'a.is_delete'=>1,'a.is_show'=>1])
            ->count();

            $type['date'] = strtotime($a);
            $type['type']=0;
            if($record == $station) $type['type']=1;
            array_push($typeArr, $type);
           
       }

      return new BaseApiResponse(['code'=>0,'msg'=>"ok",'data'=>$typeArr]);
       

   }
    public function actionPayard(){


        $id=\Yii::$app->request->get('id');
        $lng=\Yii::$app->request->get('lng');
        $lat=\Yii::$app->request->get('lat');
        $user_id=\Yii::$app->user->id;

        $station=Station::findOne(['id'=>$id]);

        $t=time();
        $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));//¿ªÊ¼
        $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));//½áÊø
        $radLat1 = deg2rad($lat);
        $radLat2 = deg2rad($station->lat);
        $radLng1 = deg2rad($lng);
        $radLng2 = deg2rad($station->lng);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin( $b / 2), 2))) * 6378.137;
        //±£ÁôÁ½Î»Ð¡Êý
        $s = round($s,2);
        $s>($station->distance*0.001)?$data['jfdk']=0:$data['jfdk']=1;
       
        
        $pay=Paycard::find()->where(" {$start} <= addtime and addtime < {$end} and paysite = '{$station->name}' and user_id = {$user_id} ")->asArray()->all();
         foreach ($pay as $k => $v) {
             $v['addtime']=date('Y-m-d H:i');
             $pay[$k]=$v;
         }
       
        empty($pay)?$data['is_pay']=0:$data['is_pay']=1;
        $data['name']=$station->name;
        return new BaseApiResponse(['code'=>0,'msg'=>"²Ù×÷³É¹¦",'data'=>$data]);
       

    }

    public function actionDetail()
    {

        $id=\Yii::$app->request->get('id');
        $user_id =\Yii::$app->user->id;
        $start=\Yii::$app->request->get('start');
        $end=\Yii::$app->request->get('end');

       
        if(is_null($id) || empty($id))  return new BaseApiResponse(['code'=>1,'msg'=>"缺少路线id"]);


        $b = Station::findOne(['id'=>$id]);
        $a = Station::find()->where(['pid'=>$id,'is_delete'=>1]);
        $nickname=User::findOne(['id'=>$user_id]);
        $time=time();

        $Station = Station::find()->alias('a')
            ->leftJoin(['d' => StationRecord::tableName()], "a.id=d.name and {$time} < d.go_over_time  and d.go_off_time < {$time}")
            ->leftJoin(['b' => User::tableName()], 'b.id=d.user_id')
            ->leftJoin(['c' => Paycard::tableName()], "c.user_id = {$user_id} and a.name=c.paysite and {$start} <= c.addtime and c.addtime < {$end}")
            ->where(['a.pid'=>$id,'a.is_delete'=>1])
            ->select('a.id,a.name,c.user_id,c.addtime,a.lng,a.lat')
            ->orderBy('a.sort desc')
            ->asArray()
            ->all();

        foreach ($Station as $k => $v) {
            if(!is_null($v['addtime']))$v['addtime']=date('Y-m-d,H:i',$v['addtime']);
           $Station[$k]=$v;
        }

        $station_user=User::findOne(['id'=>$Station['user_id']]);
        $query['phone']=$station_user->contact_way;
        $query['list']=$Station;
        $query['nickname']=$nickname->nickname;
        $query['station']=$b->name;
        $query['count']=$a->count();
     
        if($query){
            return new BaseApiResponse(['code'=>0,'msg'=>"操作成功",'data'=>$query]);
        }else{
            return new BaseApiResponse(['code'=>1,'msg'=>"操作失败",'data'=>$query]);
        }

    }

}
