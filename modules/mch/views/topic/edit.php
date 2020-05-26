<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$urlManager = Yii::$app->urlManager;
$this->title = '动态编辑';
$this->params['active_nav_group'] = 8;
?>
<div class="panel mb-3" id="app">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="auto-form" method="post" return="<?= $urlManager->createUrl(['mch/topic/index']) ?>">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">标题</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="title" value="<?= str_replace("\"", "&quot", $model->title) ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">副标题</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="sub_title" value="<?= str_replace("\"", "&quot", $model->sub_title) ?>">
                </div>
            </div>
            <input value="1"  name="layout" type="text" hidden>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">封面图</label>
                </div>
                <div class="col-sm-6">
                    <div class="upload-group">
                        <div class="input-group">
                            <input class="form-control file-input" name="cover_pic" value="<?= $model->cover_pic ?>">
                            <span class="input-group-btn">
                                <a class="btn btn-secondary upload-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="上传文件">
                                    <span class="iconfont icon-cloudupload"></span>
                                </a>
                            </span>
                            <span class="input-group-btn">
                                <a class="btn btn-secondary select-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="从文件库选择">
                                    <span class="iconfont icon-viewmodule"></span>
                                </a>
                            </span>
                            <span class="input-group-btn">
                                <a class="btn btn-secondary delete-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="删除文件">
                                    <span class="iconfont icon-close"></span>
                                </a>
                            </span>
                        </div>
                        <div class="upload-preview text-center upload-preview">
                            <span class="upload-preview-tip">268&times;202</span>
                            <img class="upload-preview-img" src="<?= $model->cover_pic ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">是否精选</label>
                </div>
                 <div class="col-sm-6">
                    <label class="radio-label">
                        <input value="0" <?= $model->is_chosen == 0 ? 'checked' : null ?> name="is_chosen" type="radio"
                               class="custom-control-input">
                        <span class="label-icon"></span>
                        <span class="label-text">不精选</span>
                    </label>
                    <label class="radio-label">
                        <input value="1" <?= $model->is_chosen == 1 ? 'checked' : null ?> name="is_chosen" type="radio"
                               class="custom-control-input">
                        <span class="label-icon"></span> 
                        <span class="label-text">精选</span>
                    </label>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-2 text-right">
                    <label class="col-form-label required">分类</label>
                </div>
                <div class="col-2">
                    <select class="form-control" name="type">
                        <option value="0" <?= $model->type == 0 ? "selected" : "" ?>>全部</option>
                            <?php foreach ($select as $item) : ?>
                            <option
                                value="<?= $item->value ?>" <?= ($item->value == $model->type) ? "selected" : "" ?>><?= $item->name ?></option>
                            <?php endforeach; ?> 
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">虚拟阅读量</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="virtual_read_count" value="<?= $model->virtual_read_count ?>">
                    <div class="text-muted fs-sm">手机端显示的阅读量=实际阅读量+虚拟阅读量</div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">排序</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="sort" value="<?= $model->sort ?>">
                    <div class="text-muted fs-sm">升序，数字越小排序越靠前，默认1000</div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">动态详情</label>
                </div>
                <div class="col-sm-6">
                    <div flex="dir:left box:first">
                        <div>
                                <textarea class="short-row" id="editor"
                                          style="width: 30rem"
                                          name="content"><?= $model->content ?></textarea>
                        </div>
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



</script>