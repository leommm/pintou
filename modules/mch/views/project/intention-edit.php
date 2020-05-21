<?php
defined('YII_ENV') or exit('Access Denied');

$urlManager = Yii::$app->urlManager;
$this->title = '意向编辑';
$type = explode(',',$model->type);
?>
<style xmlns:v-bind="http://www.w3.org/1999/xhtml">
    .member-item {
        margin: 1rem 0;
    }
    .member-item .member-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="panel mb-3" id="app">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="auto-form" method="post" return="<?= $urlManager->createUrl(['mch/project/intention'])?> ">
            <div class="form-group row" >
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">会员</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="member_id" v-bind:value="member_id" hidden>
                    <input class="form-control" data-toggle="modal" name="real_name"
                           data-target="#searchMemberModal"
                           v-bind:value="real_name" readonly>
                </div>
                <!-- Modal -->
                <div class="modal fade" data-backdrop="static" id="searchMemberModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <b class="modal-title" id="exampleModalLongTitle">查找会员</b>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" id="closeMemberModal">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="input-group">
                                    <input class="form-control" placeholder="姓名/电话" name="key_word" id="key_word">
                                    <span class="input-group-btn">
                                                <button class="btn btn-secondary" id="searchMember">搜索</button>
                                        </span>
                                </div>
                                <template v-if="member_list">
                                    <template v-if="member_list.length==0">
                                        <div class="p-5 text-center text-muted">搜索结果为空</div>
                                    </template>
                                    <template v-else>
                                        <div v-for="(item,index) in member_list" class="member-item" flex="dir:left">
                                            <div style="width: 30%" class="pr-3">
                                                <div class="member-name">{{item.real_name}}</div>
                                            </div>
                                            <div style="width: 30%" class="pr-3">
                                                <div class="member-name">{{item.phone}}</div>
                                            </div>
                                            <div style="width: 30%" class="text-right">
                                                <a v-bind:index="index" href="javascript:" class="insert-member">添加</a>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="p-5 text-center text-muted">请输入关键字搜索会员</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">项目</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="project_id" v-bind:value="project_id" hidden>
                    <input class="form-control" data-toggle="modal"
                           data-target="#searchProjectModal"
                           v-bind:value="project_title" readonly>
                </div>
                <!-- Modal -->
                <div class="modal fade" data-backdrop="static" id="searchProjectModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <b class="modal-title" id="exampleModalLongTitle">查找项目</b>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" id="closeProjectModal">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="input-group">
                                    <input class="form-control" placeholder="标题/副标题"  id="key_word1">
                                    <span class="input-group-btn">
                                                <button class="btn btn-secondary" id="searchProject">搜索</button>
                                        </span>
                                </div>
                                <template v-if="project_list">
                                    <template v-if="project_list.length==0">
                                        <div class="p-5 text-center text-muted">搜索结果为空</div>
                                    </template>
                                    <template v-else>
                                        <div v-for="(item,index) in project_list" class="member-item" flex="dir:left">
                                            <div style="width: 40%" class="pr-3">
                                                <div class="member-name">{{item.title}}</div>
                                            </div>
                                            <div style="width: 30%" class="pr-3">
                                                <div class="member-name">{{item.type_str}}</div>
                                            </div>
                                            <div style="width: 30%" class="text-right">
                                                <a v-bind:index="index" href="javascript:" class="insert-project">添加</a>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="p-5 text-center text-muted">请输入关键字搜索项目</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">咨询产品</label>
                </div>
                <div class="col-sm-6">
                    <template v-if="type.length==0">
                        未包含产品
                    </template>
                    <template v-else>
                        <label class="radio-label" v-for="(item,index) in type">
                            <input class="form-control-checkbox" type="checkbox" v-bind:value="item.id" name="type[]" v-model='checkedIds'>
                            <span class="label-icon"></span>
                            <span class="label-text">{{item.name}}</span>
                        </label>
                    </template>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">手机号</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="phone" v-bind:value="phone">
                </div>
            </div>


            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">留言备注</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="remark" value="<?=$model->remark?>">
                </div>
            </div>
            <div class="form-group row" >
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">投资保姆</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" name="nanny_id" v-bind:value="nanny_id" hidden>
                    <input class="form-control" data-toggle="modal"
                           data-target="#searchNannyModal"
                           v-bind:value="nanny_real_name" readonly>
                </div>
                <!-- Modal -->
                <div class="modal fade" data-backdrop="static" id="searchNannyModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <b class="modal-title" id="exampleModalLongTitle">查找保姆</b>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" id="closeNannyModal">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="input-group">
                                    <input class="form-control" placeholder="姓名/电话" name="key_word" id="key_word2">
                                    <span class="input-group-btn">
                                                <button class="btn btn-secondary" id="searchNanny">搜索</button>
                                        </span>
                                </div>
                                <template v-if="nanny_list">
                                    <template v-if="nanny_list.length==0">
                                        <div class="p-5 text-center text-muted">搜索结果为空</div>
                                    </template>
                                    <template v-else>
                                        <div v-for="(item,index) in nanny_list" class="member-item" flex="dir:left">
                                            <div style="width: 30%" class="pr-3">
                                                <div class="member-name">{{item.real_name}}</div>
                                            </div>
                                            <div style="width: 30%" class="pr-3">
                                                <div class="member-name">{{item.phone}}</div>
                                            </div>
                                            <div style="width: 30%" class="text-right">
                                                <a v-bind:index="index" href="javascript:" class="insert-nanny">添加</a>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="p-5 text-center text-muted">请输入关键字搜索保姆</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row" v-for="(item,index) in type">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label ">{{money_type[item.id].name}}</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="number" v-bind:name="money_type[item.id].column_name" v-bind:value="money[money_type[item.id].column_name]">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">投资年限</label>
                </div>
                <div class="col-sm-6">
                    <select class="form-control" name="stage">
                        <?php foreach(\app\models\Enum::$STAGE_TYPE as $k => $v):?>
                            <option  value="<?=$k?>" <?=$model->stage == $k ? 'selected' : ''?>><?=$v?></option>
                        <?php endforeach;?>
                    </select>
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
    var money_type=[];
    money_type[1] = {name:'车位投资',column_name:'parking_money'};
    money_type[2] = {name:'公寓投资',column_name:'flats_money'};
    money_type[3] = {name:'商铺投资',column_name:'shop_money'};

    var app = new Vue({
        el: "#app",
        data: {
            money_type:money_type,
            member_list:null,
            nanny_list:null,
            member_id: <?=!empty($model->member_id)?$model->member_id:0?>,
            real_name:'<?=!empty($model->real_name)?$model->real_name : '请选择'?>',
            nanny_id: <?=!empty($model->nanny_id)?$model->nanny_id:0?>,
            nanny_real_name:'<?=!empty($model->nanny_id)?$model->nanny->real_name : '请选择'?>',
            phone:'<?=!empty($model->phone)?$model->phone : ''?>',
            project_list: null,
            project_id: <?=!empty($model->project_id)?$model->project_id:0?>,
            project_title:'<?=!empty($model->project->title)?$model->project->title : '请选择'?>',
            type:<?php
                    $type_arr = [];
                    if ($model->project) {
                        $type = explode(',',$model->project->type);
                        $type_selected = explode(',',$model->type);
                        foreach ($type as $k=>$v) {
                            $type_arr[$k]['id'] = $v;
                            $type_arr[$k]['name'] = \app\models\Enum::getTypeName($v);
                        }
                    }
                    echo json_encode($type_arr);
                ?>,
            checkedIds:<?php
                    $type_checked = explode(',',$model->type);
                    echo json_encode($type_checked);
                    ?>,
            money:<?php
               $array['parking_money'] = isset($model->parking_money) ? $model->parking_money : 0.00;
               $array['flats_money'] = isset($model->flats_money) ? $model->flats_money : 0.00;
               $array['shop_money'] = isset($model->shop_money) ? $model->shop_money : 0.00;
               echo json_encode($array);
            ?>,

        },
    });

    $(document).on("click", "#searchMember", function () {
        console.log(app.type);
        $("#searchMember").text('正在搜索...')
        $.ajax({
            url: '<?= $urlManager->createUrl(['mch/member/search-member']) ?>',
            dataType: "json",
            data: {
                key_word:$("#key_word").val(),
                role:1
            },
            success: function (res) {
                $("#searchMember").text('搜索')
                if (res.code == 0) {
                    app.member_list = res.data;

                }
            }
        });
        return false;
    });

    $(document).on("click", ".insert-member", function () {
        var index = $(this).attr("index");
        var member = app.member_list[index];
        app.member_id=member.id;
        app.real_name=member.real_name;
        app.phone=member.phone;
        $("#closeMemberModal").click();
    });

    $(document).on("click", "#searchProject", function () {
        $("#searchProject").text('正在搜索...')
        $.ajax({
            url: '<?= $urlManager->createUrl(['mch/project/search-project']) ?>',
            dataType: "json",
            data: {
                key_word:$("#key_word1").val()
            },
            success: function (res) {
                $("#searchProject").text('搜索')
                if (res.code == 0) {
                    app.project_list = res.data;
                }
            }
        });
        return false;
    });

    $(document).on("click", ".insert-project", function () {
        var index = $(this).attr("index");
        var project = app.project_list[index];
        app.project_id=project.id;
        app.project_title=project.title;
        app.type=project.type;
        $("#closeProjectModal").click();
    });

    $(document).on("click", "#searchNanny", function () {
        console.log(app.type);
        $("#searchNanny").text('正在搜索...')
        $.ajax({
            url: '<?= $urlManager->createUrl(['mch/member/search-member']) ?>',
            dataType: "json",
            data: {
                key_word:$("#key_word2").val(),
                role:2
            },
            success: function (res) {
                $("#searchNanny").text('搜索')
                if (res.code == 0) {
                    app.nanny_list = res.data;
                }
            }
        });
        return false;
    });

    $(document).on("click", ".insert-nanny", function () {
        var index = $(this).attr("index");
        var nanny = app.nanny_list[index];
        app.nanny_id=nanny.id;
        app.nanny_real_name=nanny.real_name;
        $("#closeNannyModal").click();
    });
</script>

