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
                                <th>name</th>
                                <th>status</th>
                                <th>operation</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key => $value):?>
                            <tr>
                                <td><?php echo $value['name'];?></td>
                                <?php $id = $value['id'];$icon = $value['status'] ? "ok" : "remove";$span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\" onclick=\"change('$id',$(this),'{$value['is_system']}');\"></span>";?>
                                <td><?php echo $span;?></td>
                                <td>
                                    <?php if($value['is_system'] == 0):?>
                                        <?php echo gen_a_button('auth/group_edit','btn-default','编辑',$value['id']);?>
                                            <?php $url = base_url("auth/group_del/{$value['id']}");
                                                $call = "G.confirm('删除 {$value['name']}?',function(){G.get('{$url}')});";
                                            echo gen_button('auth/group_del','btn-primary','删除',$call);?>
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
            G.error("系统默认角色分组,禁止操作！");return false;
        }
        $.get("<?php echo base_url('auth/group_disabled');?>"+"/"+id, function(res) {
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