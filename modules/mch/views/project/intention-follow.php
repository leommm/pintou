<?php
defined('YII_ENV') or exit('Access Denied');
$urlManager = Yii::$app->urlManager;
$this->title = '跟进记录';
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
    </div>
    <form method="get" >
        <?php $_s = ['nanny_id','status','intention_id'] ?>
        <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
            <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
        <?php endforeach; ?>
        <div>
            <input type="text" name="intention_id" value="<?=$search['intention_id']?>" hidden>
            <select style="display:inline;max-width:10%" class="form-control" name="nanny_id">
                <option value="0" <?= $search['type']==0 ? 'selected' : ''?>>全部保姆</option>
                <?php foreach ($nanny_list as $v) { ?>
                    <option value="<?=$v['id']?>" <?= $search['nanny_id']==$v['id'] ? 'selected' : ''?>><?=$v['real_name']?></option>
                <?php }?>
            </select>
            <select style="display:inline;max-width:10%" class="form-control" name="status">
                <option value="0" <?= $search['type']==0 ? 'selected' : ''?>>全部状态</option>
                <option value="2" <?= $search['status']==2 ? 'selected' : ''?>>跟进中</option>
                <option value="3" <?= $search['status']==3 ? 'selected' : ''?>>已成交</option>
            </select>
            <button style="margin-bottom: 6px;margin-left:30px" class="btn btn-primary mr-4">筛选</button>
        </div>
    </form>        

    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>保姆信息</th>
                <th>会员信息</th>
                <th>意向信息</th>
                <th style="width: 350px;word-wrap:break-word;word-break:break-all">跟进记录</th>
                <th class="text-center">状态</th>
                <th class="text-center">创建时间</th>
            </tr>
            </thead>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td>
                        <p>姓名：<?=$item->nanny->real_name?></p>
                        <p>手机：<?=$item->nanny->phone?></p>
                    </td>
                    <td>
                        <p>姓名：<?=$item->intention->real_name?></p>
                        <p>手机：<?=$item->intention->phone?></p>
                    </td>
                    <td>
                        <p>项目标题：<?=$item->project->title?></p>
                        <p>咨询产品：<?=\app\models\Enum::getTypeNameByString($item->intention->type)?></p>
                        <p>留言备注：<?= $item->intention->remark?>
                        </p>
                    </td>
                    <td >
                        <?=$item->remark?>
                    </td>
                    <td class="text-center">
                        <?php
                            $arr = \app\models\Enum::$STATUS_TYPE;
                            echo $arr[$item->status];
                        ?>
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
