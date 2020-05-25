<?php
defined('YII_ENV') or exit('Access Denied');

$urlManager = Yii::$app->urlManager;
$this->title = '协议管理';
?>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form method="post" class="auto-form" action="<?php echo $urlManager->createUrl(['/mch/store/protocol'])?>">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">关于我们</label>
                </div>
                <div class="col-sm-6">
                    <div flex="dir:left box:first">
                        <div>
                                <textarea class="short-row" id="editor"
                                          style="width: 45rem"
                                          name="about_us"><?= $model->about_us ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">拼投攻略</label>
                </div>
                <div class="col-sm-6">
                    <div flex="dir:left box:first">
                        <div>
                                <textarea class="short-row" id="editor2"
                                          style="width: 45rem"
                                          name="pt_introduce"><?= $model->pt_introduce ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">注册协议</label>
                </div>
                <div class="col-sm-6">
                    <div flex="dir:left box:first">
                        <div>
                                <textarea class="short-row" id="editor3"
                                          style="width: 45rem"
                                          name="register_info"><?= $model->register_info ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                </div>
                <div class="col-sm-5">
                    <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="<?= Yii::$app->request->baseUrl ?>/statics/ueditor/ueditor.config.js?v=1.6.2"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/statics/ueditor/ueditor.all.min.js"></script>
<script>
    var ue = UE.getEditor('editor', {
        serverUrl: "<?=$urlManager->createUrl(['upload/ue'])?>",
        enableAutoSave: false,
        saveInterval: 1000 * 3600,
        enableContextMenu: false,
        autoHeightEnabled: false,
    });
    var ue2 = UE.getEditor('editor2', {
        serverUrl: "<?=$urlManager->createUrl(['upload/ue'])?>",
        enableAutoSave: false,
        saveInterval: 1000 * 3600,
        enableContextMenu: false,
        autoHeightEnabled: false,
    });
    var ue3 = UE.getEditor('editor3', {
        serverUrl: "<?=$urlManager->createUrl(['upload/ue'])?>",
        enableAutoSave: false,
        saveInterval: 1000 * 3600,
        enableContextMenu: false,
        autoHeightEnabled: false,
    });
</script>
