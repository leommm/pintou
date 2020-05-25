<?php
/**
 * link: http://tt.tryine.com/
 * copyright: Copyright (c) 2018 CSHOP
 * author: wxf
 */


namespace app\modules\mch\models;

/**
 * @property \app\models\Area $area
 */
class PickLinkForm
{
    public $userAuth;


    /**
     * 小程序菜单跳转链接
     * @return mixed|string
     */
    public function getPickLink()
    {
        $link = $this->link();

        $pickLink = $this->resetPickLink($link, $this->userAuth);

        return json_encode($pickLink);
    }

    /**
     * 小程序底部导航链接
     */
    public function getNavPickLink()
    {
        $navLink = $this->navLink();

        $navPickLink = $this->resetPickLink($navLink, $this->userAuth);

        return json_encode($navPickLink);
    }

    /**
     * 去除账号没有权限的链接
     * @param $link
     * @param $userAuth
     * @return mixed
     */
    public function resetPickLink($link, $userAuth)
    {
        $newData = [];
        foreach ($link as $k => $item) {
            //if (isset($item['sign']) == false || in_array($item['sign'], $userAuth) == true) {
                $newData[] = $item;
            //}
        }

        return $newData;
    }

    /**
     * 导航链接
     * @return array
     */
    public function link()
    {
        return [
            [
                'name' => "商城首页",
                'link' => "/pages/index/index",
                'open_type' => "navigate",
                'params' => []
            ],
            [
                'name' => "项目详情",
                'link' => "pages/projects_detail/projects_detail",
                'open_type' => "navigate",
                'params' => [
                    [
                        'key' => "project_id",
                        'value' => "",
                        'desc' => "project_id请填写在项目中相关的ID"
                    ]
                ]
            ],
            [
                'name' => "拼投动态列表",
                'link' => "pages/dynamic_page/dynamic_page",
                'open_type' => "navigate",
                'params' => [
                    [
                        'key' => "type_id",
                        'value' => "",
                        'desc' => "type_id请填写在动态分类中相关分类的ID"
                    ]
                ]
            ],
            [
                'name' => "拼投动态详情",
                'link' => "pages/dynamic_detail/dynamic_detail",
                'open_type' => "navigate",
                'params' => [
                    [
                        'key' => "detail_id",
                        'value' => "",
                        'desc' => "detail_id请填写在动态中相关的ID"
                    ]
                ]
            ],

        ];
    }

