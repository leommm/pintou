<?php


namespace app\models;


class Enum
{
    //认证申请状态
    public static $APPLY_STATUS_TYPE = [
        0 => '未审核', 1 => '通过', 2 => '驳回'
    ];

    //拼投意向状态
    public static $STATUS_TYPE = [
        1 => '未审核', 2 => '跟进中', 3 => '已成交', 4 => '未成交'
    ];

    //身份类型
    public static $ROLE_TYPE = [
        1 => '认证会员', 2 => '投资保姆', 3 => ' 经纪人'
    ];

    //产品类型
    public static $PRODUCT_TYPE = [
        1 => '车位', 2 => '公寓', 3 => '商铺'
    ];

    //登录身份类型
    public static $LOGIN_TYPE = [
        1 => '认证会员', 2 => '投资保姆', 3 => '经纪人', 4 => '城市合伙人', 5 => '商户'
    ];

    //投资年限类型
    public static $STAGE_TYPE = [
        1 => '1-3年', 2 => '4-6年', 3 => '7-10年'
    ];

    //佣金类型
    public static $COMMISSION_TYPE = [
        1 => 'A账户', 2 => 'B账户', 3 => '一级佣金', 4 => '二级佣金', 5 => '城市佣金', 6 => '保姆佣金'
    ];

    public static function getTypeName($id)
    {
        return self::$PRODUCT_TYPE[$id];
    }

    public static function getRoleName($id)
    {
        return self::$ROLE_TYPE[$id];
    }

    public static function getStageName($id)
    {
        return self::$STAGE_TYPE[$id];
    }

    public static function getTypeNameByString($str = '')
    {
        if (!$str) {
            return '';
        }
        $type = explode(',', $str);
        $type_arr = [];
        foreach ($type as $v) {
            $type_arr[] = self::$PRODUCT_TYPE[$v];
        }
        return implode('、', $type_arr);
    }

    public static function getCommissionStatus($status) {
        $status_arr = [
            0 => '未结算',1 => '已结算'
        ];
        return in_array($status,$status_arr) ? $status_arr[$status] : '未知';
    }


}