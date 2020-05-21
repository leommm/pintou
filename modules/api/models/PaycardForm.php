<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/7/15
 * Time: 13:40
 */

namespace app\modules\api\models;
use app\models\Paycard;
use app\models\Station;
use app\models\PaycardSet;
use app\hejiang\ApiCode;
use app\models\StationRecord;
use app\modules\api\models\ApiModel;
use yii\data\Pagination;

class PaycardForm extends ApiModel{

    public $lng1;

    public $lat1;
    public $station_id;
    public $user_id;
    public $paysite;
    public $addtime;
    public $time;
    public $time1;
    public $page = 1;
    public $limit = 20;

    public function save(){
        if(($this->lng1 and $this->lat1)==null) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'当前位置经纬度不全'];
        if($this->station_id==null) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'站点ID不能为空'];

        $station= Station::find()->where(['id' =>$this->station_id])->asArray()->all();

        if(empty($station))return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'没有该站点'];

        $radLat1 = deg2rad($this->lat1);
        $radLat2 = deg2rad($station['0']['lat']);
        $radLng1 = deg2rad($this->lng1);
        $radLng2 = deg2rad($station['0']['lng']);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin( $b / 2), 2))) * 6378.137;
        //保留两位小数
        $s = round($s,2);



        $distance=PaycardSet::find()->where(['id' =>1])->asArray()->all();

        $t=time();
        $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));//开始
        $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));//结束


        $paycard_sum=Paycard::find(['user_id'=>$this->user_id],['>=', 'addtime', $end],['<=', 'addtime',$start])->count();


        if($s < $station['0']['distance']*0.001){


            $record=StationRecord::find()->where(" {$t} <= go_over_time and go_off_time < {$t}  and name = {$station['0']['pid']}")->asArray()->all();

            if($record['0']['user_id']!=$this->user_id)return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'今天不是您的上班时间'];
            $time=Paycard::find()->where(" user_id = $this->user_id and {$start} <= addtime and addtime < {$end} and paysite = '{$station['0']['name']}' ")->asArray()->all();

            if($distance['0']['paycard_sum']!=0){
                if($distance['0']['paycard_sum']<$paycard_sum)return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'已超出打卡次数'];

            }


            if(!empty($time))return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'该站点不能重复打卡'];

            $add= \Yii::$app->db->createCommand('INSERT INTO `cshopmall_paycard` (`user_id`,`paysite`,`addtime`,`time`) VALUES (:user_id,:paysite,:addtime,:time)', [
                ':user_id' =>$this->user_id,
                ':paysite'=>$station['0']['name'],
                ':addtime'=>$this->addtime,
                ':time'=>date('Y-m-d',time())

            ]  )->execute();
            if ($add>0) {
                return [
                    'code' => 0,
                    'msg'  =>'打卡成功',
                ];
            } else {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg'  => '打卡失败,请稍后再试',
                ];
            }

        }else{
            return [
            'code' => ApiCode::CODE_ERROR,
            'msg'  => '打卡失败,尚未在打卡位置',
        ];

        }

    }


    public function card(){
        $t=time();
        $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));//开始
        $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));//结束

        $query = Station::find()->alias('a')
            ->leftJoin(['c' => Paycard::tableName()], "a.name=c.paysite and {$start}<=c.addtime and c.addtime<{$end}")
            ->select('a.id,a.name,c.addtime')
            ->andWhere('a.pid != 0')
            ->orderBy('a.sort DESC')
            ->asArray()
            ->all();

        foreach($query as $k =>$v){
            if($v['addtime']!=null){
                unset($query[$k]);
            }

        }
      if($query){
          return [
              'code' => 0,
              'msg' => 'success',
              'data' => $query
          ];
      }


    }


public function paylist(){


        $where="user_id = {$this->user_id}";

        if (!is_null($this->time))$where.=" and {$this->time}<=addtime";
        if (!is_null($this->time1))$where.=" and addtime < {$this->time1}";

        $query = Paycard::find()->where($where);
        $list=$query->all();
        foreach ($list as $k => $v) {
           $v['addtime']=date('H:i',$v['addtime']);
           $list[$k]=$v;
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'row_count' => $count,
                'page_count' => $pagination->pageCount,
                'list' => $list,
            ],
        ];
    }

}