    /**
     * 底部导航可选的链接
     * @return array
     */
    public function navLink()
    {
        return [
            [
                'name' => '首页',
                'url' => '/pages/index/index',
                'params' => []
            ],
            [
                'name' => '分类',
                'url' => '/pages/cat/cat',
                'params' => []
            ],
            [
                'name' => '购物车',
                'url' => '/pages/cart/cart',
                'params' => []
            ],
            [
                'name' => '会员中心',
                'url' => '/pages/member/member',
                'params' => []
            ],
            [
                'name' => '用户中心',
                'url' => '/pages/user/user',
                'params' => []
            ],
            [
                'name' => '商品列表',
                'url' => '/pages/list/list',
                'params' => []
            ],
            [
                'name' => '搜索',
                'url' => '/pages/search/search',
                'params' => []
            ],
            [
                'sign' => 'topic',
                'name' => '专题分类',
                'url' => '/pages/topic-list/topic-list',
                'params' => [
                    [
                        'key' => "type",
                        'value' => "",
                        'desc' => "type请填写在专题分类中的ID 为空则为全部"
                    ]
                ]
            ],
            [
                'sign' => 'video',
                'name' => '视频专区',
                'url' => '/pages/video/video-list',
                'params' => []
            ],
            [
                'sign' => 'miaosha',
                'name' => '秒杀',
                'url' => '/pages/miaosha/miaosha',
                'params' => []
            ],
            [
                'sign' => 'miaosha',
                'name' => '我的秒杀',
                'url' => 'pages/miaosha/order/order',
                'params' => []
            ],
            [
                'name' => '附近门店',
                'url' => '/pages/shop/shop',
                'params' => []
            ],
            [
                'sign' => 'pintuan',
                'name' => '拼团',
                'url' => '/pages/pt/index/index',
                'params' => [
                    [
                        'key' => "cid",
                        'value' => "",
                        'desc' => "cid请填写拼团商品列表的分类ID，为空则跳转地址为 拼团"
                    ]
                ],
            ],
            [
                'sign' => 'pintuan',
                'name' => "我的拼团",
                'url' => "/pages/pt/order/order",
                'params' => []
            ],
            [
                'sign' => 'book',
                'name' => '预约',
                'url' => '/pages/book/index/index',
                'params' => [
                    [
                        'key' => "cid",
                        'value' => "",
                        'desc' => "cid请填写预约商品列表的分类ID,为空则跳转地址为 预约"
                    ]
                ],
            ],
            [
                'sign' => 'book',
                'name' => '我的预约',
                'url' => '/pages/book/order/order',
                'params' => []
            ],
            [
                'name' => "关于我们",
                'url' => "/pages/article-detail/article-detail",
                'params' => [
                    [
                        'key' => "id",
                        'value' => "about_us",
                        'disabled' => 'disabled',
                        'desc' => "id 值为 about_us, 不能改变"
                    ],
                ]
            ],
            [
                'name' => "服务中心",
                'url' => "/pages/article-list/article-list",
                'params' => [
                    [
                        'key' => "id",
                        'value' => "2",
                        'disabled' => 'disabled',
                        'desc' => "id 值为 2, 不能改变"
                    ],
                ]
            ],
            [
                'sign' => 'share',
                'name' => '分销中心',
                'url' => '/pages/share/index',
                'params' => []
            ],
            [
                'name' => '快速购买',
                'url' => '/pages/quick-purchase/index/index',
                'params' => []
            ],
            [
                'name' => '一键拨号',
                'url' => 'tel',
                'open_type' => 'tel',
                'params' => [
                    [
                        'key' => "tel",
                        'value' => "",
                        'desc' => "请填写联系电话"
                    ]
                ],
            ],
            [
                'name' => '小程序',
                'url' => 'wxapp',
                'open_type' => 'wxapp',
                'params' => [
                    [
                        'key' => "appid",
                        'value' => "",
                        'desc' => "请填写小程序appid"
                    ],
                    [
                        'key' => "path",
                        'value' => "",
                        'desc' => "打开的页面路径，如pages/index/index，开头请勿加“/”"
                    ],
                ],
            ],
            [
                'name' => '客服',
                'url' => 'contact',
                'params' => []
            ],
            [
                'name' => '外链',
                'url' => 'web',
                'open_type' => 'web',
                'params' => [
                    [
                        'key' => "web",
                        'value' => "",
                        'desc' => "打开的网页链接（注：域名必须已在微信官方小程序平台设置业务域名）"
                    ]
                ],
            ],
//            [
//                'sign' => 'integralmall',
//                'name' => '积分商城',
//                'url' => '/pages/integral-mall/index/index',
//                'params' => []
//            ],
//            [
//                'sign' => 'integralmall',
//                'name' => '签到',
//                'url' => '/pages/integral-mall/register/index',
//                'params' => []
//            ],
            [
                'sign' => 'mch',
                'name' => "好店推荐",
                'url' => "/mch/shop-list/shop-list",
                'params' => []
            ],
//            [
//                'sign' => 'scratch',
//                'name' => "刮刮卡",
//                'url' => "/scratch/index/index",
//                'params' => []
//            ],
            [
                'sign' => 'mch',
                'name' => "多商户店铺",
                'url' => "/mch/shop/shop",
                'params' => [
                    [
                        'key' => "mch_id",
                        'value' => "",
                        'desc' => "mch_id 请填写入驻商户ID",
                    ]
                ]
            ],
        ];
    }
}
