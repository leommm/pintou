<?php


namespace app\modules\mch\controllers;

use app\hejiang\ApiResponse;
use app\models\Enum;
use app\models\IntentionFollow;
use app\models\Member;
use app\models\MemberIncome;
use app\models\MessageService;
use app\models\Project;
use app\models\ProjectIntention;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class ProjectController extends Controller
{
    //项目列表
    public function actionIndex($type=0) {
        $query = Project::find()->where(['is_delete' => 0]);
        if ($type != 0) {
            $query->andWhere(['like','type',$type]);
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
        return $this->render('index', [
            'list' => $dataProvider->getModels(),
            'pagination' => $pagination,
            'search' => ['type'=>$type],
        ]);
    }

    //项目编辑
    public function actionEdit($id=0) {
        $model = Project::findOne([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if(!$id) {
            $model = new Project();
        }
        if (\Yii::$app->request->isPost) {
            $model->attributes = \Yii::$app->request->post();
            $model->imgs = json_encode(\Yii::$app->request->post('imgs'),JSON_UNESCAPED_SLASHES);
            if (!$model->save()) {
                return new \app\hejiang\ValidationErrorResponse($model->errors);
            }
            return ['code'=>0,'msg'=>'保存成功'];

        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    //项目删除
    public function actionDelete($id) {
        $model = Project::findOne([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if (!$model) {
            return ['code'=>1,'msg'=>'未找到记录'];
        }
        $model->is_delete = 1;
        $model->save();
        return ['code'=>0,'msg'=>'删除成功'];
    }

    //意向列表
    public function actionIntention($type='',$status=0) {
        $query = ProjectIntention::find()->where(['is_delete' => 0]);
        if ($type) {
            $query->andWhere(['like','type',$type]);
        }
        if ($status){
            $query->andWhere(['status' => $status]);
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
        return $this->render('intention', [
            'list' => $dataProvider->getModels(),
            'pagination' => $pagination,
            'search' => [
                'type' => $type,
                'status' => $status
            ],
        ]);
    }

    //意向编辑
    public function actionIntentionEdit($id=0) {
        $model = ProjectIntention::findOne([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if(!$id) {
            $model = new ProjectIntention();
        }
        if (\Yii::$app->request->isPost) {
            if ($model->status == 3) {
                return ['code' => 1 ,'msg' => '该项目已成交，无法做出更改'];
            }
            $model->attributes = \Yii::$app->request->post();
            if (!$model->save()) {
                return new \app\hejiang\ValidationErrorResponse($model->errors);
            }
            return ['code'=>0,'msg'=>'保存成功'];
        }
        return $this->render('intention-edit', [
            'model' => $model,
        ]);
    }

    //意向审核
    public function actionIntentionApply($id,$status) {
        $intention = ProjectIntention::findOne($id);
        $intention->status = $status;
        if ($status == 2) {
            if (!$intention->nanny_id) {
                return ['code'=>1,'msg'=>'请先为该意向分配保姆'];
            }
        }
        $intention->save();
        return ['code'=>0,'msg'=>'操作成功'];

    }

    //意向删除
    public function actionIntentionDelete($id) {
        $intention = ProjectIntention::findOne($id);
        $intention->is_delete = 1;
        $intention->save();
        return ['code'=>0,'msg'=>'已删除'];
    }

    //ajax 请求项目
    public function actionSearchProject($key_word = '') {
        $query = Project::find()->select('id,title,type')->andWhere(['is_delete'=>0]);
        if (!empty($key_word)) {
            $query->andWhere([
                'or',
                ['like','title',$key_word],
                ['like','sub_title',$key_word]
            ]);
        }
        $res = $query->asArray()->all();
        foreach ($res as $k => $v) {
            $type = explode(',',$v['type']);
            $type_arr = [];
            $type_str = [];
            foreach ($type as $k1=>$v1) {
                $name = Enum::getTypeName($v1);
                $type_arr[$k1]['id'] = $v1;
                $type_arr[$k1]['name'] = $name;
                $type_str[] = $name;
            }
            $res[$k]['type'] = $type_arr;
            $res[$k]['type_str'] = implode('|',$type_str);
        }
        return new ApiResponse(0,'success',$res);
    }

    //意向跟进列表
    public function actionIntentionFollow($nanny_id=0,$status=0,$intention_id=0) {
        $nanny_list = Member::find()->select('id,real_name')->andWhere(['role'=>2,'is_delete'=>0])->asArray()->all();
        $query = IntentionFollow::find()->where(['is_delete' => 0]);
        if ($nanny_id) {
            $query->andWhere(['nanny_id'=>$nanny_id]);
        }
        if ($intention_id) {
            $query->andWhere(['intention_id'=>$intention_id]);
        }
        if ($status){
            $query->andWhere(['status' => $status]);
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
        return $this->render('intention-follow', [
            'list' => $dataProvider->getModels(),
            'pagination' => $pagination,
            'search' => [
                'nanny_id' => $nanny_id,
                'status' => $status
            ],
            'nanny_list' => $nanny_list
        ]);
    }

    public function actionMemberIncome($id=0) {
        $intention = ProjectIntention::findOne($id);
        $query = MemberIncome::find()->where(['is_delete' => 0,'intention_id' => $id]);
        $query->orderBy('create_time DESC');
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $dataProvider->setPagination($pagination);
        return $this->render('member-income', [
            'list' => $dataProvider->getModels(),
            'pagination' => $pagination,
            'member_id' => $intention->member_id,
            'intention_id' => $id
        ]);
    }

    public function actionAddIncome() {
        $model = new MemberIncome();
        $model->attributes = \Yii::$app->request->post('data');
        if (!$model->save()) {
            return new \app\hejiang\ValidationErrorResponse($model->errors);
        }
        MessageService::createMsg($model->member_id,1,'系统通知','您拼投的'.$model->intention->project->title . '收益已到账');
        return ['code'=>0,'msg'=>'添加成功'];
    }


}