<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:97:"/Users/shilinqing/GitHub/lucky/public/../addons/member/user/view/default/member/view_balance.html";i:1541835969;s:78:"/Users/shilinqing/GitHub/lucky/public/../web/user/view/default/base/popup.html";i:1541827348;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>      
        <link rel="stylesheet" type="text/css" href="__STATIC__/web-icons/css.css" />   
        <link rel="stylesheet" type="text/css" href="__CSS__/style.css?v=3" />                        
        <script type="text/javascript" src="__STATIC__/jquery/jquery.min.js?v=3"></script>       
        <style type="text/css">           
            .sidebar .nav_title{
                height:35px;
                line-height:35px;
            }
            .right-off {
                top:0;
                left: 160px;
            }
        </style>
    </head>    
    <body style="background-color:#fff">         
        
    <div class="box-content"> 
        <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$asset): $mod = ($i % 2 );++$i;?>
        <div class="control-group">
            <label class="control-label"><?php echo $asset['coin_name']; ?></label>
            <div class="controls">
                <botton class="btn btn-success"><?php echo $asset['amount']; ?></botton>                                      
            </div>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
        
        <script type="text/javascript" src="__STATIC__/layer/layer.js?v=3"></script>
        <script type="text/javascript" src="__JS__/common.js?v=3"></script>  
        <script type="text/javascript" src="__STATIC__/jquery/jquery.form.ui.js"></script>        
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
            function getOkBtn() {
                if (layer_iframe)
                    return layer_iframe.find('.layui-layer-btn0');
                else
                    return $;
            }
            function getOkCloseBtn() {
                if (layer_iframe)
                    return layer_iframe.find('.layui-layer-btn1');
                else
                    return $;
            }
            function getCancelBtn() {
                if (layer_iframe)
                    return layer_iframe.find('.layui-layer-btn2');
                else
                    return $;
            }
            var layer_iframe = null;
            var form = null;
            $(function () {
                form = $(".ui-form").ui().render();
                var layui_iframe = $(".layui-layer-iframe", parent.document);
                layer_iframe = layui_iframe.eq(layui_iframe.length - 1);
                //回车自动提交
                $('.search').keyup(function (event) {
                    if (event.keyCode === 13) {
                        $("#js_search").click();
                    }
                });
                layer_iframe.find(".js_layui-layer-btn_wrap").show();
            });
            $(window).resize(function () {
                setSideNavHeight(null);
                resizeGridHeight(null);
            });
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
        
<script type="text/javascript">
</script>

    </body>   
</html>