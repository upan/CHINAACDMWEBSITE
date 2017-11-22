<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo SITE_NAME; ?> | Login</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/dist/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/dist/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/assets/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/assets/plugins/iCheck/square/blue.css">
  <!-- sweetalert2 -->
  <link rel="stylesheet" href="/assets/plugins/sweetalert/sweetalert2.min.css">
  <link rel="stylesheet" href="/assets/plugins/bootstrapvalidator/bootstrapValidator.min.css"> 
  <!-- vue 2.0 -->
  <script src="/assets/plugins/vue/vue.js"></script>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<div class="wrapper" id="app">
<form class="form-horizontal">
  <div class="box-body">
    <div class="form-group">
      <label for="old_password" class="col-sm-2 control-label">原密码</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" name="old_password" required v-model="old_password">
      </div>
    </div>
    <div class="form-group">
      <label for="new_password" class="col-sm-2 control-label">新密码</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" name="new_password" required v-model="new_password" minlength="6" data-bv-stringlength-message="密码至少为6个字符长度！">
      </div>
    </div>
    <div class="form-group">
      <label for="confirm_password" class="col-sm-2 control-label">确认密码</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" name="confirm_password" required v-model="confirm_password">
      </div>
    </div>
  <!-- /.box-body -->
  <div class="form-group text-center">
    <button type="button" class="btn btn-default" @click="commit();">确定</button>
    <button type="button" class="btn btn-info" @click="close();">取消</button>
  </div>
  </div>
  <!-- /.box-footer -->
</form>
</div>
<!-- jQuery 2.2.3 -->
<script src="/assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/assets/plugins/iCheck/icheck.min.js"></script>
<!-- sweetalert2 -->
<script src="/assets/plugins/sweetalert/es6-promise.min.js"></script>
<script src="/assets/plugins/sweetalert/sweetalert2.min.js"></script>
<!-- bootstrapValidator -->
<script src="/assets/plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
<script src="/assets/plugins/bootstrapvalidator/zh_CN.js"></script>
<script src="/assets/dist/js/core.js"></script>
<!-- layer.js -->
<script src="/assets/plugins/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("form").bootstrapValidator({
            fields:{
                confirm_password: {
                    validators: {
                        identical: {
                            field: 'new_password',
                            message: '输入密码与上次不符！'
                        },
                    }
                },
                new_password: {
                    validators: {
                        different: {
                            field: 'old_password',
                            message: '新密码与输入的原密码相同！'
                        },
                    }
                },
            }
        });
    });
    var vm = new Vue({
        el:"#app",
        data:<?php echo json_encode($default);?>,
        methods:{
            commit:function(){
                var result = $('form').data('bootstrapValidator').validate().isValid();
                if (!result) return false;
                $.post("<?php echo base_url('admin/changepwd');?>",this.$data, function(data) {
                    var _data = $.parseJSON(data);
                    if(_data.status){
                        layer.alert(_data.msg,{icon:1},function(){
                            parent.G.redirect("<?php echo base_url('login/loginOut');?>");
                        });
                    }else{
                        layer.alert(_data.msg, {icon:2});
                    }
                });
            },
            close:function(){
                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                parent.layer.close(index);
            }
        }
    });
</script>
</body>
</html>