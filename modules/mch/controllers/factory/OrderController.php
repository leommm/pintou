<?php

/**
 * Created by PhpStorm.
 * User: peize
 * Date: 2017/12/7
 * Time: 20:01
 */

namespace app\modules\user\controllers\community;

use app\models\Express;
use app\models\PtNoticeSender;
use app\models\CmOrder;
use app\models\PtRobot;
use app\modules\api\models\group\OrderRevokeForm;
use app\modules\user\controllers\Controller;
use app\modules\user\behaviors\MchBehavior;
use app\modules\user\behaviors\UserLoginBehavior;
use app\modules\mch\models\ExportList;
use app\modules\mch\models\group\AddRobotForm;
use app\modules\mch\models\group\OrderRefundForm;
use app\modules\mch\models\group\OrderRefundListForm;
use app\modules\mch\models\group\OrderSendForm;
use app\modules\mch\models\group\CmOrderForm;
use app\modules\mch\models\group\PtPrintForm;
use app\modules\mch\extensions\Export;
use yii\web\UploadedFile;

/**
 * Class OrderController
 * @package app\modules\mch\controllers\group
 * 订单列表
 */
class OrderController extends Controller
{
	public function behaviors()
	{
		return [
			'login' => [
				'class' => UserLoginBehavior::className(),
			],
			'mch' => [
				'class' => MchBehavior::className(),
			],
		];
	}
    /**
     * @return string
     * 拼团订单列表
     */
    public function actionIndex($offline = null)
    {
    	$form = new CmOrderForm();
        $form->attributes = \Yii::$app->request->get();
        $form->attributes = \Yii::$app->request->post();
        if ($offline != null) {
            $form->offline = $offline;
        }
        $form->store_id = $this->store->id;
        $arr = $form->getList($this->mch->id);
        if ($arr === null) {
            return null;
        }

        // 获取可导出数据
        $f = new ExportList();
        $f->order_type = 2;
        $exportList = $f->getList();
		
        return $this->render('index', [
            'list' => $arr['list'],
            'pagination' => $arr['p'],
            'express_list' => $this->getExpressList(),
            'row_count' => $arr['row_count'],
            'exportList' => \Yii::$app->serializer->encode($exportList)
        ]);
    }

    //订单发货
    public function actionSend()
    {
        $form = new OrderSendForm();
        $post = \Yii::$app->request->post();
        if ($post['is_express'] == 1) {
            $form->scenario = 'EXPRESS';
        }
        $form->attributes = $post;
        $form->store_id = $this->store->id;
        return $form->save();
    }

    // 面单打印
    public function actionPrint()
    {
        $id = \Yii::$app->request->get('id');
        $express = \Yii::$app->request->get('express');
        $post_code = \Yii::$app->request->get('post_code');
        $form = new PtPrintForm();
        $form->store_id = $this->store->id;
        $form->order_id = $id;
        $form->express = $express;
        $form->post_code = $post_code;
        return $form->send();
    }

    // 快递列表
    private function getExpressList()
    {
        $storeExpressList = CmOrder::find()
            ->select('express')
            ->where([
                'AND',
                ['store_id' => $this->store->id],
                ['is_send' => 1],
                ['!=', 'express', ''],
            ])->groupBy('express')->orderBy('send_time DESC')->limit(5)->asArray()->all();
        $expressLst = Express::getExpressList();
        $newStoreExpressList = [];
        foreach ($storeExpressList as $i => $item) {
            foreach ($expressLst as $value) {
                if ($value['name'] == $item['express']) {
                    $newStoreExpressList[] = $item['express'];
                    break;
                }
            }
        }

        $newPublicExpressList = [];
        foreach ($expressLst as $i => $item) {
            $newPublicExpressList[] = $item['name'];
        }

        return [
            'private' => $newStoreExpressList,
            'public' => $newPublicExpressList,
        ];
    }

    /**
     * @return string
     * 拼团订单
     */
    public function actionGroup()
    {
        $form = new CmOrderForm();
        $form->attributes = \Yii::$app->request->get();
        $form->store_id = $this->store->id;
        $arr = $form->getGroupList();
        return $this->render('group', [
            'list' => $arr['list'],
            'pagination' => $arr['p'],
            'row_count' => $arr['row_count'],
        ]);
    }

    /**
     * @return string
     * 拼团列表
     */
    public function actionGroupList()
    {
        $form = new CmOrderForm();
        $form->store_id = $this->store->id;
        $pid = \Yii::$app->request->get('pid');
        $arr = $form->getGroupInfo($pid);
        $robot = PtRobot::find()->andWhere(['is_delete' => 0, 'store_id' => $this->store->id])->asArray()->all();
        $surplus = $arr['list'][0]['group_num'] - $arr['row_count'];
        return $this->render('group-list', [
            'list' => $arr['list'],
            'pagination' => $arr['p'],
            'express_list' => $this->getExpressList(),
            'row_count' => $arr['row_count'],
            'robot' => $robot,
            'goods_id' => $arr['list'][0]['goods_id'],
            'pid' => $pid,
            'surplus' => $surplus,
        ]);
    }

    public function actionAddRobot()
    {
        $form = new AddRobotForm();
        $form->store_id = $this->store->id;
        $form->p_id = \Yii::$app->request->get('pid');
        $form->goods_id = \Yii::$app->request->get('goods_id');
        $form->r_id = \Yii::$app->request->get('robot_id');
        return $form->save();
    }

