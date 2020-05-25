<?php


namespace app\modules\api\controllers;



use app\hejiang\ApiResponse;
use app\models\SystemMessage;
use app\models\SystemSetting;
use app\modules\api\models\MessageListForm;

class MessageController extends Controller
{
    public function actionList() {
        $form = new MessageListForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->search();
    }

    public function actionReadAll() {
        $member_id = \Yii::$app->request->get('member_id');
        $shop_id= \Yii::$app->request->get('shop_id');
        if (empty($shop_id) && empty($member_id)) {
            return new ApiResponse(1,'缺少参数');
        }
        if (!empty($member_id)) {
            SystemMessage::updateAll(['is_read'=>1],['member_id'=>$member_id,'is_read'=>0]);
        }
        if (!empty($shop_id)) {
            SystemMessage::updateAll(['is_read'=>1],['shop_id'=>$shop_id,'is_read'=>0]);
        }
        return new ApiResponse(0,'success');
    }

    public function actionDelete() {
        $msg_id = \Yii::$app->request->post('msg_id');
        if (!$msg_id) {
            return new ApiResponse(1,'缺少参数');
        }
        $msg = SystemMessage::findOne($msg_id);
        if (!$msg) {
            return new ApiResponse(1,'未找到消息');
        }
        $msg->is_delete = 1;
        $msg->save();
        return new ApiResponse(0,'success');
    }

}