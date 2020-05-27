<?php


namespace app\modules\mch\controllers;


use app\models\MessageService;
use app\models\SystemNotice;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class MessageController extends Controller
{
    public function actionNotice($is_push='') {
        $query = SystemNotice::find()->where(['is_delete' => 0]);
        if ($is_push !== '') {
            $query->andWhere(['is_push'=>$is_push]);
        }
        $query->orderBy('create_time DESC');
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $dataProvider->setPagination($pagination);
        return $this->render('notice', [
            'list' => $dataProvider->getModels(),
            'pagination' => $pagination,
            'search' => ['is_push'=>$is_push],
        ]);
    }

    public function actionNoticeEdit($id=0) {
        $model = SystemNotice::findOne([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if(!$id) {
            $model = new SystemNotice();
        }
        if (\Yii::$app->request->isPost) {
            $model->attributes = \Yii::$app->request->post();
            if (!$model->save()) {
                return new \app\hejiang\ValidationErrorResponse($model->errors);
            }
            return ['code'=>0,'msg'=>'保存成功'];

        }
        return $this->render('notice-edit', [
            'model' => $model,
        ]);
    }

    public function actionPush($id) {
        $model = SystemNotice::findOne([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if (!$model) {
            return ['code'=>1,'msg'=>'未找到公告'];
        }
        MessageService::pushMsg($model->title,$model->content,$model->page_url);
        $model->is_push = 1;
        $model->save();
        return ['code'=>0,'msg'=>'已推送'];
    }

}