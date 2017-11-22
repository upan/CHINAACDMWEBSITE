<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <title><?php echo $system_name;?><?php echo isset($page_name) ? '-' . $page_name : '';?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.css');?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.css');?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom-styles.css');?>"/>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/layer/layer.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.metisMenu.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/common.js');?>"></script>
</head>
<style>
    body{ text-align:center}
    .login_form{
        margin:130px auto;
        text-align: left;
        width:420px;
    }
    button{
        width:100%;
    }
    .input-lg{
        height: 38px;
        font-size:14px;
    }

</style>

<body>
<div id="wrapper">
    <nav class="navbar navbar-default top-navbar" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="javascript:void(0);"><?php echo $system_name;?></a>
        </div>
    </nav>
    <!-- /. NAV SIDE  -->
    <form id="exec_form">
        <div class="login_form">
            <div class="panel">
                <div class="panel-body">
                    <div class="form-group">
                        <h3 id="login_msg">登录 <small>LOGIN</small></h3>
                    </div>
                    <hr />
                    <div class="form-group">
                        <input type="text" class="form-control input-lg" placeholder="输入手机号" name="mobile" id="mobile" />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control input-lg" placeholder="输入密码" name="password" id="password" />
                    </div>
                    <div class="form-group" style="text-align:right;">
                        <a href="javascript:void(0);" class="btn-sm">忘记密码</a>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-lg btn-login">登录</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $('#mobile').select();
    var LOGIN_EXEC_URL = '<?php echo base_url('account/operate/login_exec');?>';
    $(document).on('click','.btn-login', function(){
        login_exec();
    });
    $(document).keyup(function(event){
        if(event.keyCode ==13){
            login_exec();
        }
    });

    function login_exec(){
        if ($("#mobile").val().trim().length < 1) {
            layer.tips('请输入手机号', '#login_msg', {tips: [1, '#e23911']});
            $('#mobile').select();
            return false;
        }
        if (check_mobile($("#mobile").val()) == false) {
            layer.tips('手机号格式不正确', '#login_msg', {tips: [1, '#e23911']});
            $('#mobile').select();
            return false;
        }
        if ($("#password").val().trim().length < 1) {
            layer.tips('请输入密码', '#login_msg', {tips: [1, '#e23911']});
            $('#password').select();
            return false;
        }
        var msg_layer = layer.msg('登录中...', {icon: 16 ,shade: 0.4, time:0});
        $.post(LOGIN_EXEC_URL, $('#exec_form').serialize(), function(result){
            layer.close(msg_layer);
            var data = eval('(' + result + ')');
            if (data.success == 1){
                layer.closeAll();
                layer.msg('登录成功,正在跳转至首页...', {icon: 16 ,shade: 0.4, time:0});
                window.location.href = "<?php echo $system_index_url?>";
            } else {
                layer.tips(data.text, '#login_msg', {tips: [1, '#e23911']});
            }
        });
    }
</script>
</body>
</html>