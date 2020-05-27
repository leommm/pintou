<?php


namespace app\models;


class MessageService
{

    //消息单独发送给会员
    public static function createMsg($id,$type,$title,$content,$page_url='') {
        $msg = new SystemMessage();
        $msg->member_id = $id;
        $msg->type = $type;
        $msg->title = $title;
        $msg->content = $content;
        $msg->page_url = $page_url;
        return $msg->save();
    }

    //消息单独发送给商户
    public static function createShopMsg($id,$type,$title,$content,$page_url='') {
        $msg = new SystemMessage();
        $msg->shop_id = $id;
        $msg->type = $type;
        $msg->title = $title;
        $msg->content = $content;
        $msg->page_url = $page_url;
        return $msg->save();
    }

    //公告推送
    public static function pushMsg($title,$content,$page_url) {
        $shop_list = PintouShop::find()->select('id')->andWhere(['is_delete'=>0])->asArray()->all();
        foreach ($shop_list as $shop) {
            self::createShopMsg($shop['id'],0,$title,$content,$page_url);
        }
        $member_list = Member::find()->select('id')->andWhere(['is_delete'=>0])->asArray()->all();
        foreach ($member_list as $member) {
            self::createMsg($member['id'],0,$title,$content,$page_url);
        }
        return true;
    }

}