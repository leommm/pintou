<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/11
 * Time: 16:07
 */

namespace app\modules\api\models;

use app\hejiang\ApiResponse;
use app\models\UploadConfig;
use app\models\UploadForm;
use luweiss\wechat\Wechat;

/**
 * @property Wechat $wechat 小程序的
 */
class LiveForm extends ApiModel
{
//    public $order_no;
    public $data;//['scene'=>"",'page'=>'','width'=>100]
    public $store;
    public $wechat;


    public function getLiveList()
    {
        $this->wechat = $this->getWechat();
        $this->store = isset(\Yii::$app->controller->store) ? \Yii::$app->controller->store : null;
        //获取微信小程序码
        $access_token = $this->wechat->getAccessToken();
        $data=['start'=>0,'limit'=>10];
        $api = "http://api.weixin.qq.com/wxa/business/getliveinfo?access_token={$access_token}";
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->wechat->curl->post($api, $data);
        if ($this->wechat->curl->error) {
            return new ApiResponse(1, '直播间获取失败');
        }
        $curl = $this->wechat->curl;
        $res = json_decode($curl->response, true);
        if($res['errcode']!=0){
            return new ApiResponse(1, '直播间获取失败2');
        }
        $list=$res['room_info'];
        foreach ($list as $k=>$v){
            $list[$k]['start_time']=date("Y-m-d H:i",$v['start_time']);
            $list[$k]['end_time']=date("Y-m-d H:i",$v['end_time']);
        }
        return new ApiResponse(0, 'success',$list);
    }
}
