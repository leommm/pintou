<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: wxf
 * Date: 2017/6/19
 * Time: 16:52
 */

$this->title = '用户列表';
$urlManager = Yii::$app->urlManager;
$user_login_url = Yii::$app->urlManager->createAbsoluteUrl(['mch/permission/passport/index', 'mch_store_id' => $this->context->store->id]);
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span style="color: red;">管理人员登录入口：</span>
        <a href="<?= $user_login_url ?>" target="_blank"><?= $user_login_url ?></a>
        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="nav-link"
                   href="<?= $urlManager->createUrl('mch/permission/user/create') ?>">添加用户</a>
            </li>
        </ul>
    </div>
    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>账号</th>
                <th>昵称</th>
                <th>创建日期</th>
                <th>操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item) : ?>
                <tr>
                    <td><?= $item->username ?></td>
                    <td><?= $item->nickname ?></td>
                    <td><?= date('Y-m-d H:i:s', $item->addtime) ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/permission/user/edit', 'id' => $item->id]) ?>">编辑</a>

                        <a class="btn btn-sm btn-danger article-delete"
                           href="<?= $urlManager->createUrl(['mch/permission/user/destroy', 'id' => $item->id]) ?>">删除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<script>
    $(document).on("click", ".article-delete", function () {
        var href = $(this).attr("href");
        $.confirm({
            content: "确认删除？",
            confirm: function () {
                $.loading();
                $.ajax({
                    url: href,
                    dataType: "json",
                    success: function (res) {
                        $.myAlert({
                            content: res.msg,
                            confirm: function () {
                                if (res.code == 0) {
                                    location.reload();
                                }
                            }
                        })

                    }
                });
            }
        });
        return false;
    });
</script>
