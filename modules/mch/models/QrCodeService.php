<?php


namespace app\modules\mch\models;


class QrCodeService
{
    public static function createCode($type,$id) {
        include \Yii::$app->basePath . '/extensions/phpqrcode/phpqrcode.php';
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小
        $filename = md5(uniqid()) . '.png';
        $path = \Yii::$app->basePath . '/web/qrcode/'.$filename;
        $webRoot = str_replace('http://', 'https://', \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/qrcode/' . $filename);
        $value = [
            'token'=> 'PTfurebuLO4aH5mfsloXsgU3P6Hafdgn',
        ];
        if($type == 1) {
            $value['member_id'] = $id;
        }elseif($type==2){
            $value['shop_id'] = $id;
        }
        //生成二维码图片
        \QRcode::png(json_encode($value), $path, $errorCorrectionLevel, $matrixPointSize, 2,true);
        return $webRoot;
    }

}