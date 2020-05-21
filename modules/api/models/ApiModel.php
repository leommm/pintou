<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/7/5
 * Time: 16:01
 */

namespace app\modules\api\models;

use app\models\Model;
use luweiss\wechat\Wechat;

class ApiModel extends Model
{
    /**
     * @return Wechat
     */
    public function getWechat()
    {
        return isset(\Yii::$app->controller->wechat) ? \Yii::$app->controller->wechat : null;
    }
}
