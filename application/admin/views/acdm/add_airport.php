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
        <h3 class="box-title">添加机场：</h3>
    </div>
    <div class="box-body" id="airport_validator">
        <div class="form-group">
            <label for="airport_iata" class="col-sm-2 control-label">三字码</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.airport_iata" name="airport_iata" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="airport_icao" class="col-sm-2 control-label">四字码</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.airport_icao" name="airport_icao" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="airport_name" class="col-sm-2 control-label">机场名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.airport_name" name="airport_name" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="cn_name" class="col-sm-2 control-label">机场中文</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.cn_name" name="cn_name" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="cn_name_short" class="col-sm-2 control-label">机场简称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.cn_name_short" name="cn_name_short" required>
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="runway" class="col-sm-2 control-label">跑道号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.runway" name="runway" placeholder="多个以 英文 , 分隔">
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="system_name" class="col-sm-2 control-label">系统名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.system_name" name="system_name">
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="api_public_key" class="col-sm-2 control-label">API公钥</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.api_public_key" name="api_public_key">
            </div>
        </div>
        <span>&nbsp;</span>
        <div class="form-group">
            <label for="system_prefix" class="col-sm-2 control-label">集团默认机场</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" v-model="info.system_prefix" name="system_prefix">
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
    $("#airport_validator").bootstrapValidator();
});
var vm = new Vue({
    el:"#app",
    data:{
        info:<?php echo json_encode($data);?>,
    },
    methods:{
        add:function(){
            var result = $('#airport_validator').data('bootstrapValidator').validate().isValid();
            if (!result) return false;
            <?php if((int)(isset($data["member_airport_id"]) ? $data["member_airport_id"] : 0)):?>
            G.post("<?php echo base_url('acdm/edit_airport');?>"+"/"+this.info.member_airport_id,this.info);
            <?php else:?>
            G.post("<?php echo base_url('acdm/add_airport');?>",this.info);
            <?php endif;?>
        },
    },
    watch:{
        "info.airport_iata": {
            handler: function (val, oldVal) {
                if (val.length == 3) {
                    $.get("<?php echo base_url('acdm/get_airport_info_by_iata');?>"+"/"+val, function(res) {
                        var data = $.parseJSON(res);
                        if (data.status) {
                            vm.info.cn_name       = data.data.cn_name;
                            vm.info.cn_name_short = data.data.cn_name_short;
                            vm.info.airport_iata  = data.data.airport_iata;
                            vm.info.airport_icao  = data.data.airport_icao;
                        }
                    });
                }
            },
            deep: true,
        }
    }
});
</script>