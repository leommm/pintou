<?php
defined('YII_ENV') or exit('Access Denied');

$urlManager = Yii::$app->urlManager;
$this->title = '系统设置';
?>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form method="post" class="auto-form" action="<?php echo $urlManager->createUrl(['/mch/store/system-setting'])?>">

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">认证会员是否需审核</label>
                </div>
                <div class="col-sm-5">
                    <select class="form-control" name="condition">
                        <option value="1" <?= $model->condition==1? 'selected':'' ?>>是</option>
                        <option value="0" <?= $model->condition==0? 'selected':''?>>否</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">一级车位佣金（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="first_parking"
                           value="<?= $model->first_parking ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">一级公寓佣金（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="first_flats"
                           value="<?= $model->first_flats ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">一级商铺佣金（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="first_shop"
                           value="<?= $model->first_shop ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">二级佣金（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="second"
                           value="<?= $model->second ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">保姆佣金（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="nanny_commission"
                           value="<?= $model->nanny_commission ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">城市级佣金（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="city_commission"
                           value="<?= $model->city_commission ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">A账户1-3年返利（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="account_a_1"
                           value="<?=$model->account_a_1?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">A账户4-6年返利（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="account_a_2"
                           value="<?=$model->account_a_2?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">A账户7-10年返利（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="account_a_3"
                           value="<?=$model->account_a_3?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">B账户1-3年返利（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="account_b_1"
                           value="<?=$model->account_b_1?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">B账户4-6年返利（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="account_b_2"
                           value="<?=$model->account_b_2?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                    <label class="col-form-label required">B账户7-10年返利（%）</label>
                </div>
                <div class="col-sm-5">
                    <input class="form-control" type="number" name="account_b_3"
                           value="<?=$model->account_b_3?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-4 text-right">
                </div>
                <div class="col-sm-5">
                    <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                </div>
            </div>

        </form>
    </div>
</div>