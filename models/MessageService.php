<?php


namespace app\models;


class MessageService
{

    public static function createMsg($id,$type,$title) {
        $msg = new SystemMessage();
        $msg->member_id = $id;
        $msg->type = $type;
        $msg->title = $title;
        $msg->content = $title;
        return $msg->save();
    }

}