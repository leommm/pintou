<?php


namespace app\models;


class MessageService
{

    public static function createMsg($id,$type,$title,$content,$page_url='') {
        $msg = new SystemMessage();
        $msg->member_id = $id;
        $msg->type = $type;
        $msg->title = $title;
        $msg->content = $content;
        $msg->page_url = $page_url;
        return $msg->save();
    }

}