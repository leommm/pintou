<?php
/**
 * link: http://tt.tryine.com/
 * copyright: Copyright (c) 2018 CSHOP
 * author: wxf
 */

namespace app\modules\mch\models\user\integral;

use app\models\IntegralLog;
use app\modules\mch\models\MchModel;
use yii\data\Pagination;

class IndexRechargeForm extends MchModel
{
    public $userId = 0;
    public $type = '';

    public function getIntegralRechargeList()
    {
        $query = IntegralLog::find()
            ->andWhere(['store_id' => $this->getCurrentStoreId(), 'type' => $this->type]);

        if ($this->userId) {
            $query->andWhere(['user_id' => $this->userId]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 20]);
        $list = $query->orderBy('addtime DESC')
            ->limit($pagination->limit)
            ->offset($pagination->offset)
            ->all();

        return [
            'list' => $list,
            'pagination' => $pagination
        ];
    }
}
