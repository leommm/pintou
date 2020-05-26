<?php


namespace app\modules\api\models;


use app\models\Member;

class SubListService
{
    private static function getSubIds($id) {
        $first = Member::find()->select('id')->andWhere(['is_active'=>1,'is_delete'=>0,'parent_id'=>$id])->andWhere(['in','role',[1,3]])->asArray()->all();
        $data = [];
        if($first) {
            $data = array_column($first,'id');
        }
        foreach ($data as $v) {
            $second = Member::find()->select('id')->andWhere(['is_active'=>1,'is_delete'=>0,'parent_id'=>$v])->andWhere(['in','role',[1,3]])->asArray()->all();
            if ($second) {
                $temp = array_column($second,'id');
                $data = array_merge($data,$temp);
            }
        }
        return $data;
    }

    public static function getSubCount($id) {
        return count(self::getSubIds($id));
    }

    public static function getSubList($id) {
        return self::getSubIds($id);
    }

}