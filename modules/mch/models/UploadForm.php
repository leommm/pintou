<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25 0025
 * Time: 16:47
 */
namespace app\modules\mch\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }
}