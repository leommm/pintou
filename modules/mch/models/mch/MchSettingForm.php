<?php
/**
 * @link http://tt.tryine.com/
 * @copyright Copyright (c) 2018 CSHOP
 * @author Lu Wei
 *
 * Created by Adon.
 * User: Adon
 * Date: 2018/5/30
 * Time: 11:25
 */


namespace app\modules\mch\models\mch;

use app\models\Option;
use app\modules\mch\models\MchModel;
use yii\helpers\Html;

class MchSettingForm extends MchModel
{
    public $store_id;
    public $entry_rules;
    public $type;

    public function rules()
    {
        return [
            [['entry_rules'], 'string', 'max' => 10000,],
            [['type'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'entry_rules' => '入驻协议',
        ];
    }

    public function search()
    {
        $default = [
            'entry_rules' => '',
            'type'=>[]
        ];
        $data = Option::get('mch_setting', $this->store_id, 'mch', $default);
        if (!isset($data['type']) || !$data['type']) {
            $data['type'] = [];
        }
        return $data;
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $data = [
            'entry_rules' => Html::encode($this->entry_rules),
            'type' => $this->type ? $this->type : []
        ];
        $res = Option::set('mch_setting', $data, $this->store_id, 'mch');
        if ($res) {
            return [
                'code' => 0,
                'msg' => '保存成功。',
            ];
        } else {
            return [
                'code' => 1,
                'msg' => '保存失败。',
            ];
        }
    }
}
