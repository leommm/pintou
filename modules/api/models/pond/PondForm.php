<?php

namespace app\modules\api\models\pond;

use app\models\Pond;
use app\modules\api\models\ApiModel;
use app\models\Coupon;
use app\models\PondSetting;
use app\models\PondLog;
use app\models\GoodsCat;
use app\modules\api\controllers\pond\PondController;
use app\modules\api\models\StoreFrom;

use app\utils\GrafikaHelper;
use Curl\Curl;
use Grafika\Color;
use Grafika\Grafika;

class PondForm extends ApiModel
{
    public $user_id;
    public $store_id;

    public function index()
    {
        //todo 上版本之间兼任 
        $store = new StoreFrom();
        $images = $store->search()['pond']['pond'];
        $images['pond-head'] = $images['pond_head'];
        $images['pond-success'] = $images['pond_success'];
        $images['pond-empty'] = $images['pond_empty'];

        $list = Pond::find()
            ->where([
                'store_id' => $this->store_id,
            ])->with(['gift' => function ($query) {
                $query->where([
                    'store_id' => $this->store_id,
                    'is_delete' => 0
                ]);
            }])->with(['coupon' => function ($query) {
                $query->where([
                    'store_id' => $this->store_id,
                    'is_delete' => 0
                ]);
            }])
            ->orderBy('orderby ASC,id ASC')
            ->all();

        $setting = PondSetting::findOne(['store_id' => $this->store_id]);
        $oppty = $setting['oppty'];
        $list = $this->simplifyData($list);

        if ($setting['start_time'] > time() || $setting['end_time'] < time()) {
            return [
                'code' => 1,
                'data' => [
                    'list' => $list,
                    'oppty' => $oppty,
                    'time' => false,
                    'images' => $images
                ]
            ];
        }
        if ($setting->type == 1) {
            $start_time = strtotime(date('Y-m-d 00:00:00', time()));
            $end_time = $start_time + 86400;
        } elseif ($setting->type == 2) {
            $start_time = $setting['start_time'];
            $end_time = $setting['end_time'];
        }
        $log = PondLog::find()
            ->where(['store_id' => $this->store_id, 'user_id' => $this->user_id])
            ->andWhere(['>', 'create_time', $start_time])
            ->andWhere(['<', 'create_time', $end_time])
            ->count();

        if ($log >= $setting['oppty']) {
            return [
                'code' => 1,
                'data' => [
                    'list' => $list,
                    'oppty' => 0,
                    'images' => $images,
                    'time' => true,
                ]
            ];
        } else {
            return [
                'code' => 0,
                'data' => [
                    'list' => $list,
                    'oppty' => $setting['oppty'] - $log,
                    'images' => $images,
                    'time' => true,
                ]
            ];
        }
    }

    protected function simplifyData($data)
    {
        foreach ($data as $key => $val) {
            $newData[$key] = $val->attributes;

            if ($val->gift) {
                $newData[$key]['gift'] = $val->gift->attributes['name'];
            }
            if ($val->coupon) {
                $newData[$key]['coupon'] = $val->coupon->attributes['name'];
            }
        }
        return $newData;
    }

    protected function get_rand($probability)
    {
        // 概率数组的总概率精度
        $max = array_sum($probability);
        foreach ($probability as $key => $val) {
            //$rand_number = mt_rand(1, $max);//从1到max中随机一个值
            $rand_number = $this->random_num(1, $max);

            if ($rand_number <= $val) {//如果这个值小于等于当前中奖项的概率，我们就认为已经中奖
                return $key;
            } else {
                $max -= $val;//否则max减去当前中奖项的概率，然后继续参与运算
            }
        }
    }

