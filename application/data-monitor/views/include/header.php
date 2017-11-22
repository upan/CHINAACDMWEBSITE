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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/js/dataTables/dataTables.bootstrap.css');?>"/>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.metisMenu.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/dataTables/jquery.dataTables.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/dataTables/dataTables.bootstrap.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/layer/layer.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/common.js')?>"></script>
</head>
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
                <a class="navbar-brand" href="<?php echo base_url('console/dashboard/index');?>"><?php echo $system_name;?></a>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" aria-expanded="false">
                        <i class="fa fa-envelope fa-fw"></i>(1) <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="javascript:void(0);">
                                <div>
                                    <strong>月度报表</strong>
                                    <span class="pull-right text-muted">
                                        <em><?php echo date('m/d H:i');?></em>
                                    </span>
                                </div>
                                <div>你有一封新的邮件已发送至你的工作邮箱，请查看相关信息。</div>
                            </a>
                        </li>
                    </ul>
                </li>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" aria-expanded="false">
                        <i class="fa fa-bell fa-fw"></i>(1) <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="javascript:void(0);">
                                <div>
                                    <i class="fa fa-comment fa-fw"></i> 你的备降详情接口已中断，详细邮件已发送至你的工作邮箱，请立即处理。
                                    <span class="pull-right text-muted small"><?php echo date('m/d H:i');?></span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" aria-expanded="false">
                        <i class="fa fa-user fa-fw"></i>袁裕 <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?php echo base_url('account/operate/logout');?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>

        </nav>
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li<?php echo $url_seg1 == 'console' ? ' class="active"' : '';?>>
                            <a href="<?php echo base_url('console/dashboard/index');?>"<?php echo $url_seg2 == 'dashboard' ? ' class="active-menu"' : '';?>><i class="fa fa-desktop"></i>运行实况</a>
                    </li>
                    <li<?php echo $url_seg1 == 'statistic' ? ' class="active"' : '';?>>
                        <a href="javascript:void(0);"<?php echo $url_seg1 == 'statistic' ? ' class="active-menu"' : '';?>><i class="fa fa-bar-chart-o"></i>统计<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="javascript:void(0);">预留页面<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li><a href="javascript:void(0);">预留页面子页面1</a></li>
                                    <li><a href="javascript:void(0);">预留页面子页面2</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li<?php echo $url_seg1 == 'logs' ? ' class="active"' : '';?>>
                        <a href="javascript:void(0);"<?php echo $url_seg1 == 'logs' ? ' class="active-menu"' : '';?>><i class="fa fa-file"></i> 日志<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo base_url('logs/notice_log/index');?>"<?php echo $url_seg2 == 'notice_log' ? ' class="active-menu"' : '';?>>告警日志</a>
                            </li>
                        </ul>
                    </li>
                    <li<?php echo $url_seg1 == 'setting' ? ' class="active"' : '';?>>
                        <a href="javascript:void(0);"<?php echo $url_seg1 == 'setting' ? ' class="active-menu"' : '';?>><i class="fa fa-edit"></i>设置<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo base_url('setting/api/index');?>"<?php echo $url_seg2 == 'api' ? ' class="active-menu"' : '';?>>接口管理</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('setting/staff/index');?>"<?php echo $url_seg2 == 'staff' ? ' class="active-menu"' : '';?>>员工管理</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
		<div id="page-inner">