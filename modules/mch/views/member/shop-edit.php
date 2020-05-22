<?php
defined('YII_ENV') or exit('Access Denied');

$urlManager = Yii::$app->urlManager;
$this->title = '商户编辑';
?>

<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="auto-form" method="post" style="display: inline-block;width: 45%;" return="<?= $urlManager->createUrl(['mch/member/shop-list'])?> ">
            <div class="form-group row">
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">姓名</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" name="real_name" value="<?= $model->real_name ?>">
                </div>
            </div>
            <div class="form-group row" >
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">电话</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" name="phone" value="<?= $model->phone ?>">
                </div>
            </div>

            <div class="form-group row" >
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">微信</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" name="wechat" value="<?= $model->wechat ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">银行卡号</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" name="bank_card" value="<?= $model->bank_card ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">身份证号</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" name="id_card" value="<?= $model->id_card ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">店铺名称</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" name="shop_name" value="<?= $model->shop_name ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">店铺类型</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" name="shop_type" value="<?= $model->shop_type ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">店铺地址</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" name="shop_address" value="<?= $model->shop_address ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-3 text-right">
                    <label class="col-form-label required">营业执照</label>
                </div>
                <div class="col-sm-9">
                    <div class="upload-group">
                        <div class="input-group">
                            <input class="form-control file-input" name="license" value="<?= $model->license ?>">
                            <span class="input-group-btn">
                                <a class="btn btn-secondary upload-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="上传文件">
                                    <span class="iconfont icon-cloudupload"></span>
                                </a>
                            </span>
                            <span class="input-group-btn">
                                <a class="btn btn-secondary select-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="从文件库选择">
                                    <span class="iconfont icon-viewmodule"></span>
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
                            <img class="upload-preview-img" src="<?= $model->license ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-3 text-right">
                </div>
                <div class="col-sm-9">
                    <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                </div>
            </div>
        </form>

        <div style="display: inline-block;vertical-align: top;width: 35%">
            <div class="form-group row map">
                <div class="offset-2 col-9">
                    <div class="input-group" style="margin-top: 20px;">
                        <input class="form-control region" type="text" placeholder="城市">
                        <span class="input-group-addon ">和</span>
                        <input class="form-control keyword" type="text" placeholder="关键字">
                        <a class="input-group-addon search" href="javascript:">搜索</a>
                    </div>
                    <div class="text-info">搜索时城市和关键字必填</div>
                    <div class="text-info">点击地图上的蓝色点，获取经纬度</div>
                    <div class="text-danger map-error mb-3" style="display: none">错误信息</div>
                    <div id="container" style="min-width:600px;min-height:600px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=QOLBZ-3OUW4-O7TUZ-XTDQA-GUCBZ-YRBCV"></script>
<script>
    var searchService, map, markers = [];
    //        window.onload = function(){
    //直接加载地图
    //初始化地图函数  自定义函数名init
    function init() {
        //定义map变量 调用 qq.maps.Map() 构造函数   获取地图显示容器
        var map = new qq.maps.Map(document.getElementById("container"), {
            center: new qq.maps.LatLng(39.916527, 116.397128),      // 地图的中心地理坐标。
            zoom: 15                                                 // 地图的中心地理坐标。
        });
        var latlngBounds = new qq.maps.LatLngBounds();
        //调用Poi检索类
        searchService = new qq.maps.SearchService({
            complete: function (results) {
                var pois = results.detail.pois;
                $('.map-error').hide();
                if (!pois) {
                    $('.map-error').show().html('关键字搜索不到，请重新输入');
                    return;
                }
                for (var i = 0, l = pois.length; i < l; i++) {
                    (function (n) {
                        var poi = pois[n];
                        latlngBounds.extend(poi.latLng);
                        var marker = new qq.maps.Marker({
                            map: map,
                            position: poi.latLng,
                        });

                        marker.setTitle(n + 1);

                        markers.push(marker);
                        //添加监听事件
                        qq.maps.event.addListener(marker, 'click', function (e) {
                            var address = poi.address;
                            $("input[name='shop_address']").val(address);
                        });
                    })(i);
                }
                map.fitBounds(latlngBounds);
            }
        });
    }
    //清除地图上的marker
    function clearOverlays(overlays) {
        var overlay;
        while (overlay = overlays.pop()) {
            overlay.setMap(null);
        }
    }
    function searchKeyword() {
        var keyword = $(".keyword").val();
        var region = $(".region").val();
        clearOverlays(markers);
        searchService.setLocation(region);
        searchService.search(keyword);
    }

    //调用初始化函数地图
    init();
</script>

<script>
    $(document).on('click', '.search', function () {
        searchKeyword();
    })
</script>