<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-09-14T14:38:10+0800
 */
-->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datetimepicker/css/bootstrap-datetimepicker.css'); ?>">
<div class="content-wrapper">
<section class="content">
  <div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">添加版本：</h3>
    </div>
    <div class="box-body" id="version_validator">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">版本名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.name" name="name" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="code" class="col-sm-2 control-label">版本编号</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" v-model="info.code" name="code" placeholder="请输入版本编号,纯数字,供安卓使用">
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="is_must" class="col-sm-2 control-label">是否必需</label>
            <div class="col-sm-10">
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="is_must" id="is_must_1" v-bind:value="1" v-model="info.is_must" required>
                    <label for="radio_1">是</label>
                </span>
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="is_must" id="is_must_2" v-bind:value="0" v-model="info.is_must" required>
                    <label for="radio_2">否</label>
                </span>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="airport_iata" class="col-sm-2 control-label">所属机场</label>
            <div class="col-sm-10">
                <select class="form-control" v-model="info.airport_iata" name="airport_iata" required>
                    <option value="AM0">ACDM项目</option>
                    <?php foreach ($airports_list as $airport_info):?>
                    <option value="<?php echo $airport_info['airport_iata'];?>"><?php echo $airport_info['cn_name'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">所属客户端</label>
            <div class="col-sm-10">
                <select class="form-control" v-model="info.type" name="type" required>
                    <?php foreach ($app_type as $type):?>
                    <option value="<?php echo $type['id'];?>"><?php echo $type['name'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">版本描述信息</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="description" v-model="info.description" placeholder="请输入版本更新描述" rows="3"></textarea>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="url" class="col-sm-2 control-label">URL</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.url" name="url" placeholder="请输入最新版本地址">
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="status" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-10">
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="status" id="status_1" v-bind:value="1" v-model="info.status" required>
                    <label for="radio_1">
                        正常
                    </label>
                </span>
                <span class="radio radio-danger radio-inline">
                    <input type="radio" name="status" id="status_2" v-bind:value="0" v-model="info.status" required>
                    <label for="radio_2">
                        禁用
                    </label>
                </span>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <button type="button" class="btn btn-info pull-right" @click="add();">保存</button>
    </div>
  </div>
</section>
</div>
<?php include VIEWPATH . 'footer_anchor.php'; ?>
<!-- datepicker -->
<script type="text/javascript"
        src="<?php echo base_url('assets/plugins/datetimepicker/js/bootstrap-datetimepicker.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('assets/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js'); ?>"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#version_validator").bootstrapValidator();
});
var vm = new Vue({
    el:"#app",
    data:{
        info:<?php echo json_encode($data);?>,
    },
    methods:{
        add:function(){
            var result = $('#version_validator').data('bootstrapValidator').validate().isValid();
            if (!result) return false;
            <?php if((int)(isset($data["id"]) ? $data["id"] : 0)):?>
            G.post("<?php echo base_url('acdm/edit_version');?>"+"/"+this.info.id,this.info);
            <?php else:?>
            G.post("<?php echo base_url('acdm/add_version');?>",this.info);
            <?php endif;?>
        },
    },
});
</script>