    public function lottery()
    {
        $list = PondSetting::findOne(['store_id' => $this->store_id]);

        if ($list['start_time'] > time() || $list['end_time'] < time()) {
            return [
                'code' => 1,
                'msg' => '活动已结束或未开启'
            ];
        }

        if ($list->type == 1) {
            $start_time = strtotime(date('Y-m-d 00:00:00', time()));
            $end_time = $start_time + 86400;
        } elseif ($list->type == 2) {
            $start_time = $list['start_time'];
            $end_time = $list['end_time'];
        } else {
            return [
                'code' => 1,
                'msg' => '参数错误'
            ];
        }
        $log = PondLog::find()
            ->where(['store_id' => $this->store_id, 'user_id' => $this->user_id])
            ->andWhere(['>', 'create_time', $start_time])
            ->andWhere(['<', 'create_time', $end_time])
            ->count();
        if ($log >= $list['oppty']) {
            return [
                'code' => 1,
                'msg' => '机会已用完'
            ];
        }

        $pond = Pond::find()->where(['store_id' => $this->store_id])->all();

        $succ = array();
        $err = array();
        foreach ($pond as $k => $v) {
            if ($v->type != 5) {
                if ($v->stock > 0) {
                    $succ[$v->id] = $v->stock;
                }
            } else {
                $err[$v->id] = $v->id;
            }
        }


        // $rand = mt_rand(0,10000);
        $rand = $this->random_num(1, 10000);

        $max = array_sum($succ);

        if (empty($err)) {
            if ($max > 0) {
                $id = $this->get_rand($succ);
            } else {
                return [
                    'code' => 1,
                    'msg' => '网络异常'
                ];
            }
        } else {
            if ($rand < $list['probability'] && $max > 0) {
                $id = $this->get_rand($succ);
            } else {
                $id = array_rand($err, 1);
            }
        }

        $form = Pond::findOne([
            'store_id' => $this->store_id,
            'id' => $id
        ]);

        $pondLog = new PondLog;
        $pondLog->store_id = $this->store_id;
        $pondLog->user_id = $this->user_id;
        $pondLog->type = $form->type;
        $pondLog->num = $form->num;
        if ($form->type == 1) {
            $pondLog->num = 0;
            $pondLog->price = floatval($form->price);
        }
        $pondLog->status = 0;
        $pondLog->pond_id = $id;
        $pondLog->coupon_id = $form->coupon_id;
        if ($form->type == 4) {
            $pondLog->attr = $form->attr;
        }
        $pondLog->gift_id = $form->gift_id;
        $pondLog->create_time = time();

        $t = \Yii::$app->db->beginTransaction();
        if ($form->type != 5) {
            $sql = 'select * from ' . Pond::tableName() . ' where store_id = ' . $this->store_id . ' and id = ' . $id . ' for update';
            $pond = \Yii::$app->db->createCommand($sql)->queryOne();

            //判断库存是否大于0
            if ($pond['stock'] > 0) {
                //将库存数量减1
                $form->stock = $pond['stock'] - 1;
                if (!$form->save()) {
                    $t->rollBack();
                    return [
                        'code' => 1,
                        'msg' => '网络异常'
                    ];
                }
            } else {
                $pondLog->type = 5;
                if (empty($err)) {
                    return [
                        'code' => 1,
                        'msg' => '网络异常'
                    ];
                } else {
                    $id = array_rand($err, 1);
                };

                $pondLog->pond_id = $id;
                $pondLog->coupon_id = 0;
                $pondLog->attr = '';
                $pondLog->num = 0;
                $pondLog->gift_id = 0;
                $pondLog->price = 0;
            }
        }

        if ($pondLog->save()) {
            $t->commit();
            $array = [
                'oppty' => $list['oppty'] - $log - 1,
                'id' => $id,
                'p_id' => $pondLog->id,
            ];
            return [
                'code' => 0,
                'msg' => '成功',
                'data' => (object)$array
            ];
        } else {
            return $this->getErrorResponse($pondLog);
        }
    }

    public function setting()
    {
        $list = PondSetting::findOne(['store_id' => $this->store_id]);
        $list->end_time = date('Y.m.d H', $list->end_time);
        $list->start_time = date('Y.m.d H', $list->start_time);
        if ($list) {
            return [
                'code' => 0,
                'msg' => '成功',
                'data' => $list
            ];
        }
    }

    protected function random_num($min, $max)
    {
        return mt_rand() % ($max - $min + 1) - $min;
    }
}
