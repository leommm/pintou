<?php
/**
 * link: http://tt.tryine.com/
 * copyright: Copyright (c) 2018 CSHOP
 * author: wxf
 */

namespace app\modules\mch\controllers\permission;


class Menu
{

    public static function getMenu()
    {
        $a = [
            [
                'name' => '系统管理',
                'is_menu' => true,
                'route' => '',
                'icon' => 'icon-setup',
                'children' => [
                    [
                        'name' => '系统设置',
                        'is_menu' => true,
                        'route' => 'mch/store/system-setting',
                    ],
                    [
                        'name' => '微信配置',
                        'is_menu' => true,
                        'route' => 'mch/store/wechat-setting',
                    ],
                    [
                        'name' => '短信通知',
                        'is_menu' => true,
                        'route' => 'mch/store/sms',
                    ],
                    [
                        'name' => '协议配置',
                        'is_menu' => true,
                        'route' => 'mch/store/protocol',
                    ],
                ],
            ],
            [
                'name' => '小程序管理',
                'is_menu' => true,
                'route' => '',
                'icon' => 'icon-xiaochengxu3',
                'children' => [
                    [
                        'name' => '轮播图',
                        'is_menu' => true,
                        'route' => 'mch/store/slide',
                        'sub' => [
                            [
                                'name' => '轮播图(S|U)',
                                'is_menu' => false,
                                'route' => 'mch/store/slide-edit',
                            ],
                        ],
                        'action' => [
                            [
                                'name' => '轮播图删除',
                                'route' => 'mch/store/slide-del',
                            ]
                        ]
                    ],
                    [
                        'name' => '首页广告位',
                        'is_menu' => true,
                        'route' => 'mch/store/index-ad',
                    ],
                    [
                        'offline' => true,
                        'name' => '小程序发布',
                        'is_menu' => true,
                        'route' => 'mch/store/wxapp',
                    ],
                    [
                        'name' => '小程序页面',
                        'is_menu' => true,
                        'route' => 'mch/store/wxapp-pages',
                    ],
                    [
                        'name' => '微信公众号',
                        'is_menu' => true,
                        'route' => 'mch/wechat-platform/setting',
                        'children' => [
                            [
                                'name' => '公众号配置',
                                'is_menu' => true,
                                'route' => 'mch/wechat-platform/setting',
                            ],
                            [
                                'name' => '群发模板消息',
                                'is_menu' => true,
                                'route' => 'mch/wechat-platform/send-msg',
                            ],
                        ],
                    ],
                    [
                        'key' => 'copyright',
                        'name' => '版权设置',
                        'is_menu' => true,
                        'route' => 'mch/we7/copyright',
                    ],
                ],
            ],
            [
                'name' => '项目管理',
                'is_menu' => true,
                'route' => 'mch/project/index',
                'icon' => 'icon-service',
                'children' => [
                    [
                        'name' => '项目列表',
                        'is_menu' => true,
                        'route' => 'mch/project/index',
                        'sub' => [
                            [
                                'name' => '项目编辑',
                                'route' => 'mch/project/edit'
                            ],
                            [
                                'name' => '项目删除',
                                'route' => 'mch/project/delete'
                            ],
                            [
                                'name' => '项目(隐藏|显示)',
                                'route' => 'mch/project/show-hide'
                            ],
                        ]
                    ],
                    [
                        'name' => '项目意向',
                        'is_menu' => true,
                        'route' => 'mch/project/intention',
                        'sub' => [
                            [
                                'name' => '意向编辑',
                                'route' => 'mch/project/intention-edit'
                            ],
                            [
                                'name' => '意向删除',
                                'route' => 'mch/project/intention-delete'
                            ],
                            [
                                'name' => '返佣记录',
                                'route' => 'mch/project/member-income'
                            ],
                        ]
                    ],
                    [
                        'name' => '意向跟进记录',
                        'is_menu' => true,
                        'route' => 'mch/project/intention-follow',
                        'sub' => [
                        ]
                    ],

                ],
            ],
            [
                'name' => '用户管理',
                'is_menu' => true,
                'route' => 'mch/user/index',
                'icon' => 'icon-people',
                'children' => [
                    [
                        'name' => '用户列表',
                        'is_menu' => true,
                        'route' => 'mch/user/index',
                        'sub' => [
                            [
                                'name' => '用户列表Card',
                                'is_menu' => false,
                                'route' => 'mch/user/card',
                            ],
                            [
                                'name' => '用户卡券',
                                'is_menu' => false,
                                'route' => 'mch/user/coupon',
                            ],
                            [
                                'name' => '用户编辑',
                                'is_menu' => false,
                                'route' => 'mch/user/edit',
                            ],
                            [
                                'name' => '用户余额',
                                'is_menu' => false,
                                'route' => 'mch/user/recharge-money-log'
                            ],
                            [
                                'name' => '积分充值记录',
                                'is_menu' => false,
                                'route' => 'mch/user/rechange-log',
                            ],
                        ],
                        'action' => [
                            [
                                'name' => '用户(设置/取消核销员)',
                                'route' => 'mch/user/clerk-edit'
                            ],
                            [
                                'name' => '用户核销员列表',
                                'route' => 'mch/user/clerk'
                            ],
                            [
                                'name' => '用户列表',
                                'route' => 'mch/user/get-user'
                            ],
                            [
                                'name' => '用户删除',
                                'route' => 'mch/user/del'
                            ],
//                            [
//                                'name' => '用户积分充值',
//                                'route' => 'mch/user/rechange'
//                            ],
//                            [
//                                'name' => '用户金额充值',
//                                'route' => 'mch/user/recharge-money'
//                            ],
                            [
                                'name' => '用户卡券删除',
                                'route' => 'mch/user/coupon-del'
                            ],
                        ]
                    ],
                    [
                        'name' => '成员列表',
                        'is_menu' => true,
                        'route' => 'mch/member/index',
                        'sub' => [
                            [
                                'name' => '成员编辑',
                                'is_menu' => false,
                                'route' => 'mch/member/edit',
                            ],
                            [
                                'name' => '成员删除',
                                'is_menu' => false,
                                'route' => 'mch/member/delete',
                            ],
                        ],
                    ],
                    [
                        'name' => '认证申请',
                        'is_menu' => true,
                        'route' => 'mch/member/apply-list',
                        'sub' => [
                            [
                                'name' => '通过/驳回',
                                'is_menu' => false,
                                'route' => 'mch/member/apply',
                            ],
                        ],
                    ],
                    [
                        'name' => '佣金记录',
                        'is_menu' => true,
                        'route' => 'mch/member/commission-log',
                        'sub' => [
                            [
                                'name' => '结算',
                                'is_menu' => false,
                                'route' => 'mch/member/settle',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => '商户管理',
                'is_menu' => true,
                'route' => 'mch/member/shop-list',
                'icon' => 'icon-shanghu',
                'children' => [
                    [
                        'name' => '商户列表',
                        'is_menu' => true,
                        'route' => 'mch/member/shop-list',
                        'sub' => [
                            [
                                'name' => '编辑',
                                'is_menu' => false,
                                'route' => 'mch/member/shop-edit',
                            ]
                        ],
                    ],
                    [
                        'name' => '商户收入',
                        'is_menu' => true,
                        'route' => 'mch/member/shop-income',
                        'sub' => [
                            [
                                'name' => '提现',
                                'is_menu' => false,
                                'route' => 'mch/member/member-cash',
                            ]
                        ],
                    ],
                ],
            ],

            [
                'name' => '内容管理',
                'is_menu' => true,
                'route' => 'mch/article/index',
                'icon' => 'icon-barrage',
                'children' => [
                    [
                        'key' => 'topic',
                        'name' => '动态',
                        'is_menu' => true,
                        'route' => 'mch/topic/index',
                        'sub' => [
                            [
                                'name' => '动态(S|U)',
                                'is_menu' => false,
                                'route' => 'mch/topic/edit',
                            ]
                        ],
                        'action' => [
                            [
                                'name' => '动态删除',
                                'route' => 'mch/topic/delete'
                            ],
                        ],
                    ],
                    [
                        'key' => 'topic',
                        'name' => '动态分类',
                        'is_menu' => true,
                        'route' => 'mch/topic-type/index',
                        'sub' => [
                            [
                                'name' => '动态分类(S|U)',
                                'is_menu' => false,
                                'route' => 'mch/topic-type/edit',
                            ]
                        ],
                        'action' => [
                            [
                                'name' => '动态分类删除',
                                'route' => 'mch/topic-type/delete'
                            ],
                        ],
                    ],
                ],
            ],

            [
                'name' => '营销管理',
                'is_menu' => true,
                'route' => 'mch/coupon/index',
                'icon' => 'icon-coupons',
                'children' => [
                    [
                        'key' => 'coupon',
                        'name' => '优惠券',
                        'is_menu' => true,
                        'route' => 'mch/coupon/index',
                        'sub' => [
                            [
                                'name' => '优惠券改善',
                                'is_menu' => false,
                                'route' => 'mch/coupon/send',
                            ],
                            [
                                'name' => '优惠券编辑',
                                'is_menu' => false,
                                'route' => 'mch/coupon/edit',
                            ]
                        ],
                        'children' => [
                            [
                                'name' => '优惠券管理',
                                'is_menu' => true,
                                'route' => 'mch/coupon/index',
                                'action' => [
                                    [
                                        'name' => '优惠券分类删除',
                                        'route' => 'mch/coupon/delete-cat'
                                    ],
                                    [
                                        'name' => '优惠券删除',
                                        'route' => 'mch/coupon/delete'
                                    ],
                                    [
                                        'name' => '优惠券发放',
                                        'route' => 'mch/coupon/send'
                                    ],
                                ]
                            ],
//                            [
//                                'name' => '自动发放设置',
//                                'is_menu' => true,
//                                'route' => 'mch/coupon/auto-send',
//                                'sub' => [
//                                    [
//                                        'name' => '自动发放编辑',
//                                        'is_menu' => false,
//                                        'route' => 'mch/coupon/auto-send-edit'
//                                    ]
//                                ],
//                                'action' => [
//                                    [
//                                        [
//                                            'name' => '优惠券自动发放设置',
//                                            'route' => 'mch/coupon/auto-send-edit'
//                                        ],
//                                        [
//                                            'name' => '优惠券自动发放方案删除',
//                                            'route' => 'mch/coupon/auto-send-delete'
//                                        ],
//                                    ]
//                                ]
//                            ]
                        ]
                    ],
                ],
            ],
            [
                'admin' => true,
                'name' => '安装应用',
                'is_menu' => true,
                'route' => 'mch/plugin/index',
                'icon' => 'icon-manage',
                'sub' => [
                    [
                        'name' => '应用详情',
                        'is_menu' => false,
                        'route' => 'mch/plugin/detail',
                    ]
                ],
            ],
            [
                'admin' => true,
                'name' => '教程管理',
                'is_menu' => true,
                'icon' => 'icon-iconxuexi',
                'route' => 'mch/handle/index',
                'children' => [
                    [
                        'name' => '操作教程',
                        'is_menu' => true,
                        'route' => 'mch/handle/index',
                    ],
                    [
                        'admin' => true,
                        'name' => '教程设置',
                        'is_menu' => true,
                        'route' => 'mch/handle/setting',
                    ],
                ]
            ],
            [
                'key' => 'permission',
                'name' => '操作员管理',
                'is_menu' => true,
                'icon' => 'icon-quanxianguanli',
                'route' => '',
                'children' => [
                    [
                        'name' => '角色列表',
                        'is_menu' => true,
                        'icon' => 'icon-manage',
                        'route' => 'mch/permission/role/index',
                        'sub' => [
                            [
                                'is_menu' => false,
                                'name' => '添加角色',
                                'route' => 'mch/permission/role/create',
                            ],
                            [
                                'is_menu' => false,
                                'name' => '编辑角色',
                                'route' => 'mch/permission/role/edit',
                            ],
                        ],
                        'action' => [
                            [
                                'name' => '角色(U)',
                                'route' => 'mch/permission/role/update',
                            ],
                            [
                                'name' => '角色删除',
                                'route' => 'mch/permission/role/destroy',
                            ],
                            [
                                'name' => '角色(S)',
                                'route' => 'mch/permission/role/store',
                            ],
                        ]
                    ],
                    [
                        'is_menu' => true,
                        'name' => '用户管理',
                        'route' => 'mch/permission/user/index',
                        'sub' => [
                            [
                                'is_menu' => false,
                                'name' => '添加用户',
                                'route' => 'mch/permission/user/create',
                            ],
                            [
                                'is_menu' => false,
                                'name' => '编辑用户',
                                'route' => 'mch/permission/user/edit',
                            ],
                        ],
                        'action' => [
                            [
                                'name' => '用户(U)',
                                'route' => 'mch/permission/user/update',
                            ],
                            [
                                'name' => '用户删除',
                                'route' => 'mch/permission/user/destroy',
                            ],
                            [
                                'name' => '用户(S)',
                                'route' => 'mch/permission/user/store',
                            ],
                        ]
                    ],
                    [
                        'is_menu' => true,
                        'name' => '操作日志',
                        'route' => 'mch/action-log/index',
                        'sub' => [
                            [
                                'is_menu' => true,
                                'name' => '日志开关',
                                'route' => 'mch/action-log/switch',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => '系统工具',
                'is_menu' => true,
                'icon' => 'icon-xitonggongju',
                'route' => '',
                'children' => [
                    [
                        'is_ind' => true,
                        'admin' => true,
                        'name' => '数据库优化',
                        'is_menu' => true,
                        'route' => 'mch/system/db-optimize',
                    ],
                    [
                        'is_ind' => true,
                        'admin' => true,
                        'name' => '上传设置',
                        'is_menu' => true,
                        'route' => 'mch/store/upload',
                    ],
                    [
                        'admin' => true,
                        'we7' => true,
                        'name' => '权限分配',
                        'is_menu' => true,
                        'route' => 'mch/we7/auth',
                    ],
                    [
                        'is_ind' => true,
                        'admin' => true,
                        'name' => '小程序管理',
                        'is_menu' => true,
                        'route' => 'mch/we7/copyright-list',
                    ],
                    [
                        // TODO 子账号也需要清除缓存权限
                        // 'admin' => true,
                        'is_ind' => true,
                        'name' => '缓存',
                        'is_menu' => true,
                        'route' => 'mch/cache/index',
                    ],
                    [
                        'is_ind' => true,
                        'admin' => true,
                        'offline' => true,
                        'name' => '更新',
                        'is_menu' => true,
                        'route' => 'mch/update/index',
                    ],
                ],
            ],
        ];

        return $a;
    }
}
