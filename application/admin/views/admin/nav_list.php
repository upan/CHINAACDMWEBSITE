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
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>name</th>
                        <th>status</th>
                        <th>parent</th>
                        <th>sort</th>
                        <th>icon</th>
                        <th>operation</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $key => $value): ?>
                        <tr>
                            <td><?php echo $value['id']; ?></td>
                            <td><?php echo $value['name']; ?></td>
                            <?php $id = $value['id'];$icon = $value['status'] ? "ok" : "remove";$span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\" onclick=\"change('$id',$(this));\"></span>";?>
                            <td><?php echo $span;?></td>
                            <td><?php echo "主导航"; ?></td>
                            <td><?php echo $value['sort']; ?></td>
                            <td>
                                <i class="fa <?php echo $value['icon']; ?>"></i>
                                <?php echo $value['icon']; ?>
                            </td>
                            <td>
                                <a href="<?php echo base_url("admin/nav_add/{$value['id']}"); ?>"
                                   class="btn btn-default">编辑</a>
                                <?php $url = base_url("admin/nav_del/{$value['id']}"); ?>
                                <button class="btn btn-primary"
                                        onclick="G.confirm('删除 <?php echo $value['name']; ?>?',function(){G.get('<?php echo $url;?>');});">
                                    删除
                                </button>
                            </td>
                        </tr>
                        <?php if(isset($value['child'])):?>
                            <?php foreach ($value['child'] as $val): ?>
                                <tr>
                                    <td></td>
                                    <td><?php echo $val['name']; ?></td>
                                    <?php $id = $val['id'];$icon = $val['status'] ? "ok" : "remove";$span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\" onclick=\"change('$id',$(this));\"></span>";?>
                                    <td><?php echo $span;?></td>
                                    <td><?php echo $value['name']; ?></td>
                                    <td><?php echo $val['sort']; ?></td>
                                    <td>
                                    <i class="fa <?php echo $val['icon']; ?>"></i>
                                    <?php echo $val['icon']; ?></td>
                                    <td>
                                        <a href="<?php echo base_url("admin/nav_add/{$val['id']}"); ?>"
                                           class="btn btn-default">编辑</a>
                                        <?php $url = base_url("admin/nav_del/{$val['id']}"); ?>
                                        <button class="btn btn-primary"
                                                onclick="G.confirm('删除 <?php echo $val['name']; ?>?',function(){G.get('<?php echo $url; ?>');});">
                                            删除
                                        </button>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif;?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- /.box-footer-->
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
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
        var url = "<?php echo base_url('admin/nav_disabled');?>"+"/"+id;
        var c = el.data('tag');
        $.get(url, function(res) {
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