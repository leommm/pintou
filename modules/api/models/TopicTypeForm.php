<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/9/28
 * Time: 14:11
 */

namespace app\modules\api\models;

use app\hejiang\ApiResponse;
use app\models\TopicType;
use yii\data\Pagination;

class TopicTypeForm extends ApiModel
{

    public function search()
    {
        $query = TopicType::find()->where(['is_delete' => 0]);
        $list = $query->orderBy('sort ASC')->select('id,name')->asArray()->all();
        return new ApiResponse(0, 'success', ['list'=>$list]);
    }
}
