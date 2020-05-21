<?php
defined('YII_ENV') or exit('Access Denied');

$urlManager = Yii::$app->urlManager;
$this->title = '成员编辑';
?>
<style>
    .member-item {
        margin: 1rem 0;
    }
    .member-item .member-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="auto-form" method="post" return="<?= $urlManager->createUrl(['mch/member/index'])?> ">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">姓名</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="real_name" value="<?= $model->real_name ?>">
                </div>
            </div>
            <div class="form-group row" >
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">电话</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="phone" value="<?= $model->phone ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">银行卡号</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="bank_card" value="<?= $model->bank_card ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">身份证号</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="id_card" value="<?= $model->id_card ?>">
                </div>
            </div>
            <div class="form-group row" id="app">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label ">上级</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="parent_id" v-bind:value="parent_id" hidden>
                    <input class="form-control" data-toggle="modal"
                           data-target="#searchModal"
                           v-bind:value="parent_name" readonly>
                </div>
                <!-- Modal -->
                <div class="modal fade" data-backdrop="static" id="searchModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <b class="modal-title" id="exampleModalLongTitle">查找上级</b>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" id="closeModal">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                    <div class="input-group">
                                        <input class="form-control" placeholder="姓名/电话" name="key_word" id="key_word">
                                        <span class="input-group-btn">
                                                <button class="btn btn-secondary" id="search">搜索</button>
                                        </span>
                                    </div>
                                    <template v-if="member_list">
                                    <template v-if="member_list.length==0">
                                        <div class="p-5 text-center text-muted">搜索结果为空</div>
                                    </template>
                                    <template v-else>
                                        <div v-for="(item,index) in member_list" class="member-item" flex="dir:left">
                                            <div style="width: 20%" class="pr-3">
                                                <div class="member-name">{{item.real_name}}</div>
                                            </div>
                                            <div style="width: 30%" class="pr-3">
                                                <div class="member-name">{{item.phone}}</div>
                                            </div>
                                            <div style="width: 30%" class="pr-3">
                                                <div class="member-name">{{role[item.role]}}</div>
                                            </div>
                                            <div style="width: 20%" class="text-right">
                                                <a v-bind:index="index" href="javascript:" class="insert-parent">添加</a>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="p-5 text-center text-muted">请输入关键字搜索成员</div>
                                </template>
                            </div>
                        </div>
                    </div>
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
                    <label class="col-form-label">身份</label>
                </div>
                 <div class="col-sm-6">
                    <label class="radio-label">
                        <input value="1" <?= $model->role == 1 ? 'checked' : null ?> name="role" type="radio"
                               class="custom-control-input">
                        <span class="label-icon"></span>
                        <span class="label-text">认证会员</span>
                    </label>
                    <label class="radio-label">
                        <input value="2" <?= $model->role == 2 ? 'checked' : null ?> name="role" type="radio"
                               class="custom-control-input">
                        <span class="label-icon"></span> 
                        <span class="label-text">投资保姆</span>
                    </label>
                     <label class="radio-label">
                         <input value="3" <?= $model->role == 3 ? 'checked' : null ?> name="role" type="radio"
                                class="custom-control-input">
                         <span class="label-icon"></span>
                         <span class="label-text">经纪人</span>
                     </label>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">是否城市合伙人</label>
                </div>
                <div class="col-sm-6">
                    <label class="radio-label">
                        <input value="0" <?= $model->is_partner == 0 ? 'checked' : null ?> name="is_partner" type="radio"
                               class="custom-control-input">
                        <span class="label-icon"></span>
                        <span class="label-text">否</span>
                    </label>
                    <label class="radio-label">
                        <input value="1" <?= $model->is_partner == 1 ? 'checked' : null ?> name="is_partner" type="radio"
                               class="custom-control-input">
                        <span class="label-icon"></span>
                        <span class="label-text">是</span>
                    </label>
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

<script>
    var role = [];
    role[0]='无';
    role[1]='认证会员';
    role[3]='经纪人';
    var app = new Vue({
        el: "#app",
        data: {
            role:role,
            member_list: null,
            parent_id: <?=!empty($model->parent_id)?$model->parent_id:0?>,
            parent_name:'<?=!empty($model->parent->real_name)?$model->parent->real_name : '无'?>'
        },
    });

    $(document).on("click", "#search", function () {
        $("#search").text('正在搜索...')
        $.ajax({
            url: '<?= $urlManager->createUrl(['mch/member/search-parent']) ?>',
            dataType: "json",
            data: {
                key_word:$("#key_word").val()
            },
            success: function (res) {
                $("#search").text('搜索')
                if (res.code == 0) {
                    if (res.data.length >0 ){
                        res.data.unshift({id:0,real_name:'无上级',phone:'',role:0});
                    }
                    app.member_list = res.data;

                }
            }
        });
        return false;
    });

    $(document).on("click", ".insert-parent", function () {
        var index = $(this).attr("index");
        var member = app.member_list[index];
        app.parent_id=member.id;
        app.parent_name=member.real_name;
        $("#closeModal").click();
    });

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