<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '商户收入';
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
    </div>
              
    <form method="get" >
        <?php $_s = ['member_id','shop_id','is_cash'] ?>
        <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
            <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
        <?php endforeach; ?>
        <div>
            <input type="text" class="form-control" name="member_id" value="<?=$search['member_id']?>" hidden>
            <input type="text" class="form-control" name="shop_id" value="<?=$search['shop_id']?>" hidden>

            <select style="display:inline;max-width:10%" class="form-control" name="is_cash">
                <option value="" <?= $search['is_cash']==='' ? 'selected' : ''?>>全部状态</option>
                <option value="0" <?= $search['is_cash']==='0' ? 'selected' : ''?>>未提现</option>
                <option value="1" <?= $search['is_cash']==='1' ? 'selected' : ''?>>已提现</option>
            </select>
            <button style="margin-bottom: 6px;margin-left:30px" class="btn btn-primary mr-4">筛选</button>
        </div>
    </form>        

    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th style="width: 500px">店铺信息</th>
                <th style="width: 300px">会员信息</th>
                <th class="text-center">消费金额</th>
                <th class="text-center">状态</th>
                <th class="text-center">创建时间</th>
                <th class="text-center">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td>
                        <p>店铺名称：<?=$item->shop->shop_name?></p>
                        <p>店主名称：<?=$item->shop->real_name?></p>
                        <p>联系电话：<?=$item->shop->phone?></p>

                    </td>

                    <td>
                        <p>姓名：<?=$item->member->real_name?></p>
                        <p>手机号：<?=$item->member->phone?></p>
                    </td>

                    <td class="text-center">
                        <p><?=$item->amount?></p>
                    </td>
                    <td class="text-center">
                        <p><?=$item->is_cash? '已提现':'未提现' ?></p>
                    </td>
                    <td class="text-center"><?= $item->create_time ?></td>
                    <td class="text-center">
                        <?php if (!$item->is_cash) :?>
                            <a class="btn btn-sm btn-primary"
                               href="<?= $urlManager->createUrl(['mch/member/shop-cash', 'id' => $item->id])?>">提现</a>
                        <?php endif;?>
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
    $(document).on("click", ".delete-btn", function () {
        var url = $(this).attr("href");
        $.confirm({
            content: '确认删除？',
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