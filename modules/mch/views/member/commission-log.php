<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '佣金记录';
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
    </div>
              
    <form method="get" >
        <?php $_s = ['is_settle'] ?>
        <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
            <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
        <?php endforeach; ?>
        <div>
            <select style="display:inline;max-width:10%" class="form-control" name="is_settle">
                <option value="" <?= $search['is_settle']==='' ? 'selected' : ''?>>全部状态</option>
                <option value="1" <?= $search['is_settle']==='1' ? 'selected' : ''?>>已结算</option>
                <option value="0" <?= $search['is_settle']==='0' ? 'selected' : ''?>>未结算</option>

            </select>
            <button style="margin-bottom: 6px;margin-left:30px" class="btn btn-primary mr-4">筛选</button>
        </div>
    </form>        

    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>成员信息</th>
                <th style="width:300px">拼投信息</th>
                <th class="text-center">佣金</th>
                <th class="text-center">佣金类型</th>
                <th class="text-center">结算状态</th>
                <th class="text-center">创建时间</th>
                <th class="text-center">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td>
                        <p>姓名：<?=$item->member->real_name?></p>
                        <p>手机：<?=$item->member->phone?></p>
                        <p>身份：<?php
                            $type = \app\models\Enum::$ROLE_TYPE;
                            $str = $type[$item->member->role];
                            if ($item->member->is_partner) {
                                $str .= '/城市合伙人';
                                $str .= '<br>'.$item->member->area;
                            }
                            echo $str;
                            ?>
                        </p>
                    </td>
                    <td>
                        <p>拼投项目：<?php echo  $item->intention->project->title?></p>
                        <p>拼投产品：<?php
                            $type = [];
                            if ($item->intention->parking_money) {
                                $type[] = 1;
                            }
                            if ($item->intention->flats_money) {
                                $type[] = 2;
                            }
                            if ($item->intention->shop_money) {
                                $type[] = 3;
                            }
                            echo \app\models\Enum::getTypeNameByString(implode(',',$type));
                            ?></p>
                        <p>拼投总额：<?=floatval($item->intention->parking_money) + floatval($item->intention->flats_money) + floatval($item->intention->shop_money)?></p>
                        <p>拼投人：<?=$item->intention->real_name?></p>
                        <p>联系电话：<?=$item->intention->phone?></p>
                    </td>
                    <td class="text-center"><?= $item->amount ?></td>

                    <td class="text-center">
                        <p><?=\app\models\Enum::$COMMISSION_TYPE[$item->type]?></p>
                    </td>
                    <td class="text-center"><?= $item->is_settle ? '已结算' : '未结算' ?></td>
                    <td class="text-center"><?= $item->create_time ?></td>
                    <td class="text-center">
                        <?php
                            if ($item->is_settle==0) {
                                ?>
                                <a class="btn btn-sm btn-danger settle-btn"
                                   href="<?= $urlManager->createUrl(['mch/member/settle', 'id' => $item->id]) ?>">结算</a>
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
    $(document).on("click", ".settle-btn", function () {
        var url = $(this).attr("href");
        $.confirm({
            content: "确认结算？",
            confirm: function () {
                $.loading();
                $.ajax({
                    url: url,
                    dataType: "json",
                    success: function (res) {
                        $.myAlert({
                            content:res.msg
                        });
                        location.reload();
                    }
                });
            }
        });
        return false;
    });
</script>