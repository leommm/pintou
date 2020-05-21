<?php
/**
 * link: http://tt.tryine.com/
 * copyright: Copyright (c) 2018 CSHOP
 * author: wxf
 */

namespace app\modules\mch\models\permission\permission;

use app\models\AuthRolePermission;
use app\models\User;
use app\modules\mch\models\MchModel;
use Yii;

class IndexPermissionForm extends MchModel
{

    public function getList()
    {
        $menuList = \Yii::$app->controller->getMenuList();
        return $menuList;
    }

    /**
     * 获取当前登录用户所拥有的权限Route
     * @return array
     */
    public function getPermissionByUser()
    {
        $roles = [];
        //只有角色登录才去查权限列表
        if (!Yii::$app->mchRoleAdmin->isGuest) {
            $user = User::find()->where(['id' => $this->getCurrentUserId()])->with('roleUser')->one();

            foreach ($user->roleUser as $item) {
                $roles[] = $item->role->id;
            }
        }
        $permissions = AuthRolePermission::find()->where(['in', 'role_id', $roles])->all();
        $data = [];
        foreach ($permissions as $permission) {
            $data[] = $permission->permission_name;
        }
        return $data;
    }

    /**
     * 获取编辑时的权限列表
     */
    public function getPermissionMenuByUser($roleId)
    {
        $list = $this->getList();
        $permissions = AuthRolePermission::find()->where(['role_id' => $roleId])->all();

        $data = [];
        foreach ($permissions as $permission) {
            $data[] = $permission->permission_name;
        }

        $resetList = $this->resetPermissionMenu($list, $data);
        $permissionsMenu = Yii::$app->serializer->encode($resetList);

        return $permissionsMenu;
    }

    /**
     * 给用户已有的权限加上show字段标识
     * @param $list
     * @param $permissions
     * @return mixed
     */
    public function resetPermissionMenu($list, $permissions)
    {
        foreach ($list as $key => $item) {
            if (in_array($item['route'], $permissions)) {
                $list[$key]['show'] = true;
            }
            if (isset($item['children'])) {
                $list[$key]['children'] = $this->resetPermissionMenu($item['children'], $permissions);

                foreach ($list[$key]['children'] as $i) {
                    if ($i['show'] == true) {
                        $list[$key]['show'] = true;
                        //一级和二级菜单编辑时要设置为空，不然更新是会有bug
                        $list[$key]['route'] = '';
                        break;
                    }
                }
            }
        }

        return $list;
    }
}
