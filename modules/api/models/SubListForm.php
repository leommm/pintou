<?php


namespace app\modules\api\models;


use app\models\Member;
use yii\data\Pagination;

class SubListForm extends ApiModel
{
    public $member_id;
    public $page = 1;
    public $limit = 10;

    public function rules()
    {
        return [
            [['member_id'],'required'],
            [['member_id','page','limit'],'integer']
        ];
    }

    public function search() {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $num = Member::find()->alias('a')->andWhere(['a.parent_id'=>$this->member_id])
            ->leftJoin(['b'=>Member::tableName()],'a.id=b.parent_id')
            ->count();
        var_dump($num);die;
        $query =  Member::find()->alias('a')->select('*')->andWhere(['a.parent_id'=>$this->member_id,'a.is_delete'=>0])
            ->innerJoin(['b'=>Member::tableName()],'a.id=b.parent_id and b.is_delete=0');
        var_dump($query->createCommand()->getRawSql());die;
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => $this->limit]);
        $list = $query->limit($pagination->limit)->offset($pagination->offset)->asArray()->all();
        var_dump($list);die;

    }


}