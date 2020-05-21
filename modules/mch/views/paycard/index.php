<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$cat = [
    1 => '打卡记录',
];
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
$this->params['page_navs'] = [
    [
        'name' => '打卡记录',
        'active' => $cat_id == 1,
        'url' => $urlManager->createUrl(['mch/paycard/index', 'cat_id' => 1,]),
    ],

];
?>
<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>

        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="btn btn-sm btn-primary"
                   href="<?= $urlManager->createUrl(['mch/paycard/edit', 'cat_id' => 1]) ?>">打卡设置</a>
            </li>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <form action="" method="post">
                <input type="hidden" value="jr" name="jr" >
                <input class="btn btn-sm btn-primary" type="submit"  value="今日打卡">
            </form>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <form action="" method="post">
                <input type="hidden" value="qb" name="qb" >
                <input  class="btn btn-sm btn-primary" type="submit"  value="全部打卡">
            </form>
            &nbsp;&nbsp;&nbsp;&nbsp;
        </ul>

        <br>
        <div class="float-right mb-4">
            <form method="post" action="">
                <div class="input-group">
                    <input class="form-control"
                           placeholder="打卡人昵称"
                           name="nickname"
                           value="">
                    <span class="input-group-btn">
                    <input  class="btn btn-sm btn-primary" type="submit"  value="搜索">
                </span>
                </div>
            </form>
        </div>

    </div>
    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th style="text-align:center; ">ID</th>
                <th style="text-align:center; ">打卡人</th>
                <th style="text-align:center; " width="200" >打卡地点</th>
                <th style="text-align:center; " width="260">打卡时间</th>

            </tr>
            </thead>
            <?php foreach ($list as $item) : ?>
                <tr>
                    <td style="text-align:center;vertical-align:middle;"><?= $item['id'] ?></td>
                    <td style="text-align:center;vertical-align:middle;"><?= $item['nickname'] ?></td>
                    <td style="text-align:center;vertical-align:middle;"><?= $item['paysite']?></td>
                    <td style="text-align:center;vertical-align:middle;"><?= date('Y-m-d,H-i',$item['addtime'])?></td>


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