<?php
/**
 * link: http://tt.tryine.com/
 * copyright: Copyright (c) 2018 CSHOP
 * author: wxf
 */

namespace app\modules\mch\models\permission\user;

use app\models\User;
use app\modules\mch\models\MchModel;

class EditAdminUserForm extends MchModel
{
    public $userId;

    public function edit()
    {
        $edit = User::find()->andWhere(['id' => $this->userId])->with('roleUser')->one();

        return $edit;
    }
}
