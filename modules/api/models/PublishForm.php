<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/7/15
 * Time: 13:40
 */

namespace app\modules\api\models;
use app\models\Publish;
use app\hejiang\ApiCode;
use app\models\User;
use app\models\District;
use app\modules\api\models\ApiModel;
use yii\data\Pagination;

class PublishForm extends ApiModel{

    public $title;
    public $content;
    public $image;
    public $type;
    public $user_id ;
    public $page = 1;
    public $limit = 10;
    public $id;
    public $update_type;
    public $city;
    public $province;
    public $district;
    public $status;

    public function save(){

        if(empty($this->title)) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'标题不能为空',];
        if(empty($this->content)) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'简介不能为空',];
        if(empty($this->type)) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'需求类型不能为空',];
        if(empty($this->type)) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'需求类型错误',];
        if(empty($this->province)) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'省份不能为空'];
        if(empty($this->city)) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'市不能为空',];
        if(empty($this->district)) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'区县不能为空',];

        $province=District::findOne(['name'=>$this->province]);
        $city=District::findOne(['name'=>$this->city]);
        $district=District::findOne(['name'=>$this->district]);

        if($this->id){


            if(is_int($this->id)) return ['code' => ApiCode::CODE_ERROR, 'msg'  =>'需求id不能为空',];
            $up = \Yii::$app->db->createCommand()->update('cshopmall_publish', ['title' => $this->title, 'content' => $this->content,'image' => $this->image,'city'=>$city->id,'province'=>$province->id,'district'=>$district->id,'type'=>$this->type], "id = {$this->id}")->execute();
            if ($up>0) {
                return [
                    'code' => 0,
                    'msg'  =>'修改成功',
                ];
            } else {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg'  => '操作失败，请稍后重试',
                ];
            }


        }

        $add= \Yii::$app->db->createCommand('INSERT INTO `cshopmall_publish` (`title`,`content`,`image`,`type`,`create_time`,`user_id`,`province`,`city`,`district`) VALUES (:title,:content,:image,:type,:create_time,:user_id,:province,:city,:district)', [
         ':title' => $this->title,
         ':content'=>$this->content,
         ':image'=>$this->image,
         ':type'=>$this->type,
         ':create_time'=>time(),
         ':user_id'=>$this->user_id,
            ':province'=>$province->id,
            ':city'=>$city->id,
            ':district'=>$district->id,
     ]  )->execute();
        if ($add>0) {
            return [
                'code' => 0,
                'msg'  =>'添加成功',
            ];
        } else {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg'  => '操作失败，请稍后重试',
            ];
        }

    }
    public function search(){

         if(empty($this->type))  return ['code' => ApiCode::CODE_ERROR, 'msg'  => '缺少type',];
         if($this->status==1)$where="a.type = {$this->type} and a.is_delete = 0 and a.user_id = $this->user_id";
         if($this->status==2)$where="a.type = {$this->type} and a.is_delete = 0 ";

        $query = Publish::find()
            ->alias('a')
            ->leftJoin(['u' => User::tableName()], 'a.user_id=u.id')
            ->leftJoin(['p' => District::tableName()], 'a.province=p.id')
            ->leftJoin(['c' => District::tableName()], 'a.city=c.id')
            ->leftJoin(['d' => District::tableName()], 'a.district=d.id')
            ->where($where)
            ->select('a.*,u.time,u.gender,u.nickname,u.avatar_url,p.name as province_name,c.name as city_name,d.name as district_name');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $list=$query->limit($pagination->limit)->offset($pagination->offset)->asArray()->all();
        foreach ($list as $k =>$v){
            $time=(time()-$v['create_time'])/3600;
            $v['time']=round($time,2);

            $v['create_time']=date('Y-m-d',$v['create_time']);
            $list[$k]=$v;
        }

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

    public function del(){
      if(is_null($this->id))return ['code' => ApiCode::CODE_ERROR, 'msg'  => '需求id不能为空',];
        $up = \Yii::$app->db->createCommand()->update('cshopmall_publish', ['is_delete' => 1], "id = {$this->id}")->execute();
        if ($up>0) {
            return [
                'code' => 0,
                'msg'  =>'删除成功',
            ];
        } else {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg'  => '操作失败，请稍后重试',
            ];
        }


    }


}