    /**
     * @param $id
     * @param $status
     * 订单取消申请处理
     */
    public function actionApplyDeleteStatus($id, $status)
    {
        $order = CmOrder::findOne([
            'id' => $id,
            'apply_delete' => 2,
            'is_cancel' => 0,
            'store_id' => $this->store->id,
        ]);
        if (!$order) {
            return [
                'code' => 1,
                'msg' => '订单不存在，请刷新页面后重试',
            ];
        }
        if ($status == 1) { //同意
            $form = new OrderRevokeForm();
            $form->order_id = $order->id;
            $form->delete_pass = true;
            $form->user_id = $order->user_id;
            $form->store_id = $order->store_id;
            $res = $form->save();
            if ($res['code'] == 0) {
                return [
                    'code' => 0,
                    'msg' => '操作成功',
                ];
            } else {
                return $res;
            }
        } else { //拒绝
            $order->apply_delete = 0;
            $order->save();
            $msg_sender = new PtNoticeSender($this->wechat, $this->store->id);
            $msg_sender->revokeMsg('您的取消申请已被拒绝');
            return [
                'code' => 0,
                'msg' => '操作成功',
            ];
        }
    }

    //售后订单列表
    public function actionRefund()
    {
        // 获取可导出数据
        $f = new ExportList();
        $f->order_type = 2;
        $f->type = 1;
        $exportList = $f->getList();
        $form = new OrderRefundListForm($this->mch->id);
        $form->attributes = \Yii::$app->request->get();
        $form->attributes = \Yii::$app->request->post();
        $form->store_id = $this->store->id;
        $form->limit = 10;
        $data = $form->search();

        return $this->render('refund', [
            'row_count' => $data['row_count'],
            'pagination' => $data['pagination'],
            'list' => $data['list'],
            'exportList' => \Yii::$app->serializer->encode($exportList)
        ]);
    }

    //订单取消申请处理
    //    public function actionApplyDeleteStatus($id, $status)
    //    {
    //        $order = Order::findOne([
    //            'id' => $id,
    //            'apply_delete' => 1,
    //            'is_delete' => 0,
    //            'store_id' => $this->store->id,
    //        ]);
    //        if (!$order) {
    //            return [
    //                'code' => 1,
    //                'msg' => '订单不存在，请刷新页面后重试',
    //            ];
    //        }
    //        if ($status == 1) {//同意
    //            $form = new OrderRevokeForm();
    //            $form->order_id = $order->id;
    //            $form->delete_pass = true;
    //            $form->user_id = $order->user_id;
    //            $form->store_id = $order->store_id;
    //            $res = $form->save();
    //            if ($res['code'] == 0) {
    //                return [
    //                    'code' => 0,
    //                    'msg' => '操作成功',
    //                ];
    //            } else {
    //                return $res;
    //            }
    //        } else {//拒绝
    //            $order->apply_delete = 0;
    //            $order->save();
    //            $msg_sender = new WechatTplMsgSender($this->store->id, $order->id, $this->wechat);
    //            $msg_sender->revokeMsg('您的取消申请已被拒绝');
    //            return [
    //                'code' => 0,
    //                'msg' => '操作成功',
    //            ];
    //        }
    //    }

    public function actionConfirm()
    {
        $order_id = \Yii::$app->request->get('order_id');
        $order = CmOrder::findOne(['id' => $order_id]);
        if (!$order) {
            return [
                'code' => 1,
                'msg' => '订单不存在，请刷新重试',
            ];
        }
        if ($order->pay_type != 2) {
            return [
                'code' => 1,
                'msg' => '订单支付方式不是货到付款，无法确认收货',
            ];
        }
        if ($order->is_send == 0) {
            return [
                'code' => 1,
                'msg' => '订单未发货',
            ];
        }
        $order->is_confirm = 1;
        $order->confirm_time = time();
        $order->is_pay = 1;
        $order->pay_time = time();
        if ($order->save()) {
            return [
                'code' => 0,
                'msg' => '成功',
            ];
        } else {
            foreach ($order->errors as $error) {
                return [
                    'code' => 1,
                    'msg' => $error,
                ];
            }
        }
    }

    //批量发货
    public function actionBatchShip()
    {
        if (\Yii::$app->request->isPost) {
            $file = \Yii::$app->request->post();
            if (!$file['url']) {
                return [
                    'code' => 1,
                    'msg' => '请输入模板地址'
                ];
            }
            if (!$file['express']) {
                return [
                    'code' => 1,
                    'msg' => '请输入快递公司'
                ];
            }
            $arrCSV = array();
            if (($handle = fopen($file['url'], "r")) !== false) {
                $key = 0;
                while (($data = fgetcsv($handle, 0, ",")) !== false) {
                    $c = count($data);
                    for ($x = 0; $x < $c; $x++) {
                        $arrCSV[$key][$x] = trim($data[$x]);
                    }
                    $key++;
                }
                fclose($handle);
            }
            unset($arrCSV[0]);
            $form = new OrderSendForm();
            $form->store_id = $this->store->id;
            $form->express = \Yii::$app->request->post('express');
            $info = $form->batch($arrCSV);

            return [
                'code' => 0,
                'msg' => '操作成功',
                'data' => $info,
            ];
        }
        return $this->render('batch-ship', [
            'express_list' => $this->getExpressList(),
        ]);
    }

    public function actionShipModel()
    {
        Export::shipModel();
    }

    // 处理售后订单
    public function actionRefundHandle()
    {
        $form = new OrderRefundForms();
        $form->attributes = \Yii::$app->request->post();
        $form->store_id = $this->store->id;
        return $form->save();
    }
}
