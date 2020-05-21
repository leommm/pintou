<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/14
 * Time: 9:36
 */

namespace app\models;

use Yii;

class Model extends \yii\base\Model
{
    /**
     * 软删除：已删除
     */
    const IS_DELETE_TRUE = 1;

    /**
     * 软删除：未删除
     */
    const IS_DELETE_FALSE = 0;

    /**
     * 手机号正则表达式
     */
    const MOBILE_PATTERN = "/\+?\d[\d -]{8,12}\d/";


    /**
     * Get model error response
     * @param Model $model
     * @return \app\hejiang\ValidationErrorResponse
     */
    public function getErrorResponse($model = null)
    {
        if (!$model) {
            $model = $this;
        }
        return new \app\hejiang\ValidationErrorResponse($model->errors);
    }

    /**
     * 获取当前用户商城 ID
     * @return mixed
     */
    public function getCurrentStoreId()
    {
        return Yii::$app->controller->store->id;
    }
    public function i_array_column($input, $columnKey, $indexKey=null){
        if(!function_exists('array_column')){
            $columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
            $indexKeyIsNull            = (is_null($indexKey))?true :false;
            $indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
            $result                         = array();
            foreach((array)$input as $key=>$row){
                if($columnKeyIsNumber){
                    $tmp= array_slice($row, $columnKey, 1);
                    $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
                }else{
                    $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
                }
                if(!$indexKeyIsNull){
                    if($indexKeyIsNumber){
                        $key = array_slice($row, $indexKey, 1);
                        $key = (is_array($key) && !empty($key))?current($key):null;
                        $key = is_null($key)?0:$key;
                    }else{
                        $key = isset($row[$indexKey])?$row[$indexKey]:0;
                    }
                }
                $result[$key] = $tmp;
            }
            return $result;
        }else{
            return array_column($input, $columnKey, $indexKey);
        }
    }
    /**
     * 获取当前登录用户 ID
     * @param boolean isGuest 是否未登录：false否|true是
     * @return int|string
     */
    public function getCurrentUserId()
    {
        if (Yii::$app->mchRoleAdmin->isGuest == false) {
            return Yii::$app->mchRoleAdmin->id;
        }

        if (Yii::$app->user->isGuest == false) {
            return Yii::$app->user->id;
        }

        if (Yii::$app->admin->isGuest == false) {
            return Yii::$app->admin->id;
        }
    }

    /**
     * 获取当前用户we7Uid,Id === 1 表示总管理员
     * @return mixed
     */
    public function getCurrentWe7Uid()
    {
        if (Yii::$app->user->isGuest == false) {
            return Yii::$app->user->identity->we7_uid;
        }

        if (Yii::$app->admin->isGuest == false) {
            return Yii::$app->admin->id;
        }
    }
}
