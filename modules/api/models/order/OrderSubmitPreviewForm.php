<?php
/**
 * @copyright ©2018 Lu Wei
 * @author Lu Wei
 * @link http://www.luweiss.com/
 * Created by Adon
 * Date Time: 2018/7/26 15:42
 */


namespace app\modules\api\models\order;


class OrderSubmitPreviewForm extends OrderForm
{
    public function rules()
    {
        return parent::rules();
    }

    public function search()
    {
        if (!$this->validate())
            return $this->getErrorResponse();
        return [
            'code' => 0,
            'msg' => 'OOKK',
            'data' => [
                'mch_list' => $this->getMchListData(),
            ],
        ];
    }
}