<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$cat = [
    1 => '路线列表',
];
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
$this->params['page_navs'] = [
    [
        'name' => '路线列表',
        'active' => $cat_id == 1,
        'url' => $urlManager->createUrl(['mch/station/index', 'cat_id' => 1,]),
    ],

];
?>
<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
        <br>

        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="btn btn-sm btn-primary"
                   href="<?= $urlManager->createUrl(['mch/station/edit', 'cat_id' => 1]) ?>">添加路线</a>
            </li>
        </ul>

    </div>
    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th style="text-align:center; ">ID</th>
                <th style="text-align:center; ">用户昵称</th>
                <th style="text-align:center; " width="200" >站点地址</th>
                <th style="text-align:center; " width="200" >序号</th>
                <th style="text-align:center; " width="260">添加时间</th>
                <th style="text-align:center; ">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item) : ?>
                <tr>
                    <td style="text-align:center;vertical-align:middle;"><?= $item['id']?></td>
                    <td style="text-align:center;vertical-align:middle;"><?= $item['nickname']?></td>

                    <td style="text-align:center;vertical-align:middle;"><?= $item['name']?></td>
                    <td style="text-align:center;vertical-align:middle;"><?= $item['sort']?></td>

                    <td style="text-align:center;vertical-align:middle;"><?= date('Y-m-d,H:i',$item['create_time'])?></td>

                    <td style="text-align:center;vertical-align:middle;">
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/station/edit','cat_id' => 2,'id' => $item['id']]) ?>">编辑</a>

                        <a class="btn btn-sm btn-danger article-delete"
                           href="<?= $urlManager->createUrl(['mch/station/del', 'id' =>$item['id']]) ?>">删除</a>

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
            content: "确认删除？",
            confirm: function () {
                $.loading();
                $.ajax({
                    url: href,
                    dataType: "json",
                    success: function (res) {
                        location.reload();
                    }
                });
            }
        });
        return false;
    });
</script>