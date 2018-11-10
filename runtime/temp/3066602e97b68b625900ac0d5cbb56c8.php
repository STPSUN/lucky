<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"/Users/shilinqing/GitHub/lucky/public/../web/user/view/default/login/index.html";i:1541827348;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>管理员登陆</title>         
    
        <link rel="stylesheet" type="text/css" href="__CSS__/login.css" />             
        <link rel="stylesheet" type="text/css" href="__STATIC__/vegas/vegas.min.css" />            
    </head>
    <body>
        <div class="form-wrap">
            <div class="login">
                <form id="form" method="post">
                    <h1>Welcome Home!</h1>
                    <label for="loginName">Username / 用户名</label>
                    <input type="text" name="username" id="username" value="" placeholder="Enter your account">
                    <label for="loginPassword">Password / 密码</label>
                    <input type="password" name="password" id="password" value=""  placeholder="Enter your password">
                    <label for="loginPassword">VerifyCode / 验证码</label>
                    <input type="text" name="code" maxlength="4" id="code" placeholder="请输入验证码">
                    <div class="loginButton" id="loginBtn">Log in / 登录</div>
                    <div class="verify-code">
                        <img src="<?php echo captcha_src(); ?>" id="codeImg" alt="验证码"  style="margin-top: 10px"/>
                    </div>
                    <span class="error">错误提示:</span>
                </form>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="__STATIC__/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="__STATIC__/layer/layer.js"></script>
    <script type="text/javascript" src="__STATIC__/js/common.js"></script>
    <script type="text/javascript" src="__STATIC__/jquery/jquery.form.js"></script>        
    <script type="text/javascript" src="__STATIC__/jquery/jquery.cookie.js"></script> 
    <script type="text/javascript" src="__STATIC__/vegas/vegas.min.js"></script>
    <script type='text/javascript'>
        $("body").vegas({
           slides:[
               {src:"__STATIC__/vegas/images/1.jpg"},
               {src:"__STATIC__/vegas/images/2.jpg"},
               {src:"__STATIC__/vegas/images/3.jpg"},
           ],
           preload:false,//预先加载图像和视频以启动
//           preload-Image:true, // Preload images at start. preload must be false .
           timer:true, //显示/隐藏定时器
           shuffle:false, //随机
           delay:5000,//幻灯片之间的延迟时间
           transition: 'fade',
           
        });
        $(function () {
            var winHeight = $(window).height();
            var headHeight = $("#js_header").outerHeight();
            $("#js_main").height(winHeight - headHeight);
//            if ($.cookie("rmbUser") == "true") {
//                $("#rmbUser").attr("checked", true);
//                $("#username").val($.cookie("username"));
//            }
        });
        var flag = true;
        $("#loginBtn").click(function () {
            flag = true;
            submitForm();
        });
        function submitForm() {
            var f = chkForm();
            if (f) {
                $("#form").ajaxSubmit({
                    beforeSubmit: function () {
                        showLoading("登录中...");
                    },
                    success: function (res) {
                            if (res.success) {
//                                if ($("#rmbUser")[0].checked) {
//                                    var username = $("#username").val();
//                                    $.cookie("rmbUser", "true", {expires: 7}); // 存储一个带7天期限的 cookie
//                                    $.cookie("username", username, {expires: 7}); // 存储一个带7天期限的 cookie
//                                } else {
//                                    $.cookie("rmbUser", "false", {expires: -1});        // 删除 cookie
//                                    $.cookie("username", '', {expires: -1});
//                                }
                                console.log(res.data)
                                location.href = res.data;
                            } else {
                                flag = false;
                                refreshImg();
                            alert(res.message);
                        }
                    },
                    error: function (xhr, status, errMsg) {
                        flag = false;
                        msg("登录失败！" + errMsg);
                    }
                });
            }
        }
        function chkForm() {
            var username = $("#username");
            if (username.val() == "") {
                flag = false;
                msg("账号不能为空！");
                return false;
            }
            var password = $("#password");
            if (password.val() == "") {
                flag = false;
                msg("密码不能为空！");
                password.focus();
                return false;
            }
            var code = $("#code");
            if (code.val() == "") {
                flag = false;
                msg("验证码不能为空！");
                code.focus();
                return false;
            }
            return true;
        }
        $("#codeImg").click(function () {
            refreshImg();
        });
        function refreshImg() {
            var ts = Date.parse(new Date()) / 1000;
            $('#codeImg').attr("src", "/captcha?id=" + ts);
        }
        $(document).keypress(function (e) {
            // 回车键事件  
            if (e.which == 13) {
                submitForm();
            }
        });
    </script>
</html>