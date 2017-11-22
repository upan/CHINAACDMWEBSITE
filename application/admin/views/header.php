<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-10-27T13:54:06+0800
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo SITE_NAME; ?> | 管理后台</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/dist/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/assets/dist/css/ionicons.min.css">
    <!-- select2 -->
    <link rel="stylesheet" href="/assets/plugins/select2/select2.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/assets/dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/assets/dist/css/skins/_all-skins.min.css">
    <!-- bootstrapValidator -->
    <link rel="stylesheet" href="/assets/plugins/bootstrapvalidator/bootstrapValidator.css">
    <!-- awesome-bootstrap-checkbox.css -->
    <link rel="stylesheet" href="/assets/bootstrap/awesome-bootstrap-checkbox.css">
    <!-- jQuery 2.2.3 -->
    <script src="/assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- vue 2.0 -->
    <script src="/assets/plugins/vue/vue.js"></script>
    <!-- layer.js -->
    <script src="/assets/plugins/layer/layer.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
        var LOGINOUT_URL = "<?php echo base_url('login/loginOut');?>"; 
    </script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper" id="app" v-cloak>
    <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo base_url('admin'); ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><?php echo SITE_NAME_MINI; ?></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><?php echo SITE_NAME_LG; ?></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="javascript:;" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <?php if (isset($this->session->nav)): ?>
                        <?php foreach ($this->session->nav as $key => $value): ?>
                            <?php $uri = $value['second'][0]['third'][0]['uri'];?>
                            <li <?php if ($value['id'] == $this->_firstId): ?>class="active"<?php endif; ?>>
                                <a href="<?php echo base_url($uri); ?>"><?php echo $value['name']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?php echo empty($this->session->photo) ? "/assets/dist/img/user2-160x160.jpg" : $this->session->photo; ?>" class="user-image"
                                 alt="User Image">
                            <span class="hidden-xs"><?php echo $this->session->username; ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?php echo empty($this->session->photo) ? "/assets/dist/img/user2-160x160.jpg" : $this->session->photo; ?>"
                                     class="img-circle" alt="User Image">

                                <p><?php echo $this->session->username; ?>
                                    - <?php echo $this->session->group_name; ?></p>
                                <p><small>姓名：<?php echo $this->session->truename; ?> 手机：<?php echo $this->session->phone; ?>
                                    
                                </small></p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <small>
                                    登录日志：<?php echo date("Y-m-d H:i:s",$this->session->last_login_time); ?>
                                    IP:<?php echo $this->session->last_login_ip; ?></small>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?php echo base_url('login/loginOut'); ?>"
                                       class="btn btn-default btn-flat">注销登录</a>
                                </div>
                                <div class="pull-right">
                                    <a href="javascript:;" onclick="G.changepwd('<?php echo base_url('login/changepwd');?>');" 
                                       class="btn btn-default btn-flat">修改密码</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- =============================================== -->
    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?php echo base_url('assets/dist/img/user2-160x160.jpg'); ?>" class="img-circle"
                         alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>管理员名称</p>
                    <a href="javascript:;"><i class="fa fa-circle text-success"></i> 当前在线</a>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <?php foreach ($this->session->nav as $key => $value): ?>
                    <?php if ($value['id'] == $this->_firstId): ?>
                        <?php foreach ($value['second'] as $ke => $val): ?>
                            <li class="treeview <?php if ($this->_secondId == $val['id']): ?>active<?php endif; ?>">
                                <?php $uri = $val['third'][0]['uri']; ?>
                                <a href="<?php echo base_url($uri); ?>">
                                    <i class="fa <?php echo $val['icon']; ?>"></i>
                                    <span><?php echo $val['name']; ?></span><i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <?php foreach ($val['third'] as $k => $v): ?>
                                        <?php if (!$v['is_left_nav']) continue;?>
                                        <li <?php if ($this->_thirdId == $v['id']): ?>class="active"<?php endif; ?>>
                                            <a href="<?php echo base_url($v['uri']); ?>">
                                                <i class="fa fa-circle-o <?php if ($this->_thirdId == $v['id']): ?>text-aqua<?php endif; ?>"></i>
                                                <?php echo $v['name']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- =============================================== -->