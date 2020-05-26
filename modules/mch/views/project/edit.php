<?php
defined('YII_ENV') or exit('Access Denied');

$urlManager = Yii::$app->urlManager;
$this->title = '项目编辑';
$type = explode(',',$model->type);
?>
<div class="panel mb-3" id="app">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="auto-form" method="post" return="<?= $urlManager->createUrl(['mch/project/index'])?> ">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">标题</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="title" value="<?= str_replace("\"", "&quot", $model->title) ?>">
                </div>
            </div>
            <div class="form-group row" >
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">副标题</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="sub_title" value="<?= str_replace("\"", "&quot", $model->sub_title) ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">包含产品</label>
                </div>
                <div class="col-sm-6">
                    <label class="radio-label">
                        <input value="1" <?= in_array(1,$type) ? 'checked':null ?> name="type[]" type="checkbox">
                        <span class="label-icon"></span>
                        <span class="label-text">车位</span>
                    </label>
                    <label class="radio-label">
                        <input value="2" <?= in_array(2,$type) ? 'checked':null ?> name="type[]" type="checkbox">
                        <span class="label-icon"></span>
                        <span class="label-text">公寓</span>
                    </label>
                    <label class="radio-label">
                        <input value="3" <?= in_array(3,$type) ? 'checked':null ?> name="type[]" type="checkbox">
                        <span class="label-icon"></span>
                        <span class="label-text">商铺</span>
                    </label>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">所在地区</label>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="hidden" name="p_id" value="<?= $model->p_id ?>">
                        <input type="hidden" name="c_id" value="<?= $model->c_id ?>">
                        <input type="hidden" name="d_id" value="<?= $model->d_id ?>">
                        <input class="form-control district-text" name="area"
                               value="<?= $model->area ?>" readonly>
                        <span class="input-group-btn">
                            <a class="btn btn-secondary picker-district" href="javascript:">选择地区</a>
                        </span>
                    </div>
                </div>
            </div>
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
                <div class="form-group-label col-sm-2  text-right">
                    <label class="col-form-label required">项目图片</label>
                </div>
                <div class="col-sm-6">
                    <div class="upload-group multiple short-row">
                        <div class="input-group">
                            <input class="form-control file-input" readonly>
                            <span class="input-group-btn">
                                        <a class="btn btn-secondary upload-file" href="javascript:"
                                           data-toggle="tooltip"
                                           data-placement="bottom" title="上传文件">
                                            <span class="iconfont icon-cloudupload"></span>
                                        </a>
                                    </span>
                            <span class="input-group-btn">
                                        <a class="btn btn-secondary select-file" href="javascript:"
                                           data-toggle="tooltip"
                                           data-placement="bottom" title="从文件库选择">
                                            <span class="iconfont icon-viewmodule"></span>
                                        </a>
                                    </span>
                        </div>
                        <div class="upload-preview-list" id="sortList">
                            <?php
                                $pic_list = json_decode($model->imgs,true);
                                if (count($pic_list) > 0) : ?>
                                <?php foreach ($pic_list as $item) : ?>
                                    <div class="upload-preview text-center" flex="cross:center">
                                        <input type="hidden" class="file-item-input"
                                               name="imgs[]"
                                               value="<?= $item ?>">
                                        <span class="file-item-delete">&times;</span>
                                        <span class="upload-preview-tip">750&times;400</span>
                                        <img class="upload-preview-img" src="<?= $item ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="upload-preview text-center">
                                    <input type="hidden" class="file-item-input" name="imgs[]">
                                    <span class="file-item-delete">&times;</span>
                                    <span class="upload-preview-tip">750&times;400</span>
                                    <img class="upload-preview-img" src="">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">是否热门</label>
                </div>
                 <div class="col-sm-6">
                    <label class="radio-label">
                        <input value="0" <?= $model->is_hot == 0 ? 'checked' : null ?> name="is_hot" type="radio"
                               class="custom-control-input">
                        <span class="label-icon"></span>
                        <span class="label-text">不热门</span>
                    </label>
                    <label class="radio-label">
                        <input value="1" <?= $model->is_hot == 1 ? 'checked' : null ?> name="is_hot" type="radio"
                               class="custom-control-input">
                        <span class="label-icon"></span> 
                        <span class="label-text">热门</span>
                    </label>
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
                    <label class="col-form-label">项目详情</label>
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

<script>
    $(document).on('click', '.picker-district', function () {
        $.districtPicker({
            success: function (res) {
                $('input[name=p_id]').val(res.province_id);
                $('input[name=c_id]').val(res.city_id);
                $('input[name=d_id]').val(res.district_id);
                $('.district-text').val(res.province_name + "-" + res.city_name + "-" + res.district_name);
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
</script>