<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$cat = [
    1 => '添加路线',
    2 => '编辑路线',
];
$cat_id = Yii::$app->request->get('cat_id', 2);
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
$staticBaseUrl = Yii::$app->request->baseUrl . '/statics';

$this->params['page_navs'] = [
    [
        'name' => '添加路线',
        'active' => $cat_id == 1,
        'url' => $urlManager->createUrl(['mch/station/edit', 'cat_id' => 1,]),
    ],
    [
        'name' => '编辑路线',
        'active' => $cat_id == 2,
        'url' => $urlManager->createUrl(['mch/station/edit', 'cat_id' => 2,]),
    ],
];
?>

<script src="<?= $staticBaseUrl ?>/mch/js/uploadVideo.js"></script>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title?></div>
    <div class="panel-body">
        <form class="auto-form" method="post"  return="<?= $urlManager->createUrl(['mch/station/edit','cat_id'=>$cat_id,'id'=>$model->id]) ?>">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">用户</label>
                </div>
                <div class="col-sm-6">
                  <select class="form-control" id="sss" name="user_id">

                        <?php if ($model->user_id != null) : ?>
                            <option value="<?= $nickname->id ?>"><?= $nickname->nickname ?></option>
                        <?php else : ?>
                            <option value="">请选择人</option>
                        <?php endif;?>

                        <?php foreach ($user as $p) : ?>
                            <option value="<?= $p->id ?>"><?= $p-> nickname ?></option>
                        <?php endforeach;?>
                    </select>

                </div>
            </div>


            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">站点地址</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="name" value="<?=$model->name?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">站点经度</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="lng" value="<?= $model->lng ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">站点纬度</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="lat" value="<?= $model->lat ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">序号</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="sort" value="<?= $model->sort ?>">
                </div>
            </div>

            <?php if ($cat_id == 2) : ?>
                <div class="form-group row">
                    <div class="form-group-label col-sm-2 text-right">
                        <label class="col-form-label">添加时间</label>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-control" ><?= date('Y-m-d,H:i',$model->create_time) ?></div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                </div>
            </div>
        </form>
    </div>
</div>








