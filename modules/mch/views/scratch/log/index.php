<?php
defined('YII_ENV') or exit('Access Denied');

use yii\widgets\LinkPager;
$urlManager = Yii::$app->urlManager;
$this->title = '抽奖记录';
?>

<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
    <div class="mb-3 clearfix">

        <div class="float-left">
            <div class="dropdown float-right ml-2">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= isset($_GET['type']) ? $_GET['cat'] : '全部商品' ?>
                    <?= $_GET['type']==1 ? '红包':'' ?>
                    <?= $_GET['type']==2 ? '优惠券':'' ?>
                    <?= $_GET['type']==3 ? '积分':'' ?>
                    <?= $_GET['type']==4 ? '赠品':'' ?>
                    <?= $_GET['type']==5 ? '谢谢参与':'' ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                     style="max-height: 200px;overflow-y: auto">
                    <a class="dropdown-item" href="<?= $urlManager->createUrl(['mch/scratch/log/index']) ?>">全部商品</a>            
                    <a class="dropdown-item" href="<?= $urlManager->createUrl(['mch/scratch/log/index','type' => 1]) ?>">余额红包</a>
                    <a class="dropdown-item" href="<?= $urlManager->createUrl(['mch/scratch/log/index','type' => 2]) ?>">优惠券</a>
                    <a class="dropdown-item" href="<?= $urlManager->createUrl(['mch/scratch/log/index','type' => 3]) ?>">积分</a>
                    <a class="dropdown-item" href="<?= $urlManager->createUrl(['mch/scratch/log/index','type' => 4]) ?>">赠品</a>
                    <a class="dropdown-item" href="<?= $urlManager->createUrl(['mch/scratch/log/index','type' => 5]) ?>">谢谢参与</a>
                </div>
            </div>
        </div>

        <div class="float-right mb-4">
            <form method="get">
                <?php $_s = ['nickname'] ?>
                <?php foreach ($_GET as $_gi => $_gv) :
                    if (in_array($_gi, $_s)) {
                        continue;
                    } ?>
                    <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
                <?php endforeach; ?>

                <div class="input-group">
                    <input class="form-control"
                           placeholder="用户名"
                           name="nickname"
                           autocomplete="off"
                           value="<?= isset($_GET['nickname']) ? trim($_GET['nickname']) : null ?>">
                    <span class="input-group-btn">
                    <button class="btn btn-primary">搜索</button>
                </span>
                </div>
            </form>
        </div>
        <div class="text-danger"></div>
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>用户</th>
                <th>物品</th>
                <th>状态</th>
                <th>抽奖时间</th> 
                <th>领取时间</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $index => $item): ?>
                <tr>
                    <td class="nowrap"><?= $item->id ?></td>
                    <td class="nowrap"><?= $item->user->nickname ?></td>
                    <td class="nowrap">
                        <?php if ($item->type == 1): ?><?= $item->price ?>元余额红包<?php endif; ?>
                        <?php if ($item->type == 2): ?><?= $item->coupon->name;?><?php endif; ?>
                        <?php if ($item->type == 3): ?><?= $item->num ?>积分<?php endif; ?>
                        <?php if ($item->type == 4): ?><?= $item->gift->name;?><?php endif; ?>
                        <?php if ($item->type == 5): ?>谢谢惠顾<?php endif; ?>
                     </td>
                    <td classs="nowrap">
                            <?php if ($item->status == 0): ?>预领取<?php endif; ?>
                        <?php if($item->type!=5):?>
                            <?php if ($item->status == 1): ?>未兑换<?php endif; ?>
                            <?php if ($item->status == 2): ?>已兑换<?php endif; ?>
                        <?php endif?>
                    </td>
                    <td class="nowrap">
                        <?= date('Y:m:d H:i:s',$item->create_time);?>
                    <td class="nowrap">
                        <?php if ($item->raffle_time >0): ?><?= date('Y:m:d H:i:s',$item->raffle_time) ?><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation example">
            <?php echo LinkPager::widget([
                'pagination' => $pagination,
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
                'firstPageLabel' => '首页',
                'lastPageLabel' => '尾页',
                'maxButtonCount' => 5,
                'options' => [
                    'class' => 'pagination',
                ],
                'prevPageCssClass' => 'page-item',
                'pageCssClass' => "page-item",
                'nextPageCssClass' => 'page-item',
                'firstPageCssClass' => 'page-item',
                'lastPageCssClass' => 'page-item',
                'linkOptions' => [
                    'class' => 'page-link',
                ],
                'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
            ])?>
        </nav>
    </div>
</div>