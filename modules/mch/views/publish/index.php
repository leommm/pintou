<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$cat = [
    1 => '我要买',
    2 => '我要卖',
    3 => '我要服务',
];
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
$this->params['page_navs'] = [
    [
        'name' => '我要买',
        'active' => $cat_id == 1,
        'url' => $urlManager->createUrl(['mch/publish/index', 'cat_id' => 1,]),
    ],
    [
        'name' => '我要卖',
        'active' => $cat_id == 2,
        'url' => $urlManager->createUrl(['mch/publish/index', 'cat_id' => 2,]),
    ],
    [
        'name' => '我要服务',
        'active' => $cat_id == 3,
        'url' => $urlManager->createUrl(['mch/publish/index', 'cat_id' => 3,]),
    ],
];
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
        <?php if ($cat_id != 1) : ?>
            <ul class="nav nav-right">
                <li class="nav-item">
                    <a class="nav-link"
                       href="<?= $urlManager->createUrl(['mch/article/edit', 'cat_id' => 2]) ?>">添加文章</a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>类型</th>
                <th>标题</th>
                <th>发布时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item) : ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?= $item ->type ?></td>
                    <td><?= $item->title ?></td>
                    <td><?= date('Y-m-d,H:i',$item->create_time) ?></td>
                    <td><?= $item->state ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/publish/edit','id' => $item->id]) ?>">查看</a>
                        <?php if ($item->audit != 1) : ?>
                            <a class="btn btn-sm btn-danger article-delete"
                               href="<?= $urlManager->createUrl(['mch/publish/audit', 'id' => $item->id]) ?>">审核</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="text-center">
            <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination,]) ?>
            <div class="text-muted"><?= $row_count ?>条数据</div>
        </div>
    </div>
</div>

<script>
    $(document).on("click", ".article-delete", function () {
        var href = $(this).attr("href");
        $.confirm({
            content: "确认审核吗？",
            confirm: function () {
                $.loading();
                $.ajax({
                    url: href,
                    dataType: "json",
                    success: function (res) {
                        content:res.msg,
                        location.reload();
                    }
                });
            }
        });
        return false;
    });
</script>