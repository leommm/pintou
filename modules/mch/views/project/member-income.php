<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '返利记录';
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="btn btn-success" data-toggle="modal" data-target="#addIncomeModal"
                   href="javascript:;">添加返利</a>
            </li>
        </ul>
        <div class="modal fade" id="addIncomeModal" data-backdrop="static">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">添加返利</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input class="form-control" id="income" type="number" placeholder="请填写返利金额" value="0">
                        <input hidden id="member_id" type="number" value="<?=$member_id?>">
                        <input hidden id="intention_id" type="number" value="<?=$intention_id?>">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary save-income">提交</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th style="width: 300px; ">项目</th>
                <th>会员信息</th>
                <th class="text-center" style="width: 200px">返利金额</th>
                <th class="text-center">创建时间</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td style="word-wrap:break-word">
                        <p><?=$item->intention->project->title?></p>
                    </td>
                    <td>
                        <p>姓名：<?=$item->intention->real_name?></p>
                        <p>手机：<?=$item->intention->phone?></p>
                    </td>
                    <td class="text-center">
                        <?=$item->amount?>
                    </td>
                    <td class="text-center"><?= $item->create_time ?></td>
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
    $(document).on('click', '.save-income', function () {
        var btn = $(this);
        btn.btnLoading(btn.text());
        $.ajax({
            url: "<?=$urlManager->createUrl(['mch/project/add-income'])?>",
            type: "post",
            dataType: 'json',
            data: {
                data:{
                    amount:$("#income").val(),
                    member_id:$("#member_id").val(),
                    intention_id:$("#intention_id").val()
                },
                _csrf: _csrf
            },
            success: function (res) {
                if (res.code == 0) {
                    $("#addIncomeModal").modal('hide');
                    $.myAlert({
                        content:res.msg,
                        confirm:function(res){
                            window.location.reload();
                        }
                    });
                } else {
                    $.myAlert({
                        content:res.msg,
                    });
                    btn.btnReset();
                }
            }
        });
    });
</script>