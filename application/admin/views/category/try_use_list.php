<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-10-27T13:54:06+0800
 */
-->
<div class="content-wrapper">
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">试用申请</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
            <i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body">
        <table class="table table-hover">
            <thead>
               <tr>
                    <th style="width: 10px;">#</th>
                    <th>姓名</th>
                    <th>公司</th>
                    <th>邮箱</th>
                    <th>手机</th>
                    <th>工作</th>
                    <th>留言</th>
                    <th>IP</th>
                    <th>时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr>
                        <td><?php echo $value["id"];?></td>
                        <td><?php echo $value["name"];?></td>
                        <td><?php echo $value["company"];?></td>
                        <td><?php echo $value["email"];?></td>
                        <td><?php echo $value["phone"];?></td>
                        <td><?php echo $value["job"];?></td>
                        <td><?php echo $value["content"];?></td>
                        <td><?php echo $value["ip"];?></td>
                        <td><?php echo date("Y-m-d H:i:s",$value["create_time"]);?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
      </div>
    </div>
  </section>
</div>
<?php include VIEWPATH . 'footer_anchor.php'; ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.css');?>">
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js');?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.min.js');?>"></script>
<script type="text/javascript">
$(document).ready(function() {
    G.dataTable();
});
</script>