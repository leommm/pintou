<?php
namespace app\modules\api\models\scratch;
use app\models\Scratch;
use app\modules\api\models\ApiModel;
use app\models\ScratchLog;
use app\models\ScratchSetting;
use app\models\User;

class ScratchForm extends ApiModel
{
    public $user_id;
    public $store_id;

    public function index()
    {
        //检测
        $setting = ScratchSetting::findOne(['store_id' => $this->store_id]);
        if($setting->type==1){
            $start_time = strtotime(date('Y-m-d 00:00:00',time()));
            $end_time = $start_time+86400;
        }else if($setting->type==2){
            $start_time = $setting['start_time'];
            $end_time = $setting['end_time'];
        }else{
            return [
                'code' => 1,
                'msg' => '参数错误',
                'data' => [
                        'oppty' => 0
                    ]
            ];
        }
        $log = ScratchLog::find()
            ->where(['store_id' => $this->store_id,'user_id' => $this->user_id])
            ->andWhere(['>','create_time',$start_time])
            ->andWhere(['<','create_time',$end_time])
            ->andWhere(['<>','status',0])
            ->count();
        if($log >= $setting['oppty']){
            return [
                'code' => 1,
                'msg' => '机会已用完',
                'data' => [
                        'oppty' => 0
                    ]
            ];
        }
        if($setting['start_time']>time() || $setting['end_time']<time()){
            return [
                'code' => 1,
                'msg' => '活动已结束或未开启',
                'data' => [
                        'oppty' => $setting['oppty'] - $log,
                    ]
            ];
        }


        //扣积分
        if($setting['deplete_register'] > 0){
            $user = User::findOne(['id' => $this->user_id, 'store_id' => $this->store_id]);
            if($user->integral < $setting['deplete_register']){
                return [
                    'code' => 1,
                    'msg' => '积分不足',
                    'data' => [
                        'oppty' => $setting['oppty'] - $log,
                    ]
                ];
            }

        }

        ///////
        
        $list = ScratchLog::findOne([
            'store_id' => $this->store_id,
            'status' => 0,
            'user_id' => $this->user_id
        ]);

        if(empty($list)){
            return $this->lottery($setting,$log);
        }else{
            if($list['type']==5){
                return [
                    'code'=>0,
                    'data' => [
                        'list' => $list,
                        'oppty' => $setting['oppty'] - $log
                    ]
                ];
            }else{
                $scratch = Scratch::find()
                    ->where([
                        'store_id' => $this->store_id,
                        'id'=> $list['pond_id'],
                        'status' => 1,
                        'is_delete' => 0
                    ])->with(['gift' => function ($query) {
                        $query->where([
                            'store_id' => $this->store_id,
                            'is_delete' => 0
                        ]);
                    }])->with(['coupon' => function($query){
                        $query->where([
                            'store_id' => $this->store_id,
                            'is_delete' => 0
                        ]);
                    }])->all()[0];

                if($scratch['stock'] > 0){
                    //转换
                    $list = $list->attributes;
                    $list['gift'] = $scratch['gift']['name'];
                    $list['coupon'] = $scratch['coupon']['name'];

                    return [
                        'code'=>0,
                        'data' => [
                            'list' => $list,
                            'oppty' => $setting['oppty'] - $log
                        ]
                    ];
                }else{
                    if($list->delete()){
                        return $this->lottery($setting,$log);
                    }
                }
            }
        }


    }

    protected function simplifyData($data){ 
        foreach($data as $key=>$val){
            $newData[$key] = $val->attributes;
            if($val->gift){
                $newData[$key]['gift'] = $val->gift->attributes['name'];
            }
            if($val->coupon){
                $newData[$key]['coupon'] = $val->coupon->attributes['name'];
            }
        }
        return $newData;
    }

    protected function get_rand($probability) {
        // 概率数组的总概率精度
        $max = array_sum($probability);
        foreach ($probability as $key => $val) {
            //$rand_number = mt_rand(1, $max);//从1到max中随机一个值
            $rand_number = $this->random_num(1,$max);
     
            if ($rand_number <= $val) {//如果这个值小于等于当前中奖项的概率，我们就认为已经中奖
                return $key;
            } else {
                $max -= $val; //否则max减去当前中奖项的概率，然后继续参与运算
            }
        }
    }

