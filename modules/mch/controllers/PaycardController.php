<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/8/18
 * Time: 14:17
 */

namespace app\modules\mch\controllers;

use app\models\Paycard;
use app\models\User;
use app\models\PaycardSet;
use app\modules\mch\models\Model;
use yii\data\Pagination;
class PaycardController extends Controller
{
    public function actionIndex($cat_id=1,$page=1)
    {
        $quest = Paycard::find()->alias('a');
        $jr=\Yii::$app->request->post('jr');
        $qb=\Yii::$app->request->post('qb');
        $nickname=\Yii::$app->request->post('nickname');
      
        $t=time();
        $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));//开始
        $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));//结束
        $where="a.user_id !=0";
        if(!empty($jr))$where.= " and {$start}<=a.addtime and a.addtime<{$end}";
        if(!empty($qb)) $where.="";
        if(!empty($nickname)) $where.=" and b.nickname like '%{$nickname}%'";

          /*  $time=Paycard::findOne(['user_id'=>$this->user_id],['>=', 'addtime', $end],['<=', 'addtime',$start]);*/
        $list=$quest->leftJoin(['b' => User::tableName()], 'a.user_id=b.id')
            ->select('b.nickname,a.id,a.paysite,a.addtime')
            ->orderBy('a.addtime DESC')
            ->where($where)
            ->asArray()
            ->all();
        $count = $quest->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' =>$page-1]);


        return $this->render('index', ['list'=>$list,'cat_id'=>$cat_id,'row_count'=>$count,'pagination'=>$pagination]);

    }

    public function actionEdit($cat_id){

        $model = PaycardSet::findOne([
            'id' => 1,
        ]);


        if (\Yii::$app->request->isPost){

            $distance=\Yii::$app->request->post('distance');
            $paycard_sum=\Yii::$app->request->post('paycard_sum');

            if(!is_null($model)){

                $up=\Yii::$app->db->createCommand()->update('cshopmall_paycard_set',['distance' => $distance,'paycard_sum'=>$paycard_sum], "id = 1")->execute();
               
                if($up>0){
                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'data'=>['cat_id'=>$cat_id]
                    ];
                }else{
                    return [
                        'code' => 1,
                        'msg' => '保存失败',
                        'data'=>['cat_id'=>$cat_id]
                    ];
                }
            }else{
                $add= \Yii::$app->db->createCommand('INSERT INTO `cshopmall_paycard_set` (`distance`,`paycard_sum`) VALUES (:distance,:paycard_sum)', [
                    ':paycard_sum'=>$paycard_sum,
                    ':distance' =>$distance,
                ]  )->execute();
                if($add>0){
                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'data'=>['cat_id'=>$cat_id]
                    ];
                }
            }



        }

        return $this->render('edit', ['model'=>$model]);




    }

}