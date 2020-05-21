<?php
defined('YII_ENV') or exit('Access Denied');
$this->title = '添加角色';
$urlManager = Yii::$app->urlManager;
?>

<div class="panel mb-3" id="app">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">角色名称</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control cat-name role-name" name="name">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">描述</label>
                </div>
                <div class="col-sm-6">
                    <textarea id="description" class="description" style="width: 100%" name="description"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">权限</label>
                </div>
                <div class="col-sm-10">
                    <div class="col-sm-12" v-for="(permission,index) in permissions">
                        <label v-if="!permission.admin">
                            <input name="permission"
                                   v-bind:data-name="permission.name"
                                   v-bind:data-index="index"
                                   type="checkbox">
                            {{permission.name}}
                            <a data-toggle="collapse" v-bind:href="'#collapseExample'+ index" role="button"
                               aria-expanded="true" aria-controls="collapseExample" class="more"
                               v-if="permission.children">+</a>
                        </label>
                        <div class="collapse" v-bind:id="'collapseExample' + index">
                            <div class="card card-body">
                                <div style="margin-left: 30px;" class="row">
                                    <div class="col-10" v-for="(children1,c_index) in permission.children">
                                        <label v-if="!children1.admin">
                                            <input name="children1"
                                                   v-bind:data-name="children1.name"
                                                   v-bind:data-index="index"
                                                   v-bind:data-c_index="c_index"
                                                   type="checkbox"
                                                   v-bind:checked="children1.show"
                                            >
                                            {{children1.name}}
                                            <a data-toggle="collapse"
                                               v-bind:href="'#collapseExample' + index + '_' + c_index" role="button"
                                               aria-expanded="true" aria-controls="collapseExample" class="more"
                                               v-if="children1.children">+</a>
                                        </label>
                                        <div class="collapse" v-bind:id="'collapseExample' + index + '_' + c_index">
                                            <div class="card card-body">
                                                <div style="margin-left: 30px;" class="row">
                                                    <div class="col-3"
                                                         v-for="(children2,d_index) in children1.children">
                                                        <label v-if="!children2.admin">
                                                            <input name="children2"
                                                                   v-bind:data-name="children2.name"
                                                                   v-bind:data-index="index"
                                                                   v-bind:data-c_index="c_index"
                                                                   v-bind:data-d_index="d_index"
                                                                   type="checkbox"
                                                                   v-bind:checked="children2.show"
                                                            >
                                                            {{children2.name}}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary" onclick="store(this)" href="javascript:">保存</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var app = new Vue({
        el: "#app",
        data: {
            permissions: <?=$list?>
        }
    });
    console.log(app.permissions)
    $(document).on("change", "input[name=permission]", function () {
        var index = $(this).attr("data-index");
        var ck = $(this).prop("checked");
        app.permissions[index] = checked(app.permissions[index], ck);
    });

    $(document).on("change", "input[name=children1]", function () {
        var index = $(this).attr("data-index");
        var c_index = $(this).attr("data-c_index");
        var ck = $(this).prop("checked");
        app.permissions[index]['children'][c_index] = checked(app.permissions[index]['children'][c_index], ck);
    });

    $(document).on("change", "input[name=children2]", function () {
        var index = $(this).attr("data-index");
        var c_index = $(this).attr("data-c_index");
        var d_index = $(this).attr("data-d_index");
        var ck = $(this).prop("checked");
        app.permissions[index]['children'][c_index]['children'][d_index]['show'] = ck;
    });

    function checked(list, ck) {
        Vue.set(list, 'show', ck);
        if (list.hasOwnProperty('children') && list['children']) {
            for (var i in list['children']) {
                Vue.set(list['children'][i], 'show', ck);
                list['children'][i] = checked(list['children'][i], ck)
            }
        }
        return list;
    }

    function getRoleByUser(list) {
        var role = [];

        for (var i in list) {
            if (list[i].show) {
                role.push(list[i].route);
            }

            if (list[i].hasOwnProperty('children') && list[i]['children']) {
                role = role.concat(getRoleByUser(list[i]['children']));
            }
        }

        return role;
    }

    function store(t) {

        var role = getRoleByUser(app.permissions);
        var btn = $(t);
        btn.btnLoading();
        $.ajax({
            url: '<?= $urlManager->createUrl('mch/permission/role/store') ?>',
            type: 'POST',
            data: {
                role: role,
                name: $('.role-name').val(),
                description: $('.description').val(),
                _csrf: _csrf
            },
            success: function (res) {
                $.myAlert({
                    content: res.msg,
                    confirm: function () {
                        if (res.code == 0) {
                            location.reload();
                        }
                    }
                })
            },
            complete: function () {
                btn.btnReset();
            }
        })
    }
</script>
