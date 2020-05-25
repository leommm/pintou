<?php


namespace app\modules\api\models;


use app\hejiang\ApiResponse;
use app\models\SystemSetting;

class ProtocolForm extends ApiModel
{
    public $type;

    public function rules()
    {
        return [
          [['type'],'required'],
        ];
    }

    public function search() {
        if (!$this->type) {
            return new ApiResponse(1,'缺少参数');
        }

        $setting = SystemSetting::findOne(1);
        $data = [];
        switch ($this->type) {
            case 1:
                $data['title'] = '关于我们';
                $data['content'] = $setting->about_us;
                break;
            case 2:
                $data['title'] = '拼投攻略';
                $data['content'] = $setting->pt_introduce;
                break;
            case 3:
                $data['title'] = '注册协议';
                $data['content'] = $setting->register_info;
                break;
            default :
                return new ApiResponse(1,'请传入正确的类型');
        }
        return new ApiResponse(0,'success',$data);
    }

}