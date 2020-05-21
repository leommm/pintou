<?php

/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/8/3
 * Time: 14:36
 * Version: 1.5.2
 */

namespace app\modules\mch\controllers;



class UploadController extends Controller
{

    public function state()
    {
        $file = \yii::$app->request->file('file');
        if ($file) {
            $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'state';

            $info = $file->move($path);

            if ($info) {
                $picurl = DS . 'uploads' . DS . 'state' . DS . $info->getSaveName();
                $this->success('', '', $picurl);
            } else {
                // 上传失败获取错误信息
                echo $file->getError('');
            }
        }
    }

    public function datum()
    {
        $file = \yii::$app->request->file('file');
        if ($file) {
            $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'datum';
            $info = $file->move($path);
            if ($info) {
                $picurl = DS . 'uploads' . DS . 'datum' . DS . $info->getSaveName();
                $this->success('', '', $picurl);
            } else {
                // 上传失败获取错误信息
                echo $file->getError('');
            }
        }
    }

    public function forum()
    {

        $file = \yii::$app->request->file('file');
        if ($file) {
            $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'forum';
            $info = $file->move($path);
            if ($info) {
                $picurl = DS . 'uploads' . DS . 'forum' . DS . $info->getSaveName();
                $this->success('', '', $picurl);
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }


    public function actionGameimg()
    {



        if ($file) {
            $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'activity';

            $info = $file->move($path);


            if ($info) {
                $picurl = DS . 'uploads' . DS . 'activity' . DS . $info->getSaveName();
                $this->success('', '', $picurl);
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function head()
    {
        $file = \yii::$app->request->file('file');
        if ($file) {
            $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'ask';
            $info = $file->move($path);
            if ($info) {
                $picurl = DS . 'uploads' . DS . 'ask' . DS . $info->getSaveName();
                $this->success('', '', $picurl);
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function group()
    {

        $file = \yii::$app->request->file('file');
        if ($file) {
            $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'group';
            $info = $file->move($path);
            if ($info) {
                $picurl                = DS . 'uploads' . DS . 'group' . DS . $info->getSaveName();
                $_SESSION['group_pic'] = $picurl;
                $this->success('', '', $picurl);
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }


    }
}