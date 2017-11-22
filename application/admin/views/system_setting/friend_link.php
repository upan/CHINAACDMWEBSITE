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
      <h3 class="box-title">友链列表</h3>
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
                    <th>Url</th>
                    <th>状态</th>
                    <th>开始时间</th>
                    <th>结束时间</th>
                    <th>排序</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr>
                        <td><?php echo $value["id"];?></td>
                        <td><?php echo $value["name"];?></td>
                        <td><?php echo $value["url"];?></td>
                        <?php $id = $value['id'];
                            $icon = $value['status'] ? "ok" : "remove";
                            $span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\"></span>";
                        ?>
                        <td><?php echo $span;?></td>
                        <td><?php echo empty($value["start_time"]) ? "--" : $value["start_time"];?></td>
                        <td><?php echo empty($value["end_time"]) ? "--" : $value["end_time"];?></td>
                        <td><?php echo $value["sort"];?></td>
                        <td>
                            <?php echo gen_a_button('system_setting/friend_link_edit','btn-default btn-sm','编辑',$value['id']);?>
                            <?php $url = base_url("system_setting/friend_link_delete/{$value['id']}");
                                $call = "G.confirm('确定删除友链 {$value['name']}?',function(){G.get('{$url}')});";
                            echo gen_button('system_setting/friend_link_delete','btn-primary btn-sm','删除',$call);?>
                        </td>
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