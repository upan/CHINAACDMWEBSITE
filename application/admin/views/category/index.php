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
      <h3 class="box-title">栏目列表</h3>
      <div class="box-tools pull-right">
        <?php if($this->m_admin->checkAuth("category/add")):?>
        <button type="button" class="btn btn-primary" @click="add(0);">添加</button>
        <?php endif;?>
      </div>
    </div>
    <div class="box-body">
        <table class="table table-striped">
            <tbody>
                <tr>
                  <th style="width: 30px">#</th>
                  <th>名称</th>
                  <th style="width: 60px;">类型</th>
                  <th style="width: 60px;">状态</th>
                  <th style="width: 60px;">排序</th>
                  <th style="width: 200px">操作</th>
                </tr>
                <?php foreach ($list as $key => $value):?>
                    <tr data-json='<?php echo json_encode($value);?>' id="<?php echo $value["id"];?>">
                      <td><?php echo $value["id"];?></td>
                      <td><?php echo $value["_name"];?></td>
                      <td><?php echo $this->category_type_config[$value["type"]]["name"];?></td>
                      <?php $id = $value['id'];
                            $icon = $value['status'] ? "ok" : "remove";
                            $span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$icon}\"></span>";
                      ?>
                      <td><?php echo $span;?></td>
                      <td><?php echo $value["sort"];?></td>
                      <td>
                        <?php if($this->m_admin->checkAuth("category/add")):?>
                        <button type="button" class="btn btn-sm btn-primary" @click="add(<?php echo $value['id'];?>);">添加</button>
                        <?php endif;?>
                        <?php if($this->m_admin->checkAuth("category/add")):?>
                        <button type="button" class="btn btn-sm btn-info" @click="edit(<?php echo $value['id'];?>);">修改</button>
                        <?php endif;?>
                        <?php if($this->m_admin->checkAuth("category/add")):?>
                        <button type="button" class="btn btn-sm btn-danger" @click="del(<?php echo $value['id'];?>);">删除</button>
                        <?php endif;?>
                      </td>
                    </tr>
                <?php endforeach;?>
              </tbody>
        </table>
    </div>
    <div class="box-footer"></div>
  </div>
</section>
</div>
<div class="category_layer" style="display: none;">
    <div class="box-body" id="category_validator">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.name" name="name" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">类型</label>
            <div class="col-sm-10">
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="type" id="radio_3" v-bind:value="1" v-model="info.type" required>
                    <label for="radio_3">单页</label>
                </span>
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="type" id="radio_4" v-bind:value="2" v-model="info.type" required>
                    <label for="radio_4">文章</label>
                </span>
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="type" id="radio_5" v-bind:value="3" v-model="info.type" required>
                    <label for="radio_5">下载</label>
                </span>
            </div>
        </div>
        <template v-if="info.type == 1">
            <span>&nbsp;</span>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">链接</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" v-model="info.link" name="link" placeholder="外链请输入网址：http://,单页请输入页面名称即可">
                </div>
            </div>
        </template>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="sort" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.sort" name="sort" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="status" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-10">
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="status" id="radio_1" v-bind:value="1" v-model="info.status" required>
                    <label for="radio_1">
                        正常
                    </label>
                </span>
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="status" id="radio_2" v-bind:value="0" v-model="info.status" required>
                    <label for="radio_2">
                        禁用
                    </label>
                </span>
            </div>
        </div>        
    </div>
</div>
<?php include VIEWPATH . 'footer_anchor.php'; ?>
<script type="text/javascript">
$(document).ready(function() {
    $("#category_validator").bootstrapValidator();
});
var vm = new Vue({
    el:"#app",
    data:{
        default:<?php echo json_encode($default);?>,
        info:<?php echo json_encode($default);?>,
    },
    methods:{
        add:function(id){
            this.info = this.default;
            G.layer("添加栏目",$(".category_layer"),["640px","350px"],["确定","取消"],function(index, layero){
                var result = $('#category_validator').data('bootstrapValidator').validate().isValid();
                if (!result) return false;
                layer.close(index);
                vm.info.pid = id;
                G.post("<?php echo base_url('category/add');?>",vm.info);
            });
        },
        edit:function(id){
            this.info = $("#"+id).data("json");
            G.layer("编辑栏目",$(".category_layer"),["640px","350px"],["确定","取消"],function(index, layero){
                var result = $('#category_validator').data('bootstrapValidator').validate().isValid();
                if (!result) return false;
                layer.close(index);
                G.post("<?php echo base_url('category/edit');?>"+"/"+id,vm.info);
            });
        },
        del:function(id){
            var data = $("#"+id).data("json");
            G.confirm("确定删除栏目 "+data.name+"？",function(index,layero){
                layer.close(index);
                G.get("<?php echo base_url("category/delete");?>"+"/"+id);    
            });
        }
    },
});
</script>