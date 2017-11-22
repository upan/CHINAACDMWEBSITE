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
      <h3 class="box-title">机场列表</h3>
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
                    <th>三字码</th>
                    <th>四字码</th>
                    <th>全称</th>
                    <th>中文</th>
                    <th>简称</th>
                    <th>跑道</th>
                    <th>系统名称</th>
                    <th>API公钥</th>
                    <th>集团机场(默认)</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr>
                        <td><?php echo $value['member_airport_id'];?></td>
                        <td><?php echo $value['airport_iata'];?></td>
                        <td><?php echo $value['airport_icao'];?></td>
                        <td><?php echo $value['airport_name'];?></td>
                        <td><?php echo $value['cn_name'];?></td>
                        <td><?php echo $value['cn_name_short'];?></td>
                        <td><?php echo $value['runway'];?></td>
                        <td><?php echo $value['system_name'];?></td>
                        <td><?php echo $value['api_public_key'];?></td>
                        <td><?php echo $value['system_prefix'];?></td>
                        <?php $id = $value['member_airport_id'];$icon = $value['status'] ? "ok" : "remove";$span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\" onclick=\"change('$id',$(this));\"></span>";?>
                        <td><?php echo $span;?></td>
                        <td><?php echo date("Y-m-d H:i",$value["create_time"]);?></td>
                        <td><?php echo date("Y-m-d H:i",$value["update_time"]);?></td>
                        <td>
                            <?php echo gen_a_button('acdm/edit_airport','btn-default btn-sm','编辑',$value['member_airport_id']);?>
                            <?php $url = base_url("acdm/delete_airport/{$value['member_airport_id']}");
                                $call = "G.confirm('删除 {$value['cn_name']}?',function(){G.get('{$url}')});";
                            echo gen_button('acdm/delete_airport','btn-primary btn-sm','删除',$call);?>
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
        $.get("<?php echo base_url('acdm/disabled_airport');?>"+"/"+id, function(res) {
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