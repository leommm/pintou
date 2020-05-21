<?php


defined('YII_ENV') or exit('Access Denied');

use app\models\Option;

$this->title = '账户登录';
$logo = Option::get('logo', 0, 'admin', null);
$logo = $logo ? $logo : Yii::$app->request->baseUrl . '/statics/admin/images/logo.png';
$copyright = Option::get('copyright', 0, 'admin');
$copyright = $copyright ? $copyright : '©2018 <a href="http://tt.tryine.com" target="_blank">CSHOP</a>';
$passport_bg = Option::get('passport_bg', 0, 'admin', Yii::$app->request->baseUrl . '/statics/admin/images/passport-bg.jpg');
$open_register = Option::get('open_register', 0, 'admin', false);
?>

<form method="post" class="auto-submit-form" return="<?= Yii::$app->request->get('return_url') ?>">
		<div class="top_logo"></div>
        <div class="login">
			<div class="login_box">
				<h2><img src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/logo.png" alt="E-SHOP"></h2>
				<div class="usname">
					<span><img src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/ico_01.png" align="usname"></span>
					<input name="username" placeholder="请输入用户名">
				</div>
				<div class="usname">
					<span><img src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/ico_02.png" alt="pwd"></span>
					<input name="password" placeholder="请输入密码" type="password">
				</div>
				<div class="usname" style="position: relative">
					<span><img src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/ico_02.png" alt="yzm"></span>
						<input name="captcha_code" placeholder="图片验证码">
						<img class="refresh-captcha"
							 data-refresh="<?= Yii::$app->urlManager->createUrl(['admin/passport/captcha', 'refresh' => 1,]) ?>"
							 src="<?= Yii::$app->urlManager->createUrl(['admin/passport/captcha',]) ?>"
							 style="height: 33px;width: 80px;cursor: pointer; position: absolute; right: 5px; top: 8px; border-radius: 3px;" title="点击刷新验证码">
					
				</div>
				<div>
					<button class="btn btn-block btn-primary submit-btn mb-3">登录</button>
				</div>
				
			</div>
			
			<div class="bg_color">
				<ul>
					<li><img src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/login_01.jpg" alt="背景图1"></li>
					<li><img src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/login_02.jpg" alt="背景图2"></li>
					<li><img src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/login_03.jpg" alt="背景图3"></li>
				</ul>
			</div>
			
        </div>
    </form>

<div class="footer">
    <div class="text-center copyright"><?= $copyright ?></div>
</div>

<script>


	$(function(){
		var i = 0
		var size = $('.bg_color li').size();
		
		function fadeIn(){
			i++;
			if(i==size)
			{
				i=0
			}
			$('.bg_color li').eq(i).fadeIn(1000).siblings().fadeOut(1000);
		}
		$('.bg_color li').eq(0).show().siblings().hide();
		
		setInterval(fadeIn,5000)
	})

</script>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            admin_list: [],
        },
    });
    $(document).on('click', '.refresh-captcha', function () {
        var img = $(this);
        var refresh_url = img.attr('data-refresh');
        $.ajax({
            url: refresh_url,
            dataType: 'json',
            success: function (res) {
                img.attr('src', res.url);
            }
        });
    });

    $(document).on('click', '.send-sms-code', function () {
        var form = document.getElementById('send_sms_code_form');
        var mobile = form.mobile.value;
        var captcha_code = form.captcha_code.value;
        var btn = $(this);
        btn.btnLoading();
        $('.send-sms-code-error').html('').hide();
        $.ajax({
            url: form.action,
            type: 'post',
            dataType: 'json',
            data: {
                mobile: mobile,
                captcha_code: captcha_code,
                _csrf: _csrf,
            },
            complete: function () {
                btn.btnReset();
            },
            success: function (res) {
                if (res.code == 1) {
                    $('.send-sms-code-error').html(res.msg).show();
                }
                if (res.code == 0) {
                    $('#send_sms_code_form').hide();
                    $('#reset_password_form').show();
                    app.admin_list = res.data.admin_list;
                }
            },
        });
    });

    $(document).on('click', '.reset-password', function () {
        var form = document.getElementById('reset_password_form');
        var admin_id = form.admin_id.value;
        var sms_code = form.sms_code.value;
        var password = form.password.value;
        var password2 = form.password2.value;
        if (password.length < 6) {
            $('.reset-password-error').html('密码长度不能低于6位。').show();
            return false;
        }
        if (password != password2) {
            $('.reset-password-error').html('两次输入的密码不一致。').show();
            return false;
        }
        var btn = $(this);
        btn.btnLoading();
        $('.reset-password-error').html('').hide();
        $.ajax({
            url: form.action,
            type: 'post',
            dataType: 'json',
            data: {
                admin_id: admin_id,
                sms_code: sms_code,
                password: password,
                _csrf: _csrf,
            },
            complete: function () {
                btn.btnReset();
            },
            success: function (res) {
                if (res.code == 1) {
                    $('.reset-password-error').html(res.msg).show();
                }
                if (res.code == 0) {
                    $('#resetPassword').hide();
                    $.myAlert({
                        content: res.msg,
                        confirm: function () {
                            location.reload();
                        }
                    });
                }
            },
        });
    });

</script>
