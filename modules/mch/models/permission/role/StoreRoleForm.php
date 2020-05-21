<?php
/**
 * link: http://tt.tryine.com/
 * copyright: Copyright (c) 2018 CSHOP
 * author: wxf
 */

namespace app\modules\mch\models\permission\role;

use app\models\AuthRole;
use app\models\AuthRolePermission;
use app\modules\mch\models\MchModel;
use Yii;

class StoreRoleForm extends MchModel
{
    public $store_id;
    public $name;
    public $description;
    public $role;

    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['role'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '角色名称',
            'description' => '描述',
        ];
    }

    public function store()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $transaction = Yii::$app->db->beginTransaction();

        $model = new AuthRole();
        $model->attributes = $this->attributes;
        $model->created_at = time();
        $model->updated_at = time();
        $model->store_id = $this->getCurrentStoreId();

        if ($model->save()) {
            $this->storeRolePermission($model->id);
            $transaction->commit();

            return [
                'code' => 0,
                'msg' => '添加成功'
            ];
        }

        $transaction->rollBack();
        return $this->getErrorResponse($model);
    }

    public function storeRolePermission($roleId)
    {
        if (empty($this->role)) {
            return false;
        }

        $attributes = [];
        foreach ($this->role as $item) {
            $attributes[] = [
                $item, $roleId,
            ];
        }
        $query = Yii::$app->db->createCommand();
        $insert = $query->batchInsert(AuthRolePermission::tableName(), ['permission_name', 'role_id'], $attributes)->execute();

        if (!$insert) {
            return $this->getErrorResponse($insert);
        }

        return true;
    }
}
