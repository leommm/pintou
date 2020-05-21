<?php

/**
 * Created by PhpStorm.
 * User: peize
 * Date: 2017/11/22
 * Time: 14:58
 */

namespace app\modules\mch\controllers\book;

use app\models\YySetting;

class IndexController extends Controller
{
    public function actionIndex()
    {
        return $this->redirect(\Yii::$app->urlManager->createUrl(['mch/book/goods/index']));
    }

    /**
     * @return string
     *
     */
    public function actionSetting()
    {
        $setting = YySetting::findOne(['store_id' => $this->store->id]);
        if (!$setting) {
            $setting = new YySetting();
        }
        if (\Yii::$app->request->isPost) {
            $model = \Yii::$app->request->post('model');
            if ($setting->isNewRecord) {
                $setting->store_id = $this->store->id;
            }
            $setting->attributes = $model;
            if ($setting->save()) {
                return [
                    'code' => 0,
                    'msg' => '保存成功',
                ];
            } else {
                return [
                    'code' => 0,
                    'msg' => '保存失败，请重试',
                ];
            }
        }
        return $this->render('setting', [
            'setting' => $setting,
        ]);
    }
}
