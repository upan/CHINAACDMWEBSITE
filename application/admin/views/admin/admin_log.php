<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * [admin_log description]
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-11-11T14:29:45+0800
 * @param    [param]
 * @return   [type] [description]
 */
-->
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
                            <th>ID</th>
                            <th>用户名</th>
                            <th>描述</th>
                            <th>IP</th>
                            <th>Uri</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key => $value):?>
                            <tr>
                                <td><?php echo ++$key;?></td>
                                <td><?php echo $value['uid'].":".$value['username'];?></td>
                                <td><?php echo $value['description'];?></td>
                                <td><?php echo $value['ip'];?></td>
                                <td><?php echo $value['uri'];?></td>
                                <td><?php echo date("Y-m-d H:i:s",$value['time']);?></td>
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
        var t = G.dataTable();
        //Apply the search
	    // t.columns().eq(0).each( function ( colIdx ) {
	    //     $( 'input', t.column( colIdx ).footer() ).on( 'keyup change', function () {
     //            t.column( colIdx ).search( this.value ).draw();
	    //     });
	    // });
    });
</script>