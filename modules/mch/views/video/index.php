<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$cat = [
    1 => '视频列表',
];
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
$this->params['page_navs'] = [
    [
        'name' => '视频列表',
        'active' => $cat_id == 1,
        'url' => $urlManager->createUrl(['mch/video/index', 'cat_id' => 1,]),
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
                       href="<?= $urlManager->createUrl(['mch/video/edit', 'cat_id' => 1]) ?>">添加视频</a>
                </li>
            </ul>

    </div>
    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th style="text-align:center; ">ID</th>
                <th style="text-align:center; ">标题</th>
                <th style="text-align:center; " width="200" >视频地址</th>
                <th style="text-align:center; " width="260">视频介绍</th>
                <th style="text-align:center; ">序号</th>
                <th style="text-align:center; ">上传时间</th>
                <th style="text-align:center; ">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item) : ?>
                <tr>
                    <td style="text-align:center;vertical-align:middle;"><?= $item->id ?></td>
                    <td style="text-align:center;vertical-align:middle;"><?= $item->title ?></td>
                    <td >
                        <video  height="100" controls autoplay>
                            <source src="<?= $item->url?>" type="video/ogg">
                            <source src="<?= $item->url?>" type="video/mp4">
                            <source src="<?= $item->url?>" type="video/webm">
                            <object data="<?= $item->url?>" width="320" height="240">
                                <embed width="320" height="240" src="<?= $item->url?>">
                            </object>
                        </video>
                    </td>

                    <td style="text-align:center;vertical-align:middle;"><?= $item->content?></td>
                    <td style="text-align:center;vertical-align:middle;"><?= $item->sort?></td>
                    <td style="text-align:center;vertical-align:middle;"><?= date('Y-m-d,H-i',$item->addtime)?></td>

                    <td style="text-align:center;vertical-align:middle;">
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/video/edit','cat_id' => 2,'id' => $item->id]) ?>">编辑</a>

                            <a class="btn btn-sm btn-danger article-delete"
                               href="<?= $urlManager->createUrl(['mch/video/del', 'id' => $item->id]) ?>">删除</a>

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