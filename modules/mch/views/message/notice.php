<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '公告列表';
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
    </div>
              
    <form method="get" >
        <?php $_s = ['is_push'] ?>
        <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
            <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
        <?php endforeach; ?>
        <div>
            <select style="display:inline;max-width:10%" class="form-control" name="is_push">
                <option value="" <?= $search['is_push']==='' ? 'selected' : ''?>>全部状态</option>
                <option value="0" <?= $search['is_push']==='0' ? 'selected' : ''?>>已推送</option>
                <option value="1" <?= $search['is_push']==='1' ? 'selected' : ''?>>待推送</option>

            </select>
            <button style="margin-bottom: 6px;margin-left:30px" class="btn btn-primary mr-4">筛选</button>
        </div>
    </form>        

    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th class="text-center">标题</th>
                <th class="text-center">内容</th>
                <th class="text-center">小程序页面</th>
                <th class="text-center">状态</th>
                <th class="text-center">创建时间</th>
                <th class="text-center">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td class="text-center">
                        <p><?=$item->title?></p>
                    </td>
                    <td>
                        <p><?=$item->content?></p>
                    </td>
                    <td class="text-center">
                        <p><?= $item->page_url?></p>
                    </td>
                    <td class="text-center"><?= \app\models\Enum::$APPLY_STATUS_TYPE[$item->status] ?></td>

                    <td class="text-center"><?= $item->create_time ?></td>
                    <td class="text-center">
                        <?php
                            if ($item->status==0) {
                                ?>
                                <a class="btn btn-sm btn-primary apply-btn"
                                   href="<?= $urlManager->createUrl(['mch/member/apply', 'id' => $item->id,'status'=>1]) ?>">通过</a>
                                <a class="btn btn-sm btn-danger apply-btn"
                                   href="<?= $urlManager->createUrl(['mch/member/apply', 'id' => $item->id,'status'=>2]) ?>">驳回</a>
                        <?php    }
                        ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
        <div class="text-center">
            <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination,]) ?>
            <div class="text-muted"><?= $pagination->totalCount ?>条数据</div>
        </div>
    </div>
</div>

<script>
    $(document).on("click", ".apply-btn", function () {
        var url = $(this).attr("href");
        var content = '确认' + $(this).text() + '?';
        $.confirm({
            content: content,
            confirm: function () {
                $.loading();
                $.ajax({
                    url: url,
                    dataType: "json",
                    success: function (res) {
                        $.myAlert({
                            content:res.msg
                        });
                        if (res.code==0) {
                            location.reload();
                        }else {

                        }
                    }
                });
            }
        });
        return false;
    });
</script>