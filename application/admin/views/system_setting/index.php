<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-10-27T13:54:06+0800
 */
-->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css'); ?>">
<div class="content-wrapper">
<section class="content">
  <div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">系统配置</h3>
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active" @click="switch_tab('site_config');"><a href="#site_config" data-toggle="tab" aria-expanded="true">基本信息</a></li>
              <li @click="switch_tab('imgscroll_config');"><a href="#imgscroll_config" data-toggle="tab" aria-expanded="false">首页幻灯片</a></li>
              <li @click="switch_tab('email_config');"><a href="#email_config" data-toggle="tab" aria-expanded="false">邮件设置</a></li>
            </ul>
            <div class="tab-content" style="padding-bottom: 25px;">
                <div class="tab-pane active" id="site_config">
                    <div class="form-group">
                        <label for="name" class="col-sm-1 control-label">网站名称:</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" v-model="config.site_config.value.name" name="name" required>
                        </div>
                    </div>
                    <span>&nbsp;</span>
                    <div class="form-group">
                        <label for="keywords" class="col-sm-1 control-label">关键词:</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" v-model="config.site_config.value.keywords" name="keywords" required>
                        </div>
                    </div>
                    <span>&nbsp;</span>
                    <div class="form-group">
                        <label for="log_enable" class="col-sm-1 control-label">操作日志:</label>
                        <div class="col-sm-11">
                            <span class="radio radio-danger radio-inline">
                                <input type="radio" name="status" id="radio_1" v-bind:value="1" v-model="config.site_config.value.log_enable" required>
                                <label for="radio_1">开启</label>
                            </span>
                            <span class="radio radio-danger radio-inline">
                                <input type="radio" name="status" id="radio_2" v-bind:value="0" v-model="config.site_config.value.log_enable" required>
                                <label for="radio_2">关闭</label>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="email_config">
                    <div class="form-group">
                        <label for="smtp_host" class="col-sm-1 control-label">服务器:</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" v-model="config.email_config.value.smtp_host" name="smtp_host" required>
                        </div>
                    </div>
                    <span>&nbsp;</span>
                    <div class="form-group">
                        <label for="smtp_port" class="col-sm-1 control-label">端口号:</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" v-model="config.email_config.value.smtp_port" name="smtp_port" required>
                        </div>
                    </div>
                    <span>&nbsp;</span>
                    <div class="form-group">
                        <label for="smtp_user" class="col-sm-1 control-label">邮箱账号:</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" v-model="config.email_config.value.smtp_user" name="smtp_user" required>
                        </div>
                    </div>
                    <span>&nbsp;</span>
                    <div class="form-group">
                        <label for="smtp_pass" class="col-sm-1 control-label">邮箱秘钥:</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" v-model="config.email_config.value.smtp_pass" name="smtp_pass" required>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="imgscroll_config">
                    <div class="row">
                        <template v-if="config.imgscroll_config.value" v-for="(item,k) in config.imgscroll_config.value">
                            <div class="col-sm-2">
                                <p><img class="img-responsive" :src="item.path"></p>
                                <p><input type="number" min="0" name="sort" placeholder="序号" class="form-control input-sm" v-model="item.sort"></p>
                                <p><button type="button" class="btn btn-block btn-danger btn-sm" @click="del_pic(k);">删除</button></p>
                            </div>
                        </template>
                        <div class="col-sm-2"></div>
                    </div>
                    <span>&nbsp;</span>
                    <input type="file" class="form-control input-sm file-loading" data-show-preview="false" name="imgscroll" id="imgscroll" placeholder="幻灯片" accept="image/*">
                    <p class="help-block">请上传图片格式文件,大小在2M以内.</p>
                    <div id="imgscroll_errorBlock"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <button type="button" class="btn btn-info pull-right" @click="update();">保存</button>
    </div>
    <!-- /.box-footer-->
  </div>
  <!-- /.box -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php include VIEWPATH . 'footer_anchor.php'; ?>
<!-- fileinput -->
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/js/locales/zh.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/themes/fa/theme.js'); ?>"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#site_config").bootstrapValidator();
    $("#email_config").bootstrapValidator();
    $("#imgscroll_config").bootstrapValidator();
    G.fileinput($("#imgscroll"),{
        maxFileSize:10240,
        elErrorContainer: "#imgscroll_errorBlock",
        uploadUrl:"<?php echo base_url('system_setting/upload_pic');?>",
    },function(e,params){
        if (params.response.status) {
            vm.config.imgscroll_config.value.push({path:params.response.path,sort:255});
        }else{
            layer.msg(params.response.error, {icon: 5, time:3000,shade:0.5,shadeClose:true});
        }
    });
});
var vm = new Vue({
    el:"#app",
    data:{
        tab:'site_config',
        config:<?php echo json_encode($config);?>
    },
    methods:{
        update:function(){
            //console.log(this.config[this.tab],JSON.stringify(this.config[this.tab]));
            var result = $('#'+this.tab).data('bootstrapValidator').validate().isValid();
            if (!result) return false;
            G.post("<?php echo base_url('system_setting/update_system_config');?>",this.config[this.tab]);
        },
        switch_tab:function(tab){
            this.tab = tab;
        },
        del_pic:function(key){
            this.config.imgscroll_config.value.splice(key,1);
        }
    },
});
</script>