    public function lottery($list,$log){
        $award = Scratch::find()->where(['store_id' => $this->store_id,'is_delete'=>0,'status'=>1])->all();
        $succ = array();
        foreach($award as $k=>$v){
            if($v->stock > 0){
                $succ[$v->id] = $v->stock;
            }
        }
        $rand = $this->random_num(1, 10000);
        $max = array_sum($succ);

        if($rand < $list['probability'] && $max>0){

            $id = $this->get_rand($succ);


            $form = Scratch::findOne([
                'store_id' => $this->store_id,
                'id'=> $id,
                'is_delete' => 0,
                'status' => 1,
            ]);

            $scratchLog = new ScratchLog;
            $scratchLog->store_id = $this->store_id;
            $scratchLog->user_id = $this->user_id;
            $scratchLog->type = $form->type;
            $scratchLog->num = $form->num;

            if ($form->type == 1) {
                $scratchLog->num = 0;
                $scratchLog->price = floatval($form->price);
            }
            if ($form->type == 4) {
                $scratchLog->attr = $form->attr;
            }
            $scratchLog->status = 0;
            $scratchLog->pond_id = $form->id;
            $scratchLog->coupon_id = $form->coupon_id;
            $scratchLog->gift_id = $form->gift_id;
            $scratchLog->create_time = time();


            $t = \Yii::$app->db->beginTransaction(); 
            $sql = 'select * from '.Scratch::tableName().' where store_id = '.$this->store_id.' and id = '.$id.' and is_delete = 0 and status = 1 for update';
            $detail = \Yii::$app->db->createCommand($sql)->queryOne();

            if($detail['stock'] > 0){
                $form->stock = $detail['stock'] - 1;
                if(!$form->save()){
                    $t->rollBack();
                    return $this->getErrorResponse($form);
                }
            }else{
                $scratchLog->type = 5;
                $scratchLog->coupon_id = 0;
                $scratchLog->attr = '';
                $scratchLog->num = 0;
                $scratchLog->gift_id = 0;
                $scratchLog->price = 0;
            }

            if($scratchLog->save()){
                $t->commit();

                $scratch = Scratch::find()
                    ->where([
                        'store_id' => $this->store_id,
                        'id'=> $scratchLog->pond_id,
                        'status' => 1,
                        'is_delete' => 0
                    ])->with(['gift' => function ($query) {
                        $query->where([
                            'store_id' => $this->store_id,
                            'is_delete' => 0
                        ]);
                    }])->with(['coupon' => function($query){
                        $query->where([
                            'store_id' => $this->store_id,
                            'is_delete' => 0
                        ]);
                    }])->all()[0];
                //转换
                $scratchLog = $scratchLog->attributes;
                $scratchLog['gift'] = $scratch['gift']['name'];
                $scratchLog['coupon'] = $scratch['coupon']['name'];
                return [
                    'code'=> 0 ,
                    'data' => [
                        'list' => $scratchLog,
                        'oppty' => $list['oppty'] - $log,
                    ]
                ];
            }else{
                $t->rollBack();
                return $this->getErrorResponse($scratchLog);
            }
        }else{
            $scratchLog = new ScratchLog;
            $scratchLog->store_id = $this->store_id;
            $scratchLog->user_id = $this->user_id;
            $scratchLog->type = 5;
            $scratchLog->status = 0;
            $scratchLog->create_time = time();

            if($scratchLog->save()){
                return [
                    'code'=> 0,
                    'data' => [
                        'list' => $scratchLog,
                        'oppty' => $list['oppty'] - $log,
                    ]
                ];
            }else{
                return $this->getErrorResponse($scratchLog);
            }
        }
    }
    public function setting(){
        $setting = ScratchSetting::findOne(['store_id' => $this->store_id]);

        return [
            'code' => 0,
            'msg' => '成功',
            'data' => [
                'setting'=> $setting,
            ]
        ];
    }
    protected function random_num($min,$max){
        return  mt_rand() % ($max-$min+1)-$min;
    }
    
    public function qrcode(){
        return [
            'code' => 0,
            'data' => [
                'name' => "九宫格",
                'pic_url' =>"https://ss0.baidu.com/73x1bjeh1BF3odCf/it/u=1606027738,2970859222&fm=85&s=C1E226E346C67D57DEA13C3C0300D055"
            ]
        ];
    }
}
