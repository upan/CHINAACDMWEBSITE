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
      <h3 class="box-title">APP类型列表</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-block btn-primary btn-sm" @click="add();">添加</button>
      </div>
    </div>
    <div class="box-body">
        <div class="box-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>description</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr data-json='<?php echo json_encode($value);?>' id="app_type_<?php echo $value["id"];?>">
                        <td><?php echo $value['id'];?></td>
                        <td><?php echo $value['name'];?></td>
                        <td><?php echo $value['description'];?></td>
                        <td>
                            <?php if($this->m_admin->checkAuth("acdm/update_app_type")):?>
                                <button type="button" class="btn btn-sm btn-info" @click="edit(<?php echo $value['id'];?>);">编辑</button>
                            <?php endif;?>
                            <?php $url = base_url("acdm/delete_app_type/{$value['id']}");
                                $call = "G.confirm('删除 {$value['name']}?',function(){G.get('{$url}')});";
                            echo gen_button('acdm/delete_app_type','btn-primary btn-sm','删除',$call);?>
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
<div class="app_type_layer" style="display: none;">
    <div class="box-body" id="app_type_validator">
        <div class="form-group" id="id_input_div">
            <label for="id" class="col-sm-2 control-label">ID</label>
            <div class="col-sm-10">
                <input type="number" min="1" class="form-control" v-model="info.id" name="id" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.name" name="name" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">描述</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.description" name="description">
            </div>
        </div>
    </div>
</div>
<?php include VIEWPATH . 'footer_anchor.php'; ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.css');?>">
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js');?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.min.js');?>"></script>
<script type="text/javascript">
    $(function () {
        G.dataTable();
        $("#app_type_validator").bootstrapValidator();
    });
    var vm = new Vue({
        el:"#app",
        data:{
            list:<?php echo json_encode($list);?>,
            fields:<?php echo json_encode($fields);?>,
            info:<?php echo json_encode($fields);?>,
        },
        methods:{
            add:function(){
                this.info = this.fields;
                G.layer("添加APP类型",$(".app_type_layer"),["640px","280px"],["确定","取消"],function(index, layero){
                    var result = $('#app_type_validator').data('bootstrapValidator').validate().isValid();
                    if (!result) return false;
                    layer.close(index);
                    G.post("<?php echo base_url('acdm/update_app_type');?>",vm.info);
                },null,function(index,layero){
                    $("#id_input_div").show();
                });
            },
            edit:function(id){
                this.info = $("#app_type_"+id).data("json");
                G.layer("编辑APP类型",$(".app_type_layer"),["640px","280px"],["确定","取消"],function(index, layero){
                    var result = $('#app_type_validator').data('bootstrapValidator').validate().isValid();
                    if (!result) return false;
                    layer.close(index);
                    G.post("<?php echo base_url('acdm/update_app_type');?>",vm.info);
                },null,function(index,layero){
                    $("#id_input_div").hide();
                });
            }
        },
    });
</script>