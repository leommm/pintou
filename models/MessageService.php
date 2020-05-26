<?php


namespace app\models;


class MessageService
{

    public static function createMsg($id,$type,$title,$content) {
        $msg = new SystemMessage();
        $msg->member_id = $id;
        $msg->type = $type;
        $msg->title = $title;
        $msg->content = $content;
        return $msg->save();
    }

}