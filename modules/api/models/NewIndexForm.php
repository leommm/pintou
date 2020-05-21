<?php

namespace app\modules\api\models;



use app\hejiang\ApiResponse;
use app\models\Banner;
use app\models\Project;
use app\models\SystemSetting;
use yii\db\Expression;

class NewIndexForm extends ApiModel
{

    public function search()
    {
        $data = [
            'banner_list' => $this->getBanner(),
            'project_list' => $this->getHotProject(),
            'index_info' => $this->getIndexInfo()
        ];
        return new ApiResponse(0, 'success', $data);
    }

    //获取轮播图
    private function getBanner() {
        $ret = Banner::find()->select(['pic_url','page_url','open_type'])->andWhere(['is_delete'=>0])->orderBy('sort')->asArray()->all();
        return $ret;
    }

    //获取热门项目
    private function getHotProject() {
        $ret = Project::find()->select(['id','title','sub_title','cover_pic',new Expression('read_count + virtual_read_count as read_count'),'area','create_time'])
        ->andWhere(['is_delete'=>0,'is_show'=>1,'is_hot'=>1])->asArray()->all();
        foreach ($ret as $k=>$v) {
            if ($v['read_count'] > 10000) {
                $ret[$k]['read_count'] = '浏览' . round($v['read_count']/10000,2) . 'W次';
            }else {
                $ret[$k]['read_count'] = '浏览' . $v['read_count'] . '次';
            }
        }
        return $ret;
    }

    //获取首页资讯
    private function getIndexInfo() {
        $setting = SystemSetting::findOne(1);
        return $setting->index_info;
    }

}