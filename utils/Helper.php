<?php


namespace app\utils;


class Helper
{
    /**
     * 是否存在下一页
     * @param $page
     * @param $limit
     * @param $count
     * @return int
     */
    public static function judgeNext($page, $limit, $count) {
        if ($page * $limit <= $count) {
            return 1;
        }
        return 0;
    }

}