<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '成员列表';
?>
<style>

</style> 
<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="nav-link" href="<?= $urlManager->createUrl(['mch/member/edit']) ?>">添加成员</a>
            </li>
        </ul>
    </div>
              
    <form method="get">
        <?php $_s = ['type'] ?>
        <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
            <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
        <?php endforeach; ?>
        
        <div>
            <select style="display:inline;max-width:10%" class="form-control" name="type">
                <option value="0" <?= $type==0 ? 'selected' : ''?>>全部</option>
                <option value="1" <?= $type==1 ? 'selected' : ''?>>会员</option>
                <option value="2" <?= $type==2 ? 'selected' : ''?>>投资保姆</option>
                <option value="3" <?= $type==3 ? 'selected' : ''?>>经纪人</option>
                <option value="4" <?= $type==4 ? 'selected' : ''?>>城市合伙人</option>
            </select> 
            <button style="margin-bottom: 6px;margin-left:30px" class="btn btn-primary mr-4">筛选</button>
        </div>
    </form>        

    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th class="text-center">姓名</th>
                <th class="text-center">上级名称</th>
                <th class="text-center">电话</th>
                <th class="text-center">身份信息</th>
                <th>账户信息</th>
                <th class="text-center">加入时间</th>
                <th class="text-center">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td class="text-center"><?php
                            $str = $item['real_name'];
                            if($item['role']!= 2){
                                $str .= $item['is_active'] ? ' (已认证)':' (未认证)';
                            }
                           echo $str;
                        ?></td>
                    <td class="text-center"><?=!empty($item['parent_name']) ? $item['parent_name'] : '无'?></td>

                    <td class="text-center">
                        <?= $item['phone'] ?>
                    </td>
                    <td class="text-center">
                        <?php
                            $type = \app\models\Enum::$ROLE_TYPE;
                            $str = $type[$item['role']];
                            if ($item['is_partner']) {
                                $str .= '/城市合伙人';
                                $str .= '<br>'.$item['area'];
                            }
                            echo $str;
                        ?>
                    </td>
                    <td>
                        <p>返利：<?=$item['account_a']?></p>
                        <p>可消费：<?=$item['account_b']?></p>
                        <p>佣金：<?=$item['account_c']?></p>

                    </td>
                    <td class="text-center"><?=$item['create_time']?></td>
                    <td class="text-center">
                        <div class="mb-2">
                            <a class="btn btn-sm btn-primary"
                               href="<?= $urlManager->createUrl(['mch/member/edit', 'id' => $item['id']]) ?>">编辑</a>
                            <a class="btn btn-sm btn-danger delete-btn"
                               href="<?= $urlManager->createUrl(['mch/member/delete', 'id' => $item['id']]) ?>">删除</a>
                        </div>
                        <div>
                            <a class="btn btn-sm btn-info"
                               href="<?= $urlManager->createUrl(['mch/member/commission-log', 'id' => $item['id']]) ?>">佣金记录</a>
                        </div>
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