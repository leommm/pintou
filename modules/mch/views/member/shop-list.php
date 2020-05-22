<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '商户列表';
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="nav-link" href="<?= $urlManager->createUrl(['mch/member/shop-edit']) ?>">添加商户</a>
            </li>
        </ul>
    </div>
              
    <form method="get" >
        <?php $_s = ['is_active','name'] ?>
        <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
            <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
        <?php endforeach; ?>
        <div>
            <input type="text" class="form-control" style="width: 150px;display: inline-block" name="name" placeholder="姓名/店铺名" value="<?=$search['name']?>">

            <select style="display:inline;max-width:10%" class="form-control" name="is_active">
                <option value="" <?= $search['is_active']==='' ? 'selected' : ''?>>全部状态</option>
                <option value="0" <?= $search['is_active']==='0' ? 'selected' : ''?>>未认证</option>
                <option value="1" <?= $search['is_active']==='1' ? 'selected' : ''?>>已认证</option>
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
                <th style="width: 300px">店主信息</th>
                <th class="text-center">累计收入</th>
                <th class="text-center">创建时间</th>
                <th class="text-center">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td>
                        <p>名称：<?=$item->shop_name?></p>
                        <p>类型：<?=$item->shop_type?></p>
                        <p>地址：<?=$item->shop_address?></p>
                    </td>
                    <td>
                        <p>姓名：<?=$item->real_name?><?=$item->is_active? '（已认证）' : '（未认证）' ?></p>
                        <p>手机号：<?=$item->phone?></p>
                        <p>身份证：<?=$item->id_card?></p>
                        <p>银行卡：<?=$item->bank_card?></p>
                    </td>
                    <td class="text-center">
                        <p><?=$item->total_income?></p>
                    </td>

                    <td class="text-center"><?= $item->create_time ?></td>
                    <td class="text-center">
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/member/shop-edit', 'id' => $item->id,'status'=>1]) ?>">编辑</a>
                        <a class="btn btn-sm btn-danger delete-btn"
                           href="<?= $urlManager->createUrl(['mch/member/shop-delete', 'id' => $item->id,'status'=>2]) ?>">删除</a>
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