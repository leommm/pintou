<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
$cat = [
    1 => '添加视频',
    2 => '编辑视频',
];
$cat_id = Yii::$app->request->get('cat_id', 2);
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
$staticBaseUrl = Yii::$app->request->baseUrl . '/statics';

$this->params['page_navs'] = [
    [
        'name' => '添加视频',
        'active' => $cat_id == 1,
        'url' => $urlManager->createUrl(['mch/video/edit', 'cat_id' => 1,]),
    ],
    [
        'name' => '编辑视频',
        'active' => $cat_id == 2,
        'url' => $urlManager->createUrl(['mch/video/edit', 'cat_id' => 2,]),
    ],
];
?>

<script src="<?= $staticBaseUrl ?>/mch/js/uploadVideo.js"></script>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title?></div>
    <div class="panel-body">
        <form class="auto-form" method="post"  return="<?= $urlManager->createUrl(['mch/video/edit','cat_id'=>$cat_id,'id'=>$model['id']]) ?>">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">标题</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="title" value="<?=$model['title']?>">
                </div>
            </div>


            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">排序</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="number" step="1" name="sort"
                           value="<?= $model['sort'] ? $model['sort'] : 100 ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">视频简介</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="content" value="<?= $model['content'] ?>">
                </div>
            </div>


            <div class="form-group row" >
                <div class="form-group-label col-sm-2 text-right">
                    <label class=" col-form-label">商品视频</label>
                </div>
                <div class="col-9">
                    <div class="video-picker" data-url="<?= $urlManager->createUrl(['upload/video']) ?>">
                        <div class="input-group short-row">
                            <input class="video-picker-input video form-control" name="url"
                                   value="<?= $model['url'] ?>" placeholder="请输入视频源地址或者选择上传视频">
                            <a href="javascript:" class="btn btn-secondary video-picker-btn">选择视频</a>
                        </div>
                        <a class="video-check"
                           href="<?= $model['url'] ? $model['url'] : "javascript:" ?>"
                           target="_blank">视频预览</a>

                        <div class="video-preview"></div>
                        <div>
                                    <span
                                            class="text-danger fs-sm">支持格式mp4;支持编码H.264;视频大小不能超过<?= \app\models\UploadForm::getMaxUploadSize() ?>
                                        MB</span></div>
                    </div>
                </div>
            </div>
            <?php if ($cat_id == 2) : ?>
                <div class="form-group row">
                    <div class="form-group-label col-sm-2 text-right">
                        <label class="col-form-label">添加时间</label>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-control" ><?= date('Y-m-d,H:i',$model['addtime']) ?></div>
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








