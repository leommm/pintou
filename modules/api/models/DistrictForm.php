<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/7/25
 * Time: 14:02
 */

namespace app\modules\api\models;
use Yii;
use app\hejiang\ApiCode;
use app\hejiang\ApiResponse;
use app\models\District;
use app\models\DistrictArr;
class DistrictForm extends ApiModel
{
    public $type;
    public $province;
    public $city;
    public $district;
    public $user_id;

    public function search()
    {


        $cache_key = md5('district');
        $cache_data = \Yii::$app->cache->get($cache_key);
        if ($cache_data && false) {
            $province_list = $cache_data;
        }else{
            $d = new DistrictArr();
            $arr=  $d->getArr();
            $province_list = $d->getList($arr);
            \Yii::$app->cache->set($cache_key, $province_list, 86400 * 7);
        }
        return new ApiResponse(0,'success',$province_list);
        $cache_key = md5('district');
        $cache_data = \Yii::$app->cache->get($cache_key);
        if ($cache_data) {
            $province_list = $cache_data;
        } else {
            $province_list = District::find()->select('id,name')->where(['level' => 'province'])->asArray()->all();
            foreach ($province_list as $i => $province) {
                $city_list = District::find()->select('id,name')->where(['parent_id' => $province['id']])->asArray()->all();
                foreach ($city_list as $j => $city) {
                    $district_list = District::find()->select('id,name')->where(['parent_id' => $city['id']])->asArray()->all();
                    $city_list[$j]['list'] = $district_list;
                }
                $province_list[$i]['list'] = $city_list;
            }
            \Yii::$app->cache->set($cache_key, $province_list, 86400 * 7);
        }
        return new ApiResponse(0,'success',$province_list);
    }
    public function save(){
        if($this->type==null) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'type不能为空',];

       if($this->type==1){
            $province= District::find()->where(['parent_id' =>1])->all();
           if($province){
               return [
                   'code' => ApiCode::CODE_SUCCESS,
                   'msg' => '省份获取成功',
                   'date' => $province
               ];
           }else{
               return [
                   'code' => ApiCode::CODE_ERROR,
                   'msg' => '省份获取失败',
               ];
           }
       }elseif($this->type==2){
           if($this->province == null)return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'省份province不能为空',];
           $city= District::find()->where(['parent_id' =>$this->province])->all();
           if($city){

               return [
                   'code' => ApiCode::CODE_SUCCESS,
                   'msg' => '市获取成功',
                   'date' => $city
               ];
           }else{
               return [
                   'code' => ApiCode::CODE_ERROR,
                   'msg' => '市获取失败',
               ];
           }
       }elseif($this->type==3){
           if($this->city == null)return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'市city不能为空',];


           $district= District::find()->where(['parent_id' =>$this->city])->all();

           if($district){
               return [
                   'code' => ApiCode::CODE_SUCCESS,
                   'msg' => '区县获取成功',
                   'date' => $district
               ];
           }else{
               return [
                   'code' => ApiCode::CODE_ERROR,
                   'msg' => '区县获取失败',
               ];
           }
       }

    }
    public function submit(){
        if($this->province == null)return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'省份province不能为空',];
        if($this->city == null)return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'市city不能为空',];
        if($this->district == null)return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'区县district不能为空',];
        $province=District::findone(['name'=>$this->province]);
        $city=District::findone(['name'=>$this->city]);
        $district=District::findone(['name'=>$this->district]);

        $up = \Yii::$app->db->createCommand()->update('cshopmall_user', ['province' => $province->id,'city'=>$city->id,'district'=>$district->id], "id = {$this->user_id}")->execute();


        $data=[
            'province'=>$province->id,
            'city'=>$city->id,
            'district'=>$district->id,
        ];


        Yii::$app->session->set('location',$data);

        $add=Yii::$app->session->get('location');

        if($up>0){
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '定位成功',
            ];
        }else{
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '定位失败',
            ];
        }

    }


}