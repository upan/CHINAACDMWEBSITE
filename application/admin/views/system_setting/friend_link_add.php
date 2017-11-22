<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-10-27T13:54:06+0800
 */
-->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datetimepicker/css/bootstrap-datetimepicker.css'); ?>">
<div class="content-wrapper">
<section class="content">
  <div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">添加友链</h3>
    </div>
    <div class="box-body" id="firend_link_validator">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.name" name="name" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="url" class="col-sm-2 control-label">URL</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.url" name="url" required placeholder="请输入 http://">
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
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="start_time" class="col-sm-2 control-label">开始时间</label>
            <div class="col-sm-10">
                <input type="text" class="form-control start_time" v-model="info.start_time" name="start_time">
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="end_time" class="col-sm-2 control-label">结束时间</label>
            <div class="col-sm-10">
                <input type="text" class="form-control end_time" v-model="info.end_time" name="end_time">
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="sort" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.sort" name="sort" required>
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
    $("#firend_link_validator").bootstrapValidator();
    var _start_time = G.date(".start_time",2,2,"yyyy-mm-dd",function(ev){
            //vm.info.start_time = parseInt(ev.date.valueOf()/1000);
        }),
        _end_time   = G.date(".end_time",2,2,"yyyy-mm-dd",function(ev){
            //vm.info.end_time = parseInt(ev.date.valueOf()/1000);
        });
});
var vm = new Vue({
    el:"#app",
    data:{
        info:<?php echo json_encode($friend_link);?>,
    },
    methods:{
        add:function(){
            var result = $('#firend_link_validator').data('bootstrapValidator').validate().isValid();
            if (!result) return false;
            <?php if((int)$friend_link["id"]):?>
            G.post("<?php echo base_url('system_setting/friend_link_edit');?>"+"/"+this.info.id,this.info);
            <?php else:?>
            G.post("<?php echo base_url('system_setting/friend_link_add');?>",this.info);
            <?php endif;?>
        },
    },
});
</script>