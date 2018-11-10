<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:93:"/Users/shilinqing/GitHub/lucky/public/../addons/config/user/view/default/parameter/index.html";i:1541827347;s:83:"/Users/shilinqing/GitHub/lucky/public/../web/user/view/default/base/popup_form.html";i:1541827348;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>      
        <link rel="stylesheet" type="text/css" href="__STATIC__/web-icons/css.css" />   
        <link rel="stylesheet" type="text/css" href="__CSS__/style.css" />            
        <script type="text/javascript" src="__STATIC__/jquery/jquery.min.js"></script>        
    </head>
    <body style="background-color:#fff">    
        <script type="text/javascript" src="__STATIC__/layer/layer.js"></script>
        <script type="text/javascript" src="__JS__/common.js"></script>
        <script type="text/javascript" src="__STATIC__/jquery/jquery.form.ui.js"></script>
        <script type="text/javascript" src="__STATIC__/jquery/jquery.form.js"></script>                              
        <script type="text/javascript">
            function getURL(action, param, addon) {
                var m = "<?php echo MODULE_NAME; ?>";
                var c = "<?php echo $_CONTROLLER_NAME; ?>";
                var a = "<?php echo $_ADDON_NAME; ?>";
                var arr = action.split('/');
                var url = "";
                if (arr.length == 3) {
                    url = action;
                } else if (arr.length == 2) {
                    url = "/" + m + "/" + action;
                } else {
                    url = "/" + m + "/" + c + "/" + action;
                }
                if (addon != null)
                    a = addon;
                if (a != "")
                    url += "/addon/" + a;
                if (param != null && param != "") {
                    var ref = "";
                    if (typeof param === 'string') {
                        ref = param;
                    } else if (typeof param === 'object') {
                        for (var key in param) {
                            if (ref != "")
                                ref += "&";
                            ref += key + "=" + param[key];
                        }
                    }
                    if (ref != "") {
                        if (url.indexOf("?") == -1)
                            url += "?";
                        else
                            url += "&";
                        url += ref;
                    }
                }
                return url;
            }
            var layer_div = null;
            var form = null;
            var id = "<?php echo $id; ?>";
            var is_add = false;//是否是添加状态
            var loadDataAction = "<?php echo $loadDataAction; ?>";
            $(function () {
                form = $(".ui-form").bindForm();
                var layui_iframe = $(".layui-layer-iframe", parent.document);
                layer_div = layui_iframe.eq(layui_iframe.length - 1);
                if (id == "" || id == 0) {
                    id = "";
                    is_add = true;
                }
                if (id != "" && parseInt(id) > 0 && loadDataAction != "") {;
                    loadFormData();
                }
                layer_div.find(".js_layui-layer-btn_wrap").show();
            });
            $(window).resize(function () {
                setSideNavHeight(null);
                resizeGridHeight(null);
            });
            function loadFormData() {
                var url = getURL(loadDataAction, "id=" + id);
                form.load(url, {
                    onBeforeLoad: function () {
                        showLoading("数据加载中...");
                    },
                    onLoadSuccess: function (data) {
                        if (typeof (onLoadDataSuccess) != "undefined")
                            onLoadDataSuccess(data);
                        hideLoading();
                    }
                });
            }
            var _isClickSave = false;
            function saveData(callback, layer_index, autoClose) {
                if (_isClickSave)
                    return;
                var _this = this;
                if (typeof (chkForm) != 'undefined') {
                    if (!chkForm())
                        return false;
                }
                var f = form.valid();
                if (f) {
                    _isClickSave = true;
                    form.ajaxSubmit({
                        beforeSubmit: function () {
                            showLoading("数据保存中...");
                        },
                        success: function (res) {
                            _isClickSave = false;
                            hideLoading();
                            if (!res.success) {
                                if (typeof (failCallback) != "undefined")
                                    failCallback(res);
                                else
                                    alert(res.message);
                                return;
                            }
                            //保存成功后回调方法
                            if (typeof (successCallback) != "undefined")
                                successCallback(res);
                            if (typeof (callback) != "undefined")
                                callback.call(_this, res.data);
                            if (autoClose)
                                _this.close(layer_index);
                            else {
                                var _$order_index_obj = $("#order_index");
                                var _$order_index = -1;
                                if (_$order_index_obj.length > 0)
                                    _$order_index = parseInt(_$order_index_obj.val()) + 1;
                                form.reset();
                                if (_$order_index_obj.length > 0)
                                    _$order_index_obj.val(_$order_index);
                                if (typeof (reloadForm) != 'undefined') {
                                    reloadForm();
                                }
                            }
                        },
                        error: function (xhr, status, errMsg) {
                            _isClickSave = false;
                            hideLoading("数据保存中...");
                            msg("保存失败！" + errMsg);
                        }
                    });
                }
            }
            function getOkBtn() {
                if (layer_div)
                    return layer_div.find('.layui-layer-btn0');
                else
                    return $;
            }
            function getOkCloseBtn() {
                if (layer_div)
                    return layer_div.find('.layui-layer-btn1');
                else
                    return $;
            }
            function getCancelBtn() {
                if (layer_div)
                    return layer_div.find('.layui-layer-btn2');
                else
                    return $;
            }
            var main_header_height = 0;
            function getGridHeight() {
                var win_height = $(window).height();
                main_header_height = $("#js_main_header").outerHeight(true);
                if (!main_header_height)
                    main_header_height = 0;
                var height = win_height - main_header_height - 10;
                return height;
            }
            function resizeGridHeight(height) {
                if ($('#grid-table').length > 0) {
                    if (!height)
                        height = getGridHeight();
                    $('#grid-table').datagrid('resize', {
                        height: height
                    });
                }
            }
            function setSideNavHeight(height) {
                var _$js_side_content = $("#js_side_content");
                if (_$js_side_content.length > 0) {
                    if (!height) {
                        height = $(window).height();
                        var _$js_nav_title = _$js_side_content.find(".js_nav_title");
                        var len = _$js_nav_title.length;
                        if (len > 0) {
                            var nav_title_height = _$js_nav_title.eq(0).outerHeight(true);
                            height = (height - nav_title_height * len) / len;
                        }
                    }
                    _$js_side_content.find(".js_sidebar_nav").height(height);
                }
            }
            $("#js_side_content").on("click", "li", function () {
                $(this).parent().parent().parent().find("li").removeClass("active");
                $(this).addClass("active");
                if (typeof clickSideNav != 'undefined') {
                    clickSideNav.call(this, $(this).attr("data-id"), $(this).attr("data-data"));
                }
            });
            /*左侧导航栏显示隐藏功能*/
            $("#js_side_content").on("click", ".subNav", function () {
                /*显示*/
                if ($(this).find("span:first-child").attr('class') == "title-icon icon wb-triangle-right") {
                    $(this).find("span:first-child").removeClass("icon wb-triangle-right");
                    $(this).find("span:first-child").addClass("icon wb-triangle-down");
                }
                /*隐藏*/
                else {
                    $(this).find("span:first-child").removeClass("icon wb-triangle-down");
                    $(this).find("span:first-child").addClass("icon wb-triangle-right");
                }
                // 修改数字控制速度， slideUp(500)控制卷起速度
                $(this).next(".navContent").slideToggle(300).siblings(".navContent").slideUp(300);
            });
        </script>
        <form id="form1" class="ui-form" method="post">
            
<input type="hidden" id="id" name="id" />
<input type="hidden" id="json" name="json"  value=""  />
<div class="box-content">
    <div class="control-row col-2">
        <?php if(is_array($param_list) || $param_list instanceof \think\Collection || $param_list instanceof \think\Paginator): $i = 0; $__LIST__ = $param_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['control_type'] == 'select'): ?>
            <div class="control-group">
                <label class="control-label"><?php echo $vo['title']; ?></label>
                <select name="<?php echo $vo['field_name']; ?>" class="required form-control" style='width:100px'>                
                    <option value="0" <?php if($vo['parameter_val'] == 0): ?>selected<?php endif; ?>>否</option>
                    <option value="1" <?php if($vo['parameter_val'] == 1): ?>selected<?php endif; ?>>是</option>
                </select> 
            </div>
            
            <?php elseif($vo['control_type'] == 'number'): ?>
            <div class="control-group">
                <label class="control-label"><?php echo $vo['title']; ?></label>
                <input type="text" value="<?php echo $vo['parameter_val']; ?>" name="<?php echo $vo['field_name']; ?>" class="form-control" /><span class="tips"><?php echo $vo['remark']; ?></span>
            </div>
                        
            <?php elseif($vo['control_type'] == 'text'): ?>
            <div class="control-group">
                <label class="control-label"><?php echo $vo['title']; ?></label>
                <input type="text" value="<?php echo $vo['parameter_val']; ?>" name="<?php echo $vo['field_name']; ?>" class="form-control" /><span class="tips"><?php echo $vo['remark']; ?></span>
            </div>
            
            <?php elseif($vo['control_type'] == 'password'): ?>
            <div class="control-group">
                <label class="control-label"><?php echo $vo['title']; ?></label>
                <input type="password" value="<?php echo $vo['parameter_val']; ?>" name="<?php echo $vo['field_name']; ?>" class="form-control" /><span class="tips"><?php echo $vo['remark']; ?></span>
            </div>
            <?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>

        </form>
        
<script type="text/javascript">
function chkForm() {
    var data = {};
    $("input[type='text']").each(function () {
        var name = $(this)[0].name;
        if(name != ''){
            data[name] = $(this).val();
        }
    });
    $("select").each(function () {
        var name = $(this)[0].name;
        var val = $(this).val();
        data[name] = val;
    });
//    console.log(data)
    $("#json").val(JSON.stringify(data));
    return true;
}
</script>

    </body>    
</html>