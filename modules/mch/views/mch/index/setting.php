<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/12/28
 * Time: 15:53
 */
$this->title = '多商户设置';
$url_manager = Yii::$app->urlManager;
?>
<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
    </div>
    <div class="panel-body">
        <form class="auto-form" method="post">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">提现方式</label>
                </div>
                <div class="col-sm-6">
                    <label class="checkbox-label">
                        <input type="checkbox" name="type[]"
                               value="1" <?= in_array(1, $model['type']) ? 'checked' : '' ?>>
                        <span class="label-icon"></span>
                        <span class="label-text">微信支付</span>
                    </label>
                    
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">入驻协议</label>
                </div>
                <div class="col-sm-6">
                    <textarea class="form-control" name="entry_rules" rows="8"><?= $model['entry_rules'] ?></textarea>
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
<script>
</script>