<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo SITE_NAME; ?> | Login</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="Robots" content="none">
  <meta name="Robots" content="noindex,nofollow">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/dist/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="assets/dist/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/AdminLTE.min.css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="javascript:;"><?php echo SITE_NAME; ?></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">欢迎来到管理后台</p>
    <form action="login" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="用户名" name="username" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="密码" name="password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8"></div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">登录</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
    <!-- /.social-auth-links -->
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<!-- jQuery 2.2.3 -->
<script src="assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<!-- bootstrapValidator -->
<script src="assets/plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
<script src="assets/plugins/bootstrapvalidator/zh_CN.js"></script>
<!-- layer.js -->
<script src="assets/plugins/layer/layer.js"></script>
<script src="assets/dist/js/core.js"></script>
<script>
$(function () {
    $("form").bootstrapValidator();
    <?php if(isset($status)):?>
        var status = "<?php echo $status?>" == "success";
        layer.msg("<?php echo $msg;?>", {icon: status ? 6 : 5, time:3000,shade:0.5,shadeClose:true,offset: '250px'},function(){
            if(status){
                window.location.href = "<?php echo $url;?>";
            }
        });
    <?php endif;?>
});
</script>
</body>
</html>
