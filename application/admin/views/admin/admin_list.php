<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--/**
 * @authors MAGENJIE (1299234033@qq.com)
 * @date    2016-06-29 16:27:16
 * @version 1.0.0
 */-->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">列表</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                            class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="box-body no-padding">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>账号</th>
                            <th>姓名</th>
                            <th>手机</th>
                            <th>状态</th>
                            <th>角色</th>
                            <th>上次登录</th>
                            <th>IP</th>
                            <th>失败</th>
                            <th>时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key => $value):?>
                            <tr>
                                <td><?php echo $value['username'];?></td>
                                <td><?php echo $value['truename'];?></td>
                                <td><?php echo $value['phone'];?></td>
                                <?php $id = $value['id'];$icon = $value['status'] ? "ok" : "remove";$span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\" onclick=\"change('$id',$(this),'{$value['is_system']}');\"></span>";?>
                                <td><?php echo $span;?></td>
                                <td><?php echo $value['group_name'];?></td>
                                <td><?php echo date("Y-m-d H:i:s",$value['last_login_time']);?></td>
                                <td><?php echo $value['last_login_ip'];?></td>
                                <td><?php echo $value['today_login_fail'];?></td>
                                <td><?php echo date("Y-m-d H:i:s",$value['create_time']);?></td>
                                <td>
                                    <?php echo gen_a_button('admin/admin_edit','btn-default btn-sm','编辑',$value['id']);?>
                                    <?php if($value['is_system'] == 0):?>
                                    <?php $url = base_url("admin/admin_del/{$value['id']}");
                                        $call = "G.confirm('删除 {$value['username']}?',function(){G.get('{$url}')});";
                                    echo gen_button('admin/admin_del','btn-primary btn-sm','删除',$call);?>
                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php include VIEWPATH . 'footer_anchor.php'; ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.css');?>">
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js');?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.min.js');?>"></script>
<script type="text/javascript">
    $(function () {
        G.dataTable();
    });
    function change(id,el,is_system){
        var c = el.data('tag');
        if (is_system == 1) {
            G.error("无权限");return false;
        }
        $.get("<?php echo base_url('admin/admin_disabled');?>"+"/"+id, function(res) {
            var data = $.parseJSON(res);
            if (data.status) {
                c == "ok" ? _ = "remove" : _ = "ok";
                var c_class = "glyphicon-"+c+" font-"+c;
                var _class = "glyphicon-"+_+" font-"+_;
                el.removeClass(c_class).addClass(_class).data('tag',_);
            }else{
                G.error(data.msg);
            }
        });
    }
</script>