<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/7/27
 * Time: 18:41
 */

namespace app\modules\api\models\group;

use app\models\CmGoods;
use app\modules\api\models\ApiModel;

class CmGoodsAttrInfoForm extends ApiModel
{
    public $goods_id;
    public $attr_list;
    public $group_id;

    public function rules()
    {
        return [
            [['goods_id', 'attr_list','group_id'], 'required'],
        ];
    }

    public function search()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $this->attr_list = json_decode($this->attr_list, true);
        $goods = CmGoods::findOne($this->goods_id);
        if (!$goods) {
            return [
                'code' => 1,
                'msg' => '商品不存在',
            ];
        }
        $res = $goods->getAttrInfo($this->attr_list, $this->group_id);

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $res,
        ];
    }
}
