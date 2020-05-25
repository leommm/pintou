<?php


namespace app\modules\api\controllers;



use app\hejiang\ApiResponse;
use app\hejiang\BaseApiResponse;
use app\models\Project;
use app\modules\api\behaviors\VisitBehavior;
use app\modules\api\models\CancelIntentionForm;
use app\modules\api\models\CommissionLogForm;
use app\modules\api\models\FollowListForm;
use app\modules\api\models\IntentionFollowForm;
use app\modules\api\models\IntentionListForm;
use app\modules\api\models\IntentionSubmitForm;
use app\modules\api\models\MemberIncomeForm;
use app\modules\api\models\ProjectListForm;


class ProjectController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'visit' => [
                'class' => VisitBehavior::className(),
            ],
        ]);
    }
    /**
     * 项目列表
     * @return ApiResponse
     */
    public function actionList() {
        $form = new ProjectListForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->search();
    }

    /**
     * 项目详情
     * @return ApiResponse
     */
    public function actionDetail() {
        $id = \Yii::$app->request->get('id');
        if (!$id) {
            return new ApiResponse(1,'缺少参数');
        }
        $data = Project::find()->select('id,title,sub_title,content,area,cover_pic,type,read_count,virtual_read_count,is_chosen,is_hot,create_time')
            ->andWhere(['id'=>$id])->asArray()->one();
        $read_count = intval($data['read_count'] + $data['virtual_read_count']);
        unset($data['read_count']);
        unset($data['virtual_read_count']);
        if ($read_count < 10000) {
            $read_count = $read_count . '次浏览';
        }
        if ($read_count >= 10000) {
            $read_count = round($read_count / 10000,2) . 'W次浏览';
        }
        $data['read_count'] = $read_count;
        $data['time'] = date('Y-m-d',strtotime($data['create_time']));
        unset($data['create_time']);
        $data['type'] = explode(',',$data['type']);
        return new ApiResponse(0,'success',$data );
    }

    /**
     * 提交意向
     */
    public function actionIntentionSubmit() {
        $form = new IntentionSubmitForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->submit());
    }

    /**
     * 意向跟进
     */
    public function actionIntentionFollow() {
        $form = new IntentionFollowForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->save());
    }

    /**
     * 意向列表
     */
    public function actionIntentionList() {
        $form = new IntentionListForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->search());
    }

    /**
     * 跟进记录
     */
    public function actionFollowList() {
        $form = new FollowListForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->search());
    }

    /**
     * 取消拼投接口
     */
    public function actionCancelIntention() {
        $form = new CancelIntentionForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->cancel());
    }

    /**
     * 收益记录（会员）
     */
    public function actionMemberIncome() {
        $form = new MemberIncomeForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->search());
    }

    /**
     * 佣金记录
     */
    public function actionCommissionLog() {
        $form = new CommissionLogForm();
        $form->attributes = \Yii::$app->request->post();
        return new BaseApiResponse($form->search());
    }

    /**
     * 下级列表
     */
    public function actionSubList() {

    }


}