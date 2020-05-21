<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '认证申请';
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
    </div>
              
    <form method="get" >
        <?php $_s = ['status'] ?>
        <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
            <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
        <?php endforeach; ?>
        <div>
            <select style="display:inline;max-width:10%" class="form-control" name="status">
                <option value="" <?= $search['status']==='' ? 'selected' : ''?>>全部状态</option>
                <option value="0" <?= $search['status']==='0' ? 'selected' : ''?>>待审核</option>
                <option value="1" <?= $search['status']==='1' ? 'selected' : ''?>>通过</option>
                <option value="2" <?= $search['status']==='2' ? 'selected' : ''?>>驳回</option>

            </select>
            <button style="margin-bottom: 6px;margin-left:30px" class="btn btn-primary mr-4">筛选</button>
        </div>
    </form>        

    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th class="text-center">头像</th>
                <th class="text-center">微信昵称</th>
                <th style="width: 300px">申请信息</th>
                <th class="text-center">认证类型</th>
                <th class="text-center">状态</th>
                <th class="text-center">创建时间</th>
                <th class="text-center">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td class="text-center">
                        <img src="<?=$item->user->avatar_url?>" width="100px">
                    </td>
                    <td class="text-center">
                        <p><?=$item->user->nickname?></p>
                    </td>
                    <td>
                        <p>姓名：<?=$item->real_name?></p>
                        <p>手机号：<?=$item->phone?></p>
                        <p>身份证：<?=$item->id_card?></p>
                        <p>银行卡：<?=$item->bank_card?></p>
                    </td>
                    <td class="text-center">
                        <p><?= \app\models\Enum::$LOGIN_TYPE[$item->type]?></p>
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