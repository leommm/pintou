<?php


namespace app\modules\mch\controllers;


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
        $query->orderBy('sort ASC,create_time DESC');
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

    }

}