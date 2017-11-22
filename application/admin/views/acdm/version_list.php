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
      <h3 class="box-title">APP版本列表</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="box-body">
        <div class="box-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>airport_iata</th>
                    <th>type</th>
                    <th>status</th>
                    <th>name</th>
                    <th>code</th>
                    <th>is_must</th>
                    <th>description</th>
                    <th>url</th>
                    <th>update_time</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr>
                        <td><?php echo $value['id'];?></td>
                        <td><?php echo $value['airport_iata'];?></td>
                        <td><?php echo $app_type[$value['type']]["name"];?></td>
                        <?php $id = $value['id'];$icon = $value['status'] ? "ok" : "remove";$span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\" onclick=\"change('$id',$(this));\"></span>";?>
                        <td><?php echo $span;?></td>
                        <td><?php echo $value['name'];?></td>
                        <td><?php echo $value['code'];?></td>
                        <td><?php echo $value['is_must'] ? "是" : "否";?></td>
                        <td><?php echo $value['description'];?></td>
                        <td><?php echo $value['url'];?></td>
                        <td><?php echo date("Y-m-d H:i",$value['update_time']);?></td>
                        <td>
                            <?php echo gen_a_button('acdm/edit_version','btn-default btn-sm','编辑',$value['id']);?>
                            <?php $url = base_url("acdm/delete_version/{$value['id']}");
                                $call = "G.confirm('删除 {$value['name']}?',function(){G.get('{$url}')});";
                            echo gen_button('acdm/delete_version','btn-primary btn-sm','删除',$call);?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
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
    $(function () {
        G.dataTable();
    });
    function change(id,el,is_system){
        var c = el.data('tag');
        $.get("<?php echo base_url('acdm/disabled_version');?>"+"/"+id, function(res) {
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