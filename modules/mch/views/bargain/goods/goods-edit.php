<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/29
 * Time: 10:49
 */

$urlManager = Yii::$app->urlManager;
$this->title = '砍价商品编辑';
$staticBaseUrl = Yii::$app->request->baseUrl . '/statics';
$this->params['active_nav_group'] = 10;
$this->params['is_group'] = 1;
$returnUrl = Yii::$app->request->referrer;
if (!$returnUrl) {
    $returnUrl = $urlManager->createUrl(['user/bargain/goods/index']);
}
?>
<script src="<?= $staticBaseUrl ?>/mch/js/uploadVideo.js"></script>
<style>
    .new_int {
        width: 200px;border-radius: 2px;padding: .35rem .7rem;border: 1px solid rgba(0,0,0,.15)
    }

    .qj-span {
        width: 50px;
        background-color: #00CCFF;
        color: white;
        padding: 8px 8px;
        border-radius: 5px;
        font-size: 12px;
    }

    .cat-box {
        border: 1px solid rgba(0, 0, 0, .15);
    }

    .cat-box .row {
        margin: 0;
        padding: 0;
    }

    .cat-box .col-6 {
        padding: 0;
    }

    .cat-box .cat-list {
        border-right: 1px solid rgba(0, 0, 0, .15);
        overflow-x: hidden;
        overflow-y: auto;
        height: 10rem;
    }

    .cat-box .cat-item {
        border-bottom: 1px solid rgba(0, 0, 0, .1);
        padding: .5rem 1rem;
        display: block;
        margin: 0;
    }

    .cat-box .cat-item:last-child {
        border-bottom: none;
    }

    .cat-box .cat-item:hover {
        background: rgba(0, 0, 0, .05);
    }

    .cat-box .cat-item.active {
        background: rgb(2, 117, 216);
        color: #fff;
    }

    .cat-box .cat-item input {
        display: none;
    }

    form {
    }

    form .head {
        position: fixed;
        top: 50px;
        right: 1rem;
        left: calc(240px + 1rem);
        z-index: 9;
        padding-top: 1rem;
        background: #f5f7f9;
        padding-bottom: 1rem;
    }

    form .head .head-content {
        background: #fff;
        border: 1px solid #eee;
        height: 40px;
    }

    .head-step {
        height: 100%;
        padding: 0 20px;
    }

    .step-block {
        position: relative;
    }

    form .body {
        padding-top: 45px;
    }

    .step-block > div {
        padding: 20px;
        background: #fff;
        border: 1px solid #eee;
        margin-bottom: 5px;
    }

    .step-block > div:first-child {
        padding: 20px;
        width: 120px;
        margin-right: 5px;
        font-weight: bold;
        text-align: center;
    }

    .step-block .step-location {
        position: absolute;
        top: -122px;
        left: 0;
    }

    .step-block:first-child .step-location {
        top: -140px;
    }

    form .foot {
        text-align: center;
        background: #fff;
        border: 1px solid #eee;
        padding: 1rem;
    }

    .edui-editor,
    #edui1_toolbarbox {
        z-index: 2 !important;
    }

    form .short-row {
        width: 380px;
    }

    .form {
        background: none;
        width: 100%;
        max-width: 100%;
    }

    .attr-group {
        border: 1px solid #eee;
        padding: .5rem .75rem;
        margin-bottom: .5rem;
        border-radius: .15rem;
    }

    .attr-group-delete {
        display: inline-block;
        background: #eee;
        color: #fff;
        width: 1rem;
        height: 1rem;
        text-align: center;
        line-height: 1rem;
        border-radius: 999px;
    }

    .attr-group-delete:hover {
        background: #ff4544;
        color: #fff;
        text-decoration: none;
    }

    .attr-list > div {
        vertical-align: top;
    }

    .attr-item {
        display: inline-block;
        background: #eee;
        margin-right: 1rem;
        margin-top: .5rem;
        overflow: hidden;
    }

    .attr-item .attr-name {
        padding: .15rem .75rem;
        display: inline-block;
    }

    .attr-item .attr-delete {
        padding: .35rem .75rem;
        background: #d4cece;
        color: #fff;
        font-size: 1rem;
        font-weight: bold;
    }

    .attr-item .attr-delete:hover {
        text-decoration: none;
        color: #fff;
        background: #ff4544;
    }

    .panel {
        margin-top: calc(40px + 1rem);
    }

    form .form-group .col-3 {
        -webkit-box-flex: 0;
        -webkit-flex: 0 0 160px;
        -ms-flex: 0 0 160px;
        flex: 0 0 160px;
        max-width: 160px;
        width: 160px;
    }
