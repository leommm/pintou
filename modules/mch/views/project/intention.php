<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '意向列表';
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="nav-link" href="<?= $urlManager->createUrl(['mch/project/intention-edit']) ?>">添加意向</a>
            </li>
        </ul>
    </div>
              
    <form method="get" >
        <?php $_s = ['type','status'] ?>
        <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
            <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
        <?php endforeach; ?>
        <div>
            <select style="display:inline;max-width:10%" class="form-control" name="type">
                <option value="0" <?= $search['type']==0 ? 'selected' : ''?>>全部产品</option>
                <?php foreach (\app\models\Enum::$PRODUCT_TYPE as $k => $v) { ?>
                    <option value="<?=$k?>" <?= $search['type']==$k ? 'selected' : ''?>><?=$v?></option>
                <?php }?>
            </select>
            <select style="display:inline;max-width:10%" class="form-control" name="status">
                <option value="0" <?= $search['status']==0 ? 'selected' : ''?>>全部状态</option>
                <?php foreach (\app\models\Enum::$STATUS_TYPE as $k => $v) { ?>
                    <option value="<?=$k?>" <?= $search['status']==$k ? 'selected' : ''?>><?=$v?></option>
                <?php }?>
            </select>
            <button style="margin-bottom: 6px;margin-left:30px" class="btn btn-primary mr-4">筛选</button>
        </div>
    </form>        

    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th >会员信息</th>
                <th style="width:200px">项目</th>
                <th>咨询产品</th>
                <th class="text-center">留言备注</th>
                <th>投资信息</th>
                <th class="text-center">拼投保姆</th>
                <th class="text-center">状态</th>
                <th class="text-center">创建时间</th>
                <th class="text-center">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td>
                        <p>姓名：<?=$item->real_name?></p>
                        <p>手机：<?=$item->phone?></p>
                    </td>
                    <td>
                        <?=$item->project->title?>
                    </td>
                    <td>
                        <p><?=\app\models\Enum::getTypeNameByString($item->type)?></p>
                    </td>
                    <td class="text-center"><?= $item->remark ?></td>
                    <td>
                        <p>车位投资：<?= $item->parking_money ?></p>
                        <p>公寓投资：<?= $item->flats_money ?></p>
                        <p>商铺投资：<?= $item->shop_money ?></p>
                        <p>投资年限：<?=\app\models\Enum::getStageName($item->stage)?></p>
                    </td>
                    <td class="text-center"><?= $item->nanny->real_name ?></td>
                    <td class="text-center">
                        <?php
                            $arr = \app\models\Enum::$STATUS_TYPE;
                            echo $arr[$item->status];
                        ?>
                    </td>
                    <td class="text-center"><?= $item->create_time ?></td>
                    <td class="text-center">
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/project/intention-edit', 'id' => $item->id]) ?>">编辑</a>
                        <a class="btn btn-sm btn-danger delete-btn"
                           href="<?= $urlManager->createUrl(['mch/project/intention-delete', 'id' => $item->id]) ?>">删除</a>
                        <br>
                        <a class="btn btn-sm btn-info" style="margin-top: 5px"
                           href="<?= $urlManager->createUrl(['mch/project/intention-follow', 'id' => $item->id]) ?>">跟进记录</a>
                        <?php if ($item->status==3):?>
                            <br>
                            <a class="btn btn-sm btn-warning" style="margin-top: 5px"
                               href="<?= $urlManager->createUrl(['mch/project/member-income', 'id' => $item->id]) ?>">返利记录</a>
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
            content: "确认删除？",
            confirm: function () {
                $.loading();
                $.ajax({
                    url: url,
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