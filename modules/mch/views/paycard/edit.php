<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$cat = [
    1 => '打卡设置',
];
$cat_id = Yii::$app->request->get('cat_id', 2);
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
$staticBaseUrl = Yii::$app->request->baseUrl . '/statics';

$this->params['page_navs'] = [
    [
        'name' => '打卡设置',
        'active' => $cat_id == 1,
        'url' => $urlManager->createUrl(['mch/paycard/edit', 'cat_id' => 1,]),
    ],
];
?>

<script src="<?= $staticBaseUrl ?>/mch/js/uploadVideo.js"></script>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title?></div>
    <div class="panel-body">
        <form class="auto-form" method="post"  return="<?= $urlManager->createUrl(['mch/paycard/edit','cat_id'=>$cat_id,'id'=>$model['id']]) ?>">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">打卡距离</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="distance" value="<?=$model['distance']?>">
                </div>
            </div>
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