</style>
<div class="panel mb-3" id="page">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">

        <form class="form auto-form" method="post" autocomplete="off" data-return="<?= $returnUrl ?>">
            <div class="head">
                <div class="head-content" flex="dir:left">
                    <a flex="cross:center" class="head-step" href="#step1">选择分类</a>
                    <a flex="cross:center" class="head-step" href="#step2">基本信息</a>
                    <a flex="cross:center" class="head-step" href="#step4">商品详情</a>
                </div>
            </div>
            <div class="step-block" flex="dir:left box:first">
                <div>
                    <span>选择分类</span>
                    <span class="step-location" id="step1"></span>
                </div>
                <div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">商品分类</label>
                        </div>
                        <div class="col-9">
                            <div class="input-group short-row">
                                <select class="form-control parent" name="model[cat_id]">
                                    <option value="">请选择分类</option>
                                    <?php foreach ($cat as $value) : ?>
                                        <option
                                            value="<?= $value['id'] ?>" <?= $value['id'] == $goods['cat_id'] ? 'selected' : '' ?>><?= $value['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" hidden>
                        <div class="col-3 text-right">
                            <label class=" col-form-label">淘宝一键采集</label>
                        </div>
                        <div class="col-9">
                            <div class="input-group short-row">
                                <input class="form-control copy-url" placeholder="请输入淘宝商品详情地址连接">
                                <span class="input-group-btn">
                                    <a class="btn btn-secondary copy-btn" href="javascript:">立即获取</a>
                                </span>
                            </div>
                            <div class="short-row text-muted fs-sm">
                                例如：商品链接为:http://item.taobao.com/item.htm?id=522155891308
                                或:http://detail.tmall.com/item.htm?id=522155891308
                            </div>
                            <div class="short-row text-muted fs-sm">若不使用，则该项为空</div>
                            <div class="copy-error text-danger fs-sm" hidden></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label">京东一键采集</label>
                        </div>
                        <div class="col-9">
                            <div class="input-group short-row">
                                <input class="form-control copy-url" placeholder="请输入京东商品详情地址连接">
                                <span class="input-group-btn">
                                    <a class="btn btn-secondary copy-btn" href="javascript:">立即获取</a>
                                </span>
                            </div>
                            <div class="short-row text-muted fs-sm">
                                例如：商品链接为:https://item.jd.com/5346660.html
                            </div>
                            <div class="short-row text-muted fs-sm">若不使用，则该项为空</div>
                            <div class="copy-error text-danger fs-sm" hidden></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label">商城商品拉取</label>
                        </div>
                        <div class="col-9">
                            <div class="input-group short-row">
                                <input class="form-control copy-mall-id" name="mall_id" type="number"
                                       placeholder="请输入商城商品ID">
                                <span class="input-group-btn">
                                    <a class="btn btn-secondary mall-copy-btn" href="javascript:">立即获取</a>
                                </span>
                            </div>
                            <div class="short-row text-muted fs-sm">若不使用，则该项为空</div>
                            <div class="copy-error text-danger fs-sm" hidden></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-block" flex="dir:left box:first">
                <div>
                    <span>基本信息</span>
                    <span class="step-location" id="step2"></span>
                </div>
                <div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">商品名称</label>
                        </div>
                        <div class="col-9">
                            <input class="form-control short-row" type="text" name="model[name]"
                                   value="<?= $goods['name'] ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label">单位</label>
                        </div>
                        <div class="col-9">
                            <input class="form-control short-row" type="text" name="model[unit]"
                                   value="<?= $goods['unit'] ? $goods['unit'] : '件' ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label">商品排序</label>
                        </div>
                        <div class="col-9">
                            <input class="form-control short-row" type="text" name="model[sort]"
                                   value="<?= $goods['sort'] ?: 100 ?>">
                            <div class="text-muted fs-sm">排序按升序排列</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label">虚拟销量</label>
                        </div>
                        <div class="col-9">
                            <input class="form-control short-row" type="number" name="model[virtual_sales]"
                                   value="<?= $goods['virtual_sales'] ?>" min="0">
                            <div class="text-muted fs-sm">前端展示的销量=实际销量+虚拟销量</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">商品库存</label>
                        </div>
                        <div class="col-9">
                            <div class="input-group short-row">
                                <input class="form-control" name="model[goods_num]"
                                       value="<?= $goods['goods_num'] ?>">
                                <span class="input-group-addon">件</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label">重量</label>
                        </div>
                        <div class="col-9">
                            <div class="input-group short-row">
                                <input type="number" step="0.01" class="form-control"
                                       name="model[weight]"
                                       value="<?= $goods['weight'] ? $goods['weight'] : 0 ?>">
                                <span class="input-group-addon">克<span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class="col-form-label required">商品缩略图</label>
                        </div>
                        <div class="col-9">
                            <div class="upload-group short-row">
                                <div class="input-group">
                                    <input class="form-control file-input" name="model[cover_pic]"
                                           value="<?= $goods['cover_pic'] ?>">
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
                                    <span class="upload-preview-tip">325&times;325</span>
                                    <img class="upload-preview-img" src="<?= $goods['cover_pic'] ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class="col-form-label">商品图片</label>
                        </div>
                        <div class="col-9">
                            <?php if ($goods->goodsPicList()) :
                                foreach ($goods->goodsPicList() as $goods_pic) : ?>
                                                                    <?php $goods_pic_list[] = $goods_pic->pic_url ?>
                                <?php endforeach;
                            else :
                                $goods_pic_list = [];
                            endif; ?>
                            <div class="upload-group multiple short-row">
                                <div class="input-group">
                                    <input class="form-control file-input" readonly>
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
                                </div>
                                <div class="upload-preview-list">
                                    <?php if (count($goods_pic_list) > 0) : ?>
                                        <?php foreach ($goods_pic_list as $item) : ?>
                                            <div class="upload-preview text-center">
                                                <input type="hidden" class="file-item-input"
                                                       name="model[goods_pic_list][]"
                                                       value="<?= $item ?>">
                                                <span class="file-item-delete">&times;</span>
                                                <span class="upload-preview-tip">750&times;750</span>
                                                <img class="upload-preview-img" src="<?= $item ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <div class="upload-preview text-center">
                                            <input type="hidden" class="file-item-input" name="model[goods_pic_list][]">
                                            <span class="file-item-delete">&times;</span>
                                            <span class="upload-preview-tip">750&times;750</span>
                                            <img class="upload-preview-img" src="">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">商品标价</label>
                        </div>
                        <div class="col-9">
                            <input type="number" step="0.01" class="form-control short-row"
                                   name="model[original_price]" min="0"
                                   value="<?= $goods['original_price'] ? $goods['original_price'] : 1 ?>">
                            <div class="fs-sm text-muted">元</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">设置低价</label>
                        </div>
                        <div class="col-9">
                            <div class="input-group short-row">
                                <input type="number" step="0.01" class="form-control"
                                       name="model[price]" min="0.01"
                                       value="<?= $goods['price'] ? $goods['price'] : 1 ?>">
                                <span class="input-group-addon">元</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">显示低价</label>
                        </div>
                        <div class="col-9 col-form-label">
                            <label class="radio-label">
                                <input <?= $goods['is_show_price'] == 1 ? 'checked' : null ?>
                                        value="1" name="model[is_show_price]" type="radio" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">显示</span>
                            </label>
                            <label class="radio-label">
                                <input <?= $goods['is_show_price'] == 0 ? 'checked' : null ?>
                                        value="0" name="model[is_show_price]" type="radio" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">不显示</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">没到低价</label>
                        </div>
                        <div class="col-9 col-form-label">
                            <label class="radio-label">
                                <input <?= $goods['is_al_order'] == 0 ? 'checked' : null ?>
                                        value="0" name="model[is_al_order]" type="radio" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">可以下单</span>
                            </label>
                            <label class="radio-label">
                                <input <?= $goods['is_al_order'] == 1 ? 'checked' : null ?>
                                        value="1" name="model[is_al_order]" type="radio" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">不可下单</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">自己砍价</label>
                        </div>
                        <div class="col-9 col-form-label">
                            <label class="radio-label">
                                <input <?= $goods['is_me_br'] == 0 ? 'checked' : null ?>
                                        value="0" name="model[is_me_br]" type="radio" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">允许</span>
                            </label>
                            <label class="radio-label">
                                <input <?= $goods['is_me_br'] == 1 ? 'checked' : null ?>
                                        value="1" name="model[is_me_br]" type="radio" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">禁止</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">活动时间</label>
                        </div>
                        <div class="col-9">
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="fav_limit" value="<?= $goods['date_start'] ? date('Y-m-d', $goods['date_start']) : '' ?> - <?= $goods['date_end'] ? date('Y-m-d', $goods['date_end']) : '' ?>" id="test6" placeholder=" - ">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">砍价限时</label>
                        </div>
                        <div class="col-9">
                            <input type="text" class="form-control short-row"
                                   name="model[limit_time]"
                                   id="limit_time"
                                   value="<?= $goods['limit_time'] ? $goods['limit_time'] : 0 ?>">
                            <div class="fs-sm text-muted">砍价限时为空则为不参与限时</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">可砍价总次数</label>
                        </div>
                        <div class="col-9">
                            <input type="number" step="1" class="form-control short-row"
                                   name="model[total_num]" min="2"
                                   value="<?= $goods['total_num'] ? $goods['total_num'] : 2 ?>">
                            <div class="fs-sm text-muted"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">每人可砍次数</label>
                        </div>
                        <div class="col-9">
                            <input type="number" step="1" class="form-control short-row"
                                   name="model[buy_limit]" min="2"
                                   value="<?= $goods['buy_limit'] ? $goods['buy_limit'] : 0 ?>">
                            <div class="fs-sm text-muted"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">活动发起次数</label>
                        </div>
                        <div class="col-9">
                            <input type="number" step="1" class="form-control short-row"
                                   name="model[fav_num]" min="2"
                                   value="<?= $goods['fav_num'] ? $goods['fav_num'] : 0 ?>">
                            <div class="fs-sm text-muted"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                        </div>
                        <div class="col-9">
                            <span style="font-size: 12px;color: red;">最大增加金额用正数表示，例如：6，最大减少金额用负数表示，例如：-6，概率相加必须等于100%</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">每次砍价概率</label>
                        </div>
                        <div class="col-9">
                            <input type="text" name='bot_amount[]' value="<?php if (count($goods['qj_content']) > 0) : ?><?= $goods['qj_content'][0]['bot_amount'] ?><?php endif; ?>" class="new_int">
                            元 至
                            <input type="text" name='top_amount[]' value="<?php if (count($goods['qj_content']) > 0) : ?><?= $goods['qj_content'][0]['top_amount'] ?><?php endif; ?>" class="new_int">
                            元 概率
                            <input type="text" name='br_lv[]' value="<?php if (count($goods['qj_content']) > 0) : ?><?= $goods['qj_content'][0]['br_lv'] ?><?php endif; ?>" class="new_int">
                            %
                            <div class="fs-sm text-muted"></div>
                        </div>
                    </div>

                    <div id="qj-div">
						<?php if (count($goods['qj_content']) > 1) : ?>
							<?php foreach ($goods['qj_content'] as $key => $item) : ?>
                                <?php if ($key >= 1) : ?>
                                <div class="form-group row">
                                    <div class="col-3 text-right">
                                        <label class=" col-form-label"></label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" name='bot_amount[]' value="<?= $item['bot_amount'] ?>" class="new_int">
                                        元 至
                                        <input type="text" name='top_amount[]' value="<?= $item['top_amount'] ?>" class="new_int">
                                        元 概率
                                        <input type="text" name='br_lv[]' value="<?= $item['br_lv'] ?>" class="new_int">
                                        %
                                        <div class="fs-sm text-muted"></div>
                                    </div>
                                </div>
                                <?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label"></label>
                        </div>
                        <div class="col-9">
                            <span class="qj-span" id="qj-put">添加区间</span>
                            <span class="qj-span" id="qj-get">删除区间</span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="step-block" flex="dir:left box:first">
                <div>
                    <span>营销管理</span>
                    <span class="step-location" id="step4"></span>
                </div>
                <div>

                    <div class="form-group row">
                        <div class="form-group-label col-3 text-right">
                            <label class="col-form-label">支付方式</label>
                        </div>
                        <?php $payment = json_decode($goods['payment'], true);?>
                        <div class="col-9">
                            <label class="checkbox-label">
                                <input <?= $payment['wechat'] == 1 ? 'checked' : null ?>
                                    value="1"
                                    name="model[payment][wechat]" type="checkbox" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">微信支付</span>
                            </label>
                            <label class="checkbox-label">
                                <input <?= $payment['balance'] == 1 ? 'checked' : null ?>
                                    value="1"
                                    name="model[payment][balance]" type="checkbox" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">余额支付</span>
                            </label>
                            <div class="fs-sm text-danger">若都不勾选，则视为与商城支付方式一致</div>
                            <div class="fs-sm">可在“<a target="_blank" href="<?=$urlManager->createUrl(['mch/recharge/setting'])?>">营销管理=>充值=>设置</a>”中开启余额功能</div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="step-block" flex="dir:left box:first">
                <div>
                    <span>分销设置</span>
                    <span class="step-location" id="step5"></span>
                </div>
                <div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">开启单独分销设置</label>
                        </div>
                        <div class="col-9 col-form-label">
                            <label class="radio-label">
                                <input <?= $goods_share['individual_share'] == 0 ? 'checked' : null ?>
                                    value="0" name="model[individual_share]" type="radio" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">不开启</span>
                            </label>
                            <label class="radio-label">
                                <input <?= $goods_share['individual_share'] == 1 ? 'checked' : null ?>
                                    value="1" name="model[individual_share]" type="radio" class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">开启</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row share-commission">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">分销佣金类型</label>
                        </div>
                        <div class="col-9 col-form-label">
                            <label class="radio-label share-type">
                                <input <?= $goods_share['share_type'] == 0 ? 'checked' : null ?>
                                    name="model[share_type]"
                                    value="0"
                                    type="radio"
                                    class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">百分比</span>
                            </label>
                            <label class="radio-label share-type">
                                <input <?= $goods_share['share_type'] == 1 ? 'checked' : null ?>
                                    name="model[share_type]"
                                    value="1"
                                    type="radio"
                                    class="custom-control-input">
                                <span class="label-icon"></span>
                                <span class="label-text">固定金额</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row share-commission">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">单独分销设置</label>
                        </div>
                        <div class="col-9">
                            <div class="short-row">
                                <div class="input-group mb-3">
                                    <span class="input-group-addon">一级佣金</span>
                                    <input name="model[share_commission_first]"
                                           value="<?= $goods_share['share_commission_first'] ?>"
                                           class="form-control"
                                           type="number"
                                           step="0.01"
                                           min="0" max="100">
                                    <span
                                        class="input-group-addon percent"><?= $goods_share['share_type'] == 1 ? "元" : "%" ?></span>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-addon">二级佣金</span>
                                    <input name="model[share_commission_second]"
                                           value="<?= $goods_share['share_commission_second'] ?>"
                                           class="form-control"
                                           type="number"
                                           step="0.01"
                                           min="0" max="100">
                                    <span
                                        class="input-group-addon percent"><?= $goods_share['share_type'] == 1 ? "元" : "%" ?></span>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-addon">三级佣金</span>
                                    <input name="model[share_commission_third]"
                                           value="<?= $goods_share['share_commission_third'] ?>"
                                           class="form-control"
                                           type="number"
                                           step="0.01"
                                           min="0" max="100">
                                    <span
                                        class="input-group-addon percent"><?= $goods_share['share_type'] == 1 ? "元" : "%" ?></span>
                                </div>
                                <div class="fs-sm">
                                    <a href="<?= $urlManager->createUrl(['mch/share/basic']) ?>"
                                       target="_blank">分销层级</a>的优先级高于商品单独的分销比例，例：层级只开二级分销，那商品的单独分销比例只有二级有效
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-block" flex="dir:left box:first">
                <div>
                    <span>图文详情</span>
                    <span class="step-location" id="step4"></span>
                </div>
                <div>
                    <div class="form-group row">
                        <div class="col-3 text-right">
                            <label class=" col-form-label required">图文详情</label>
                        </div>
                        <div class="col-9">
                            <textarea class="short-row" id="editor"
                                      name="model[detail]"><?= $goods['detail'] ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div flex="dir:left box:first">
                <div class="form-group row">
                    <div class="col-3"></div>
                    <div class="col-9">
                        <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <!-- 选择分类 -->
    <div class="modal fade" id="catModal" tabindex="-1" role="dialog" aria-labelledby="catModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <b>选择分类</b>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="cat-box">
                        <div class="row">
                            <div class="col-6">
                                <div class="cat-list parent-cat-list">
                                    <?php if (is_array($cat_list)) :
                                        foreach ($cat_list as $index => $cat) : ?>
                                        <label class="cat-item <?= $index == 0 ? 'active' : '' ?>">
                                            <?= $cat->name ?>
                                            <input value="<?= $cat->id ?>"
                                                <?= $index == 0 ? 'checked' : '' ?>
                                                   type="radio"
                                                   name="model[cat_id]">
                                        </label>
                                        <?php endforeach;
                                    endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="cat-list">
                                    <label class="cat-item" v-for="sub_cat in sub_cat_list">
                                        {{sub_cat.name}}
                                        <input v-bind:value="sub_cat.id" type="radio" name="model[cat_id]">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary cat-confirm">确认</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= Yii::$app->request->baseUrl ?>/statics/ueditor/ueditor.config.js"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/statics/ueditor/ueditor.all.min.js"></script>
<script>
    var Map = function () {
        this._data = [];
        this.set = function (key, val) {
            for (var i in this._data) {
                if (this._data[i].key == key) {
                    this._data[i].val = val;
                    return true;
                }
            }
            this._data.push({
                key: key,
                val: val,
            });
            return true;
        };
        this.get = function (key) {
            for (var i in this._data) {
                if (this._data[i].key == key)
                    return this._data[i].val;
            }
            return null;
        };
        this.delete = function (key) {
            for (var i in this._data) {
                if (this._data[i].key == key) {
                    this._data.splice(i, 1);
                }
            }
            return true;
        };
    };
    var map = new Map();

    var page = new Vue({
        el: "#page",
        data: {
            sub_cat_list: [],
//            attr_group_list: [],
//            check_attr_list: [],
            attr_group_list: JSON.parse('<?=json_encode($goods->getAttrData(), JSON_UNESCAPED_UNICODE)?>'),//可选规格数据
            checked_attr_list: JSON.parse('<?=json_encode($goods->getCheckedAttrData(), JSON_UNESCAPED_UNICODE)?>'),//已选规格数据
        },
        methods:{
            change: function (item, index) {
                this.checked_attr_list[index] = item;
            }
        }
    });

    var ue = UE.getEditor('editor', {
        serverUrl: "<?=$urlManager->createUrl(['upload/ue'])?>",
        enableAutoSave: false,
        saveInterval: 1000 * 3600,
        enableContextMenu: false,
        autoHeightEnabled: false,
    });
    $(document).on("change", ".cat-item input", function () {
        if ($(this).prop("checked")) {
            $(".cat-item").removeClass("active");
            $(this).parent(".cat-item").addClass("active");
        } else {
            $(this).parent(".cat-item").removeClass("active");
        }
    });

    $(document).on("change", ".parent-cat-list input", function () {
        getSubCatList();
    });

    $(document).on("click", ".cat-confirm", function () {
        var cat_name = $.trim($(".cat-item.active").text());
        var cat_id = $(".cat-item.active input").val();
        if (cat_name && cat_id) {
            $(".cat-name").val(cat_name);
            $(".cat-id").val(cat_id);
        }
        $("#catModal").modal("hide");
    });

    function getSubCatList() {
        var parent_id = $(".parent-cat-list input:checked").val();
        page.sub_cat_list = [];
        $.ajax({
            url: "<?=$urlManager->createUrl(['user/goods/get-cat-list'])?>",
            data: {
                parent_id: parent_id,
            },
            success: function (res) {
                if (res.code == 0) {
                    page.sub_cat_list = res.data;
                }
            }
        });
    }

    getSubCatList();


    $(document).on("change", ".attr-select", function () {
        var name = $(this).attr("data-name");
        var id = $(this).val();
        if ($(this).prop("checked")) {
        } else {
        }
    });

    $(document).on("click", ".add-attr-group-btn", function () {
        var name = $(".add-attr-group-input").val();
        name = $.trim(name);
        if (name == "")
            return;
        page.attr_group_list.push({
            attr_group_name: name,
            attr_list: [],
        });
        $(".add-attr-group-input").val("");
        page.checked_attr_list = getAttrList();
    });

    $(document).on("click", ".add-attr-btn", function () {
        var name = $(this).parents(".attr-input-group").find(".add-attr-input").val();
        var index = $(this).attr("index");
        name = $.trim(name);
        if (name == "")
            return;
        page.attr_group_list[index].attr_list.push({
            attr_name: name,
        });
        $(this).parents(".attr-input-group").find(".add-attr-input").val("");
        page.checked_attr_list = getAttrList();
    });


    $(document).on("click", ".attr-group-delete", function () {
        var index = $(this).attr("index");
        page.attr_group_list.splice(index, 1);
        page.checked_attr_list = getAttrList();
    });

    $(document).on("click", ".attr-delete", function () {
        var index = $(this).attr("index");
        var group_index = $(this).attr("group-index");
        page.attr_group_list[group_index].attr_list.splice(index, 1);
        page.checked_attr_list = getAttrList();
    });


    function getAttrList() {
        var array = [];
        for (var i in page.attr_group_list) {
            for (var j in page.attr_group_list[i].attr_list) {
                var object = {
                    attr_group_name: page.attr_group_list[i].attr_group_name,
                    attr_id: null,
                    attr_name: page.attr_group_list[i].attr_list[j].attr_name,
                };
                if (!array[i])
                    array[i] = [];
                array[i].push(object);
            }
        }
        var len = array.length;
        var results = [];
        var indexs = {};

        function specialSort(start) {
            start++;
            if (start > len - 1) {
                return;
            }
            if (!indexs[start]) {
                indexs[start] = 0;
            }
            if (!(array[start] instanceof Array)) {
                array[start] = [array[start]];
            }
            for (indexs[start] = 0; indexs[start] < array[start].length; indexs[start]++) {
                specialSort(start);
                if (start == len - 1) {
                    var temp = [];
                    for (var i = len - 1; i >= 0; i--) {
                        if (!(array[start - i] instanceof Array)) {
                            array[start - i] = [array[start - i]];
                        }
                        if (array[start - i][indexs[start - i]]) {
                            temp.push(array[start - i][indexs[start - i]]);
                        }
                    }
                    var key = [];
                    for (var i in temp) {
                        key.push(temp[i].attr_id);
                    }
                    var oldVal = map.get(key.sort().toString());
                    if (oldVal) {
                        results.push({
                            num: oldVal.num,
                            price: oldVal.price,
                            single: oldVal.single,
                            no: oldVal.no,
                            pic: oldVal.pic,
                            attr_list: temp
                        });
                    } else {
                        results.push({
                            num: 0,
                            price: 0,
                            single: 0,
                            no: '',
                            pic: '',
                            attr_list: temp
                        });
                    }
                }
            }
        }

        specialSort(-1);
        return results;
    }


    $(document).on("change", "input[name='model[individual_share]']", function () {
        setShareCommission();
    });
    setShareCommission();

    function setShareCommission() {
        if ($("input[name='model[individual_share]']:checked").val() == 1) {
            $(".share-commission").show();
        } else {
            $(".share-commission").hide();
        }
    }
    //分销佣金选择
    $(document).on('click', '.share-type', function () {
        var price_type = $(this).children('input');
        if ($(price_type).val() == 1) {
            $('.percent').html('元');
        } else {
            $('.percent').html('%');
        }
    })

    function checkUseAttr() {
        if ($('.use-attr').length == 0)
            return;
        if ($('.use-attr').prop('checked')) {
            $('input[name="model[goods_num]"]').val(0).prop('readonly', true);
            $('input[name="model[goods_no]"]').val(0).prop('readonly', true);
            $('.attr-edit-block').show();
        } else {
            $('input[name="model[goods_num]"]').prop('readonly', false);
            $('input[name="model[goods_no]"]').prop('readonly', false);
            $('.attr-edit-block').hide();
        }
    }

    $(document).on('change', '.use-attr', function () {
        checkUseAttr();
    });

    checkUseAttr();

</script>
<script>
    $(document).on('change', '.video', function () {
        $('.video-check').attr('href', this.value);
    });
	layui.use('laydate', function() {
		var laydate = layui.laydate;
		laydate.render({
			elem: '#test6'
			, range: true
		});
		//日期时间选择器
		// laydate.render({
		// 	elem: '#limit_time'
		// 	, type: 'datetime'
		// });
	})
</script>
<script>
    $(document).on('click', '.copy-btn', function () {
//        var url = $('.copy-url').val();
//        var btn = $(this);
//        var error = $('.copy-error');

        var btn = $(this);
        var url = $(btn.parent().prev()[0]).val();
        var error = $('.copy-error');

        error.prop('hidden', true);
        if (url == '' || url == undefined) {
            error.prop('hidden', false).html('请填写宝贝链接');
            return;
        }
        btn.btnLoading('信息获取中');
        $.myLoading();
        $.ajax({
            url: "<?=$urlManager->createUrl(['user/goods/copy'])?>",
            type: 'get',
            dataType: 'json',
            data: {
                url: url,
            },
            success: function (res) {
                $.myLoadingHide();
                btn.btnReset();
                if (res.code == 0) {
                    $("input[name='model[name]']").val(res.data.title);
                    $("input[name='model[virtual_sales]']").val(res.data.sale_count);
                    $("input[name='model[price]']").val(res.data.sale_price);
                    $("input[name='model[original_price]']").val(res.data.price);
                    console.log(res.data.attr_group_list);
                    page.attr_group_list = res.data.attr_group_list;
                    page.checked_attr_list = res.data.checked_attr_list;
                    ue.setContent(res.data.detail_info + "");
                    var pic = res.data.picsPath;

                    if (pic) {
                        var cover_pic = $("input[name='model[cover_pic]']");
                        var cover_pic_next = $(cover_pic.parent().next('.upload-preview')[0]).children('.upload-preview-img');
                        cover_pic.val(pic[0]);
                        $(cover_pic_next).prop('src', pic[0]);
                    }
                    if (pic.length > 1) {
                        var goods_pic_list = $(".upload-preview-list");
                        goods_pic_list.empty();
                        $(pic).each(function (i) {
                            if (i == 0) {
                                return true;
                            }
                            var goods_pic = ' <div class="upload-preview text-center">' +
                                '<input type="hidden" class="file-item-input" name="model[goods_pic_list][]" value="' + pic[i] + '"> ' +
                                '<span class="file-item-delete">&times;</span> <span class="upload-preview-tip">750&times;750</span> ' +
                                '<img class="upload-preview-img" src="' + pic[i] + '"> ' +
                                '</div>';
                            goods_pic_list.append(goods_pic);
                        });
                    }


                } else {
                    error.prop('hidden', false).html(res.msg);
                }
            }
        });
    });
    $(document).on('click', '.mall-copy-btn', function () {
        var mall_id = $('.copy-mall-id').val();
        var btn = $(this);
        var error = $('.copy-error');
        error.prop('hidden', true);
        if (mall_id == '' || mall_id == undefined) {
            error.prop('hidden', false).html('请填写商城商品ID');
            return;
        }
        btn.btnLoading('信息获取中');
        $.myLoading();
        $.ajax({
            url: "<?=$urlManager->createUrl(['mch/group/goods/copy'])?>",
            type: 'get',
            dataType: 'json',
            data: {
                mall_id: mall_id,
            },
            success: function (res) {
                $('.no-mall-get').hide();
                $('.mall-get').show();
                $.myLoadingHide();
                btn.btnReset();
                if (res.code == 0) {
                    $("input[name='model[name]']").val(res.data.name);
                    $("input[name='model[virtual_sales]']").val(res.data.virtual_sales);
                    $("input[name='model[price]']").val(res.data.price);
                    $("input[name='model[original_price]']").val(res.data.original_price);
//                    $("input[name='model[cover_pic]']").val(res.data.cover_pic);
                    $("input[name='model[unit]']").val(res.data.unit);
                    $("input[name='model[weight]']").val(res.data.weight);
                    $("input[name='model[service]']").val(res.data.service);
                    $("input[name='model[sort]']").val(res.data.sort);
//                    $("#editor").val(res.data.detail);
                    console.log(JSON.parse(res.data.attr_group_list));
                    console.log(JSON.parse(res.data.checked_attr_list));
                    page.attr_group_list = JSON.parse(res.data.attr_group_list);
                    page.checked_attr_list = JSON.parse(res.data.checked_attr_list);
                    ue.setContent(res.data.detail + "");
                    var pic = res.data.pic;

                    if (res.data.use_attr ==1){
                        $('.use-attr').prop('checked',true);
                        $('input[name="model[goods_num]"]').val(0).prop('readonly', true);
                        $('.attr-edit-block').show();
                    }

                    if (pic) {
                        var cover_pic = $("input[name='model[cover_pic]']");
                        var cover_pic_next = $(cover_pic.parent().next('.upload-preview')[0]).children('.upload-preview-img');
                        cover_pic.val(res.data.cover_pic);
                        $(cover_pic_next).prop('src', res.data.cover_pic);
                    }
                    console.log(pic);
                    if (pic.length >= 1) {
                        var goods_pic_list = $(".upload-preview-list");
                        goods_pic_list.empty();
                        $(pic).each(function (i) {
//                            if (i == 0) {
//                                return true;
//                            }
                            var goods_pic = ' <div class="upload-preview text-center">' +
                                '<input type="hidden" class="file-item-input" name="model[goods_pic_list][]" value="' + pic[i] + '"> ' +
                                '<span class="file-item-delete">&times;</span> <span class="upload-preview-tip">750&times;750</span> ' +
                                '<img class="upload-preview-img" src="' + pic[i] + '"> ' +
                                '</div>';
                            goods_pic_list.append(goods_pic);
                        });
                    }

                } else {
                    error.prop('hidden', false).html(res.msg);
                }
            }
        });
    });
</script>

<!-- 规格图片 -->
<script>
    $(document).on('click', '.upload-attr-pic', function () {
        var btn = $(this);
        var input = btn.parents('.input-group').find('.form-control');
        var index = btn.parents('.input-group').attr('data-index');
        $.upload_file({
            accept: 'image/*',
            start: function (res) {
                btn.btnLoading('');
            },
            success: function (res) {
                input.val(res.data.url).trigger('change');
                page.checked_attr_list[index].pic = res.data.url;
            },
            complete: function (res) {
                btn.btnReset();
            },
        });
    });
    $(document).on('click', '.select-attr-pic', function () {
        var btn = $(this);
        var input = btn.parents('.input-group').find('.form-control');
        var index = btn.parents('.input-group').attr('data-index');
        $.select_file({
            success: function (res) {
                input.val(res.url).trigger('change');
                page.checked_attr_list[index].pic = res.url;
            }
        });
    });
    $(document).on('click', '.delete-attr-pic', function () {
        var btn = $(this);
        var input = btn.parents('.input-group').find('.form-control');
        var index = btn.parents('.input-group').attr('data-index');
        input.val('').trigger('change');
        page.checked_attr_list[index].pic = '';
    });
</script>
<script>
    $(function () {
		$("#qj-put").click(function () {
			var html = "<div class=\"form-group row\">\n" +
				"    <div class=\"col-3 text-right\">\n" +
				"        <label class=\" col-form-label\"></label>\n" +
				"    </div>\n" +
				"    <div class=\"col-9\">\n" +
				"        <input type=\"text\" name='bot_amount[]' class=\"new_int\">\n" +
				"        元 至\n" +
				"        <input type=\"text\" name='top_amount[]' class=\"new_int\">\n" +
				"        元 概率\n" +
				"        <input type=\"text\" name='br_lv[]' class=\"new_int\">\n" +
				"        %\n" +
				"        <div class=\"fs-sm text-muted\"></div>\n" +
				"    </div>\n" +
				"</div>"

            $("#qj-div").append(html)

		})

		$("#qj-get").click(function () {
			var len = $("#qj-div").children().length
            $("#qj-div").children().eq(len - 1).remove()
		})
	})
</script>