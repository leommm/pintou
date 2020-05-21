<?php

/**
 * link: http://tt.tryine.com/
 * copyright: Copyright (c) 2018 CSHOP
 * author: wxf
 */

namespace app\modules\mch\controllers;

use app\controllers\Controller;

class ErrorController extends Controller
{
    public function actionPermissionError()
    {
        return $this->render('permission-error');
    }
}
