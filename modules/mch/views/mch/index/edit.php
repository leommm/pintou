<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/12/28
 * Time: 15:53
 */
$this->title = '入住商管理';
$url_manager = Yii::$app->urlManager;
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
    </div>
    <div class="panel-body">
        <form class="auto-form" method="post"
              return="<?= Yii::$app->request->referrer ? Yii::$app->request->referrer : '' ?>">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right required">
                    <label class="col-form-label required">联系人</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="realname" value="<?= $model->realname ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right required">
                    <label class="col-form-label required">联系电话</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="tel" value="<?= $model->tel ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">微信号</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="wechat_name" value="<?= $model->wechat_name ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">店铺名称</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="name" value="<?= $model->name ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">所在地区</label>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="hidden" name="province_id" value="<?= $model->province_id ?>">
                        <input type="hidden" name="city_id" value="<?= $model->city_id ?>">
                        <input type="hidden" name="district_id" value="<?= $model->district_id ?>">
                        <input class="form-control district-text"
                               value="<?= $province->name ?>-<?= $city->name ?>-<?= $district->name ?>" readonly>
                        <span class="input-group-btn">
                            <a class="btn btn-secondary picker-district" href="javascript:">选择地区</a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">详细地址</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="address" value="<?= $model->address ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">所售类目</label>
                </div>
                <div class="col-sm-6">
                    <select class="form-control" name="mch_common_cat_id">
                        <?php foreach ($mch_common_cat_list as $item) : ?>
                            <option value="<?= $item->id ?>"
                                <?= $item->id == $model->mch_common_cat_id ? 'selected' : null ?>><?= $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">客服电话</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="service_tel" value="<?= $model->service_tel ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">店铺Logo</label>
                </div>
                <div class="col-sm-6">
                    <div class="upload-group">
                        <div class="input-group">
                            <input class="form-control file-input" name="logo" value="<?= $model->logo ?>">
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
                            <span class="upload-preview-tip">100&times;100</span>
                            <img class="upload-preview-img" src="<?= $model->logo ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">店铺背景（顶部）</label>
                </div>
                <div class="col-sm-6">
                    <div class="upload-group">
                        <div class="input-group">
                            <input class="form-control file-input" name="header_bg" value="<?= $model->header_bg ?>">
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
                            <span class="upload-preview-tip">750&times;300</span>
                            <img class="upload-preview-img" src="<?= $model->header_bg ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">手续费(千分之)</label>
                </div>
                <div class="col-sm-6">
                    <input type="number" min="0" max="1000" step="1" class="form-control" name="transfer_rate"
                           value="<?= $model->transfer_rate ?>">
                    <div>商户每笔订单交易金额扣除的手续费，请填写0~1000范围的整数</div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">排序</label>
                </div>
                <div class="col-sm-6">
                    <input type="number" min="0" max="10000000" step="1" class="form-control" name="sort"
                           value="<?= $model->sort ?>">
                    <div>升序，数字越小排的越靠前</div>
                </div>
            </div>
            <?php if ($model->review_status == 0) : ?>
                <div class="form-group row">
                    <div class="form-group-label col-sm-2 text-right">
                        <label class="col-form-label required">审核状态</label>
                    </div>
                    <div class="col-sm-6">
                        <label class="radio-label">
                            <input type="radio" name="review_status" value="1">
                            <span class="label-icon"></span>
                            <span class="label-text">审核通过</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="review_status" value="2">
                            <span class="label-icon"></span>
                            <span class="label-text">审核不通过</span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-group-label col-sm-2 text-right">
                        <label class="col-form-label">审核结果</label>
                    </div>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="review_result"><?= $model->review_result ?></textarea>
                    </div>
                </div>
            <?php else : ?>
                <div class="form-group row">
                    <div class="form-group-label col-sm-2 text-right">
                        <label class="col-form-label required">审核状态</label>
                    </div>
                    <div class="col-sm-6 form-group-text">
                        <?php if ($model->review_status == 0) : ?>
                            <span class="text-muted">待审核</span>
                        <?php elseif ($model->review_status == 1) : ?>
                            <span class="text-success">审核通过</span>
                        <?php elseif ($model->review_status == 2) : ?>
                            <span class="text-danger">审核未通过</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-group-label col-sm-2 text-right">
                        <label class="col-form-label">审核结果</label>
                    </div>
                    <div class="col-sm-6 form-group-text"><?= $model->review_result ?></div>
                </div>
            <?php endif; ?>
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
<script>
    $(document).on('click', '.picker-district', function () {
        $.districtPicker({
            success: function (res) {
                $('input[name=province_id]').val(res.province_id);
                $('input[name=city_id]').val(res.city_id);
                $('input[name=district_id]').val(res.district_id);
                $('.district-text').val(res.province_name + "-" + res.city_name + "-" + res.district_name);
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
</script>