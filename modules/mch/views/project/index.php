<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '项目列表';
?>
<style>
    .cover-pic {
        display: block;
        width: 8rem; 
        height: 5rem;
        background-size: cover;
        background-position: center;
    }
</style> 
<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="nav-link" href="<?= $urlManager->createUrl(['mch/project/edit']) ?>">添加项目</a>
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
                <option value="0" <?= $search['type']==0 ? 'selected' : ''?>>全部产品</option>
                <option value="1" <?= $search['type']==1 ? 'selected' : ''?>>车位</option>
                <option value="2" <?= $search['type']==2 ? 'selected' : ''?>>公寓</option>
                <option value="3" <?= $search['type']==3 ? 'selected' : ''?>>商铺</option>
            </select> 
            <button style="margin-bottom: 6px;margin-left:30px" class="btn btn-primary mr-4">筛选</button>
        </div>
    </form>        

    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th style="width:200px">项目标题</th>
                <th class="text-center">封面图</th>
                <th class="text-center">所属区域</th>
                <th class="text-center">包含产品</th>
                <th class="text-center">是否热门</th>
                <th class="text-center">排序</th>
                <th class="text-center">创建时间</th>
                <th class="text-center">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td style="word-wrap:break-word"><?=$item->title?></td>
                    <td class="text-center">
                    <div></div>
                        <div class="cover-pic" style="background-image: url('<?= $item->cover_pic ?>')"></div>
                    </td>

                    <td class="text-center">
                        <?= $item['area'] ?>
                    </td>
                    <td class="text-center">
                        <p><?=\app\models\Enum::getTypeNameByString($item->type)?></p>
                    </td>
                    <td class="text-center"><?= $item->is_hot == 0 ? '否' : '是' ?></td>
                    <td class="text-center"><?= $item->sort ?></td>
                    <td class="text-center"><?= $item->create_time ?></td>
                    <td class="text-center">
                        <div class="mb-2">
                            <a class="btn btn-sm btn-primary"
                               href="<?= $urlManager->createUrl(['mch/project/edit', 'id' => $item->id]) ?>">编辑</a>
                        </div>
                        <div>
                            <a class="btn btn-sm btn-danger delete-btn"
                               href="<?= $urlManager->createUrl(['mch/project/delete', 'id' => $item->id]) ?>">删除</a>
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