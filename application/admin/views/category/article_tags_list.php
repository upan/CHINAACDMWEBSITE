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
        <h3 class="box-title">文章标签列表</h3>
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
                    <th>名称</th>
                    <th>标记</th>
                    <th>文章</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr>
                        <td><?php echo $value["id"];?></td>
                        <td><?php echo $value["name"];?></td>
                        <td><?php echo $value["num"];?></td>
                        <td><?php echo $value["article_ids"];?></td>
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