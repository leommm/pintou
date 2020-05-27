<?php
defined('YII_ENV') or exit('Access Denied');

$urlManager = Yii::$app->urlManager;
$this->title = '公告编辑';
?>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="auto-form" method="post" return="<?= $urlManager->createUrl(['mch/message/notice'])?> ">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">标题</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="title" value="<?= empty($model->title) ? '系统公告' : $model->title ?>">
                </div>
            </div>
            <div class="form-group row" >
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">内容</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="content" value="<?= $model->content ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">小程序页面链接</label>
                </div>
                <div class="col-sm-6">
                    <div class="input-group page-link-input">
                        <input class="form-control link-input"
                               name="page_url"
                               readonly
                               value="<?= $model->page_url ?>">
                        <span class="input-group-btn">
                            <a class="btn btn-secondary pick-link-btn" href="javascript:">选择链接</a>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                </div>
            </div>
        </form>
    </div>

</div>
