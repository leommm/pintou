<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/8/18
 * Time: 14:17
 */

namespace app\modules\mch\controllers;

use app\models\Article;
use app\modules\mch\models\Model;

class ArticleController extends Controller
{
    public function actionIndex()
    {
        $list = Article::find()->where([
            'store_id' => $this->store->id,
            'is_delete' => 0,
        ])->orderBy('sort ASC,addtime DESC')->all();

        return $this->render('index', [
            'list' => $list,
        ]);
    }

    public function actionEdit($cat_id, $id = null)
    {
        $model = Article::findOne([
            'store_id' => $this->store->id,
            'id' => $id,
            'article_cat_id' => $cat_id,
        ]);
        if (!$model) {
            $model = new Article();
            $model->article_cat_id = $cat_id;
            $model->store_id = $this->store->id;
        }
        if (\Yii::$app->request->isPost) {
            $model->attributes = \Yii::$app->request->post();

            $model->article_cat_id = $cat_id;
            $model->store_id = $this->store->id;
            if ($model->isNewRecord) {
                $model->addtime = time();
            }
            if ($model->save()) {
                return [
                    'code' => 0,
                    'msg' => '保存成功',
                ];
            } else {
                return (new Model())->getErrorResponse($model);
            }
        } else {
            foreach ($model as $index => $value) {
                $model[$index] = str_replace("\"", "&quot;", $value);
            }
            return $this->render('edit', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $model = Article::findOne([
            'id' => $id,
            'store_id' => $this->store->id,
        ]);
        if ($model) {
            $model->is_delete = 1;
            if (!$model->save()) {
                return (new Model())->getErrorResponse($model);
            }
        }
        return [
            'code' => 0,
            'msg' => '删除成功',
        ];
    }
}
