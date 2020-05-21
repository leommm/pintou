<?php


namespace app\modules\mch\controllers;


use app\hejiang\ApiResponse;
use app\models\CommissionLog;
use app\models\Member;
use app\models\PintouShop;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class MemberController extends Controller
{
    public function actionIndex($type=0) {
        $query = (new \yii\db\Query())->select(['member.*','parent.real_name as parent_name'])->from(['member' => Member::tableName()])
            ->leftJoin(['parent'=>Member::tableName()],'member.parent_id=parent.id')->where(['member.is_delete' => 0]);
        if ($type != 0) {
            if ($type!=4) {
                $query->andWhere(['member.role'=>$type]);
            }else{
                $query->andWhere(['member.is_partner'=>1]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
        ]);
        $list = $query->orderBy('member.create_time DESC')->limit($pagination->limit)->offset($pagination->offset)->all();
        return $this->render('index', [
            'list' => $list,
            'pagination' => $pagination,
            'type' => $type
        ]);
    }

    public function actionEdit($id=0) {
        $model = Member::findOne([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if(!$id) {
            $model = new Member();
        }
        if (\Yii::$app->request->isPost) {
            $model->attributes = \Yii::$app->request->post();
            //判断该手机号是否存在
            if ($model->isNewRecord) {
                $exist1 = Member::find()->andWhere(['phone'=>$model->phone,'is_delete'=>0])->exists();
                if ($exist1) {
                    return ['code'=>1,'msg'=>'该手机号已被使用'];
                }

            }
            //判断该区域是否存在城市合伙人
            if ($model->is_partner) {
                $m_id = 0;
                if (isset($model->id)) {
                    $m_id = $model->id;
                }
                $exist = Member::find()->andWhere(['d_id'=>$model->d_id,'is_partner'=>1,'is_delete'=>0])->andWhere(['!=','id',$m_id])->exists();
                if ($exist) {
                    return ['code'=>1,'msg'=>'该区域已存在城市合伙人'];
                }
            }
            if (!$model->isNewRecord && $model->parent_id == $model->id){
                return ['code'=>1,'msg'=>'上级不能绑定自身'];
            }

            if (!$model->save()) {
                return new \app\hejiang\ValidationErrorResponse($model->errors);
            }
            return ['code'=>0,'msg'=>'保存成功'];

        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $model = Member::findOne($id);
        $model->is_delete = 1;
        $model->save();
        return ['code'=>0,'msg'=>'已删除'];
    }

    //搜索上级
    public function actionSearchParent($key_word='') {
        $query = Member::find()->select('id,real_name,role,phone')->andWhere(['is_delete'=>0])->andWhere(['in','role',[1,3]]);
        if (!empty($key_word)) {
            $query->andWhere([
                'or',
                ['like','real_name',$key_word],
                ['like','phone',$key_word]
            ]);
        }
        $res = $query->asArray()->all();
        return new ApiResponse(0,'success',$res);
    }

    public function actionSearchMember($key_word = '',$role=1) {
        $query = Member::find()->select('id,real_name,phone')->andWhere(['is_delete'=>0])->andWhere(['role'=>$role]);
        if (!empty($key_word)) {
            $query->andWhere([
                'or',
                ['like','real_name',$key_word],
                ['like','phone',$key_word]
            ]);
        }
        $res = $query->asArray()->all();
        return new ApiResponse(0,'success',$res);
    }

    //佣金记录
    public function actionCommissionLog($is_settle='') {
        $query = CommissionLog::find()->where(['is_delete' => 0])->andWhere(['not in','type',[1,2]]);
        if ($is_settle !== '') {
            $query->andWhere(['is_settle' => $is_settle]);
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
        return $this->render('commission-log', [
            'list' => $dataProvider->getModels(),
            'pagination' => $pagination,
            'search' => ['is_settle'=>$is_settle],
        ]);
    }

    //结算
    public function actionSettle($id) {
        $model = CommissionLog::findOne($id);
        $model->is_settle = 1;
        if (!$model->save()) {
            return ['code'=>1,'msg'=>'结算失败'];
        }
        return ['code'=>0,'msg'=>'结算成功'];
    }

    //商户列表
    public function actionShopList($real_name='') {
        $query = PintouShop::find()->where(['is_delete' => 0]);
        if ($real_name) {
            $query->andWhere(['like','real_name',$real_name]);
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
        return $this->render('shop-list', [
            'list' => $dataProvider->getModels(),
            'pagination' => $pagination,
            'search' => [
                'real_name' => $real_name,
            ],
        ]);
    }



}