<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/6/19
 * Time: 16:52
 */
use \app\models\User;

$urlManager = Yii::$app->urlManager;
$this->title = '用户管理';
$this->params['active_nav_group'] = 4;
?>

<div class="panel mb-3" id="app">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <div class="float-right mb-4">
            <form method="get">

                <?php $_s = ['keyword', 'page', 'per-page'] ?>
                <?php foreach ($_GET as $_gi => $_gv) :
                    if (in_array($_gi, $_s)) {
                        continue;
                    } ?>
                    <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
                <?php endforeach; ?>

                <div class="input-group">
                    <input class="form-control"
                           placeholder="微信昵称"
                           name="keyword"
                           autocomplete="off"
                           value="<?= isset($_GET['keyword']) ? trim($_GET['keyword']) : null ?>">
                    <span class="input-group-btn">
                    <button class="btn btn-primary">搜索</button>
                </span>
                </div>
            </form>
        </div>
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>头像</th>
                <th>昵称</th>
                <th>绑定手机号</th>
                <th>联系方式</th>
                <th>备注</th>
                <th>加入时间</th>
                <th>优惠券数量</th>
                <th>当前积分</th>
                <th>操作</th>

            </tr>
            </thead>
            <?php foreach ($list as $u) : ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td>
                        <img src="<?= $u['avatar_url'] ?>" style="width: 34px;height: 34px;margin: -.6rem 0;">
                    </td>
                    <td><?= $u['nickname']; ?><br><?= $u['wechat_open_id'] ?></td>
                    <td><?= $u['binding']; ?></td>
                    <td><?= $u['contact_way']; ?></td>
                    <td><?= $u['comments']; ?></td>
                    <td><?= date('Y-m-d H:i:s', $u['addtime']) ?></td>
                    <td>
                        <a class="btn btn-sm btn-link"
                           href="<?= $urlManager->createUrl(['mch/user/coupon', 'user_id' => $u['id']]) ?>"><?= $u['coupon_count'] ?></a>
                    </td>
                    <td>
                        <a class="btn btn-sm btn-link"
                           href="<?= $urlManager->createUrl(['mch/user/rechange-log', 'user_id' => $u['id']]) ?>"><?= $u['integral'] ?></a>
                    </td>

                    <td>
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/user/edit', 'id' => $u['id']]) ?>">编辑</a>
                        <a class="btn btn-sm btn-success rechangeBtn"
                           data-toggle="modal" data-target="#attrAddModal"
                           href="javascript:;"
                           data-integral="<?= $u['integral'] ?>"
                           data-id="<?= $u['id'] ?>">充值积分</a>
                    </td>
                    <!--
                <td>
                    <a class="btn btn-sm btn-danger del" href="javascript:"
                       data-url="<?= $urlManager->createUrl(['mch/user/del', 'id' => $u['id']]) ?>"
                       data-content="是否删除？">删除</a>
                </td>
                -->
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="text-center">
            <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination,]) ?>
            <div class="text-muted"><?= $row_count ?>条数据</div>
        </div>
    </div>
    <!-- 充值积分 -->
    <div class="modal fade" id="attrAddModal" data-backdrop="static">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">充值积分</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group short-row">
                        <label class="custom-control custom-radio">
                            <input value="1" checked name="rechangeType" type="radio" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">充值</span>
                        </label>
                        <label class="custom-control custom-radio">
                            <input value="2" name="rechangeType" type="radio"
                                   class="custom-control-input integral-reduce">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">扣除</span>
                        </label>
                    </div>

                    <input class="form-control" id="integral" placeholder="请填写充值积分" value="0">
                    <input type="hidden" id="user_id" value="">
                    <div class="form-error text-danger mt-3 rechange-error" style="display: none">ddd</div>
                    <div class="form-success text-success mt-3" style="display: none">sss</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary save-rechange">提交</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 充值余额 -->
    <div class="modal fade" id="balanceAddModal" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">充值余额</h5>
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="form-group-label col-3 text-right">
                            <label class="col-form-label">操作</label>
                        </div>
                        <div class="input-group short-row col-9">
                            <label class="custom-control custom-radio">
                                <input value="1" checked name="rechargeType" type="radio" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">充值</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input value="2" name="rechargeType" type="radio" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">扣除</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group-label col-3 text-right">
                            <label class="col-form-label">金额</label>
                        </div>
                        <div class="col-9">
                            <input class="form-control money" type="number" placeholder="请填写充值余额" value="0" v-model="money">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group-label col-3 text-right">
                            <label class="col-form-label">充值图片</label>
                        </div>
                        <div class="col-9">
                            <div class="upload-group">
                                <div class="input-group">
                                    <input class="form-control file-input" name="pic_url" value="">
                            <span class="input-group-btn">
                                <a class="btn btn-secondary upload-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="上传文件">
                                    <span class="iconfont icon-cloudupload"></span>
                                </a>
                            </span>
                            <span class="input-group-btn">
                                <a class="btn btn-secondary delete-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="删除文件">
                                    <span class="iconfont icon-close"></span>
                                </a>
                            </span>
                                </div>
                                <div class="upload-preview text-center upload-preview">
                                    <span class="upload-preview-tip">100&times;100</span>
                                    <img class="upload-preview-img" src="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group-label col-3 text-right">
                            <label class="col-form-label">说明</label>
                        </div>
                        <div class="col-9">
                            <input class="form-control" name="explain">
                        </div>
                    </div>
                    <div class="form-error text-danger mt-3 money-error" style="display: none"></div>
                    <div class="form-success text-success mt-3" style="display: none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary save-balance">提交</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.del', function () {
        var a = $(this);
        $.myConfirm({
            content: a.data('content'),
            confirm: function () {
                $.ajax({
                    url: a.data('url'),
                    type: 'get',
                    dataType: 'json',
                    success: function (res) {
                        if (res.code == 0) {
                            window.location.reload();
                        } else {
                            $.myAlert({
                                title: res.msg
                            });
                        }
                    }
                });
            }
        });
        return false;
    });
    $(document).on('click', '.rechangeBtn', function () {
        var a = $(this);
        var id = a.data('id');
        var integral = a.data('integral');
        $('#user_id').val(id);
        $('.integral-reduce').attr('data-integral', integral);
    });
    $(document).on('change', '.integral-reduce', function () {
        $('#integral').val($(this).data('integral'));
    });
    $(document).on('click', '.save-rechange', function () {
        var user_id = $('#user_id').val();
        var integral = $('#integral').val();
        var oldIntegral = $('.integral-reduce').data('integral');
        var rechangeType = $("input[type='radio']:checked").val();
        var btn = $(this);
        btn.btnLoading(btn.text());
        if (rechangeType == '2') {
            if (integral > oldIntegral) {
                $('.rechange-error').css('display', 'block');
                $('.rechange-error').text('当前用户积分不足');
                return;
            }
        }
        if (!integral || integral <= 0) {
            $('.rechange-error').css('display', 'block');
            $('.rechange-error').text('请填写积分');
            return;
        }
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl(['mch/user/rechange']) ?>",
            type: 'post',
            dataType: 'json',
            data: {user_id: user_id, integral: integral, _csrf: _csrf, rechangeType: rechangeType},
            success: function (res) {
                if (res.code == 0) {
                    $("#attrAddModal").modal('hide');
                    $.myAlert({
                        content:res.msg,
                        confirm:function(res){
                            window.location.reload();
                        }
                    });
                } else {
                    $('.rechange-error').css('display', 'block');
                    $('.rechange-error').text(res.msg);
                }
            }
        });
    });

    var app = new Vue({
        el: '#app',
        data: {
            user_id: -1,
            price: 0,
            type: -1,
            rechargeType: 1,
            money: 0
        }
    });

    $(document).on('click', '.rechargeMoney', function () {
        app.type = 1;
        app.user_id = $(this).data('id');
    });

    $(document).on('change', "input[name='rechargeType']", function () {
        app.rechargeType = $(this).val();
    });

    $(document).on('click', '.close-modal', function () {
        app.user_id = -1;
        app.money = 0;
        app.price = 0;
        app.rechargeType = 1;
        app.type = -1;
    });

    $(document).on('click', '.save-balance', function () {
        var btn = $(this);
        btn.btnLoading(btn.text());
        var error = $('.money-error');
        $.ajax({
            url: "<?=$urlManager->createUrl(['mch/user/recharge-money'])?>",
            type: "post",
            dataType: 'json',
            data: {
                data: {
                    type: app.type,
                    user_id: app.user_id,
                    rechargeType: app.rechargeType,
                    money: app.money,
                    pic_url:$("input[name='pic_url']").val(),
                    explain:$("input[name='explain']").val(),

                },
                _csrf: _csrf
            },
            success: function (res) {
                if (res.code == 0) {
                    $("#balanceAddModal").modal('hide');
                    $.myAlert({
                        content:res.msg,
                        confirm:function(res){
                            window.location.reload();
                        }
                    });
                } else {
                    error.css('display', 'block');
                    error.text(res.msg);
                    btn.btnReset();
                }
            }
        });
    });

</script>
