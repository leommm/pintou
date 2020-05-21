<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$cat = [
    1 => '我要买',
    2 => '我要卖',
    3 => '我要服务',
];
$cat_id = Yii::$app->request->get('cat_id', 2);
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
$returnUrl = Yii::$app->request->referrer;
if (!$returnUrl) {
    $returnUrl = $urlManager->createUrl(['mch/article/index', 'cat_id' => $cat_id]);
}
$this->params[''] = [
    [
        'name' => '我要买',
        'active' => $cat_id == 1,
        'url' => $urlManager->createUrl(['mch/publish/index', 'cat_id' => 1,]),
    ],
    [
        'name' => '我要卖',
        'active' => $cat_id == 2,
        'url' => $urlManager->createUrl(['mch/publish/index', 'cat_id' => 2,]),
    ],
    [
        'name' => '我要服务',
        'active' => $cat_id == 3,
        'url' => $urlManager->createUrl(['mch/publish/index', 'cat_id' => 3,]),
    ],
];
?>

<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="auto-form" method="post"  action="">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">标题</label>
                </div>
                <div class="col-sm-6">
                    <div class="form-control cat-name"><?= $model->title ?></div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">分类</label>
                </div>
                <div class="col-sm-6">
                    <div class="form-control cat-name">
                        <?php if ($model->type == 1) : ?>我要买<?php endif; ?>
                        <?php if ($model->type == 2) : ?>我要卖<?php endif; ?>
                        <?php if ($model->type == 3) : ?>我要服务<?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">简介</label>
                </div>
                <div class="col-sm-6">
                    <div class="form-control cat-name"><?= $model->content?></div>

                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">图片</label>
                </div>
                <div class="col-sm-6">
                    <div class="form-control cat-name"><?= $model->image?></div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">站点名</label>
                </div>
                <div class="col-sm-6">
                    <div class="form-control cat-name"><?= $model->site?></div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">发布时间</label>
                </div>
                <div class="col-sm-6">
                    <div class="form-control cat-name"><?= date('Y-m-d,H:i:s',$model->create_time )?></div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">状态</label>
                </div>
                <div class="col-sm-3">

                        <select class="form-control" name="state">
                            <option value="1" <?= $user->state == 1 ? "selected" : "" ?>>已接受</option>
                            <option value="2" <?= $user->state == 2 ? "selected" : "" ?>>未接受</option>
                            <option value="3" <?= $user->state == 3 ? "selected" : "" ?>>处理中</option>
                            <option value="4" <?= $user->state == 4 ? "selected" : "" ?>>已完成</option>
                        </select>

                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                </div>
                <div class="col-sm-6">
                    <input type="submit"  class="btn btn-primary auto-form-btn" >

                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?= Yii::$app->request->baseUrl ?>/statics/ueditor/ueditor.config.js"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/statics/ueditor/ueditor.all.min.js"></script>
<script>

</script>