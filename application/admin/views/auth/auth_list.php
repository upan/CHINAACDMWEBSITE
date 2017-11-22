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
                                <th>名称</th>
                                <th>Uri</th>
                                <th>二级导航</th>
                                <th>一级导航</th>
                                <th>权限</th>
                                <th>左菜单</th>
                                <th>父(继承)节点</th>
                                <th>关联节点</th>
                                <th>排序</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key => $info):?>
                            <?php foreach ($info['child'] as $value):?>
                                <tr>
                                    <td><?php echo $value['name'];?></td>
                                    <td><?php echo $value['uri'];?></td>
                                    <td><?php echo $value['parent'];?></td>
                                    <td><?php echo $info['first'];?></td>
                                    <td><?php echo $value['auth_limit'] ? "开启" : "关闭";?></td>
                                    <td><?php echo $value['is_left_nav'] ? "是" : "否";?></td>
                                    <td><?php echo $value['parent_node_name'];?></td>
                                    <td><?php echo $value['related_node_name'];?></td>
                                    <td><?php echo $value['sort'];?></td>
                                    <?php $id = $value['id'];$icon = $value['status'] ? "ok" : "remove";$span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\" onclick=\"change('$id',$(this));\"></span>";?>
                                    <td><?php echo $span;?></td>
                                    <td>
                                        <?php echo gen_a_button('auth/auth_edit','btn-default btn-sm','编辑',$value['id']);?>
                                        <?php $url = base_url("auth/auth_del/{$value['id']}");
                                            $call = "G.confirm('删除 {$value['name']}?',function(){G.get('{$url}')});";
                                        echo gen_button('auth/auth_del','btn-primary btn-sm','删除',$call);?>
                                    </td>
                                </tr>
                            <?php endforeach;?>
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
    function change(id,el){
        var c = el.data('tag');
        $.get("<?php echo base_url('auth/auth_disabled');?>"+"/"+id, function(res) {
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