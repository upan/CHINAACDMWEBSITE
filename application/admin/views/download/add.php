<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-10-27T13:54:06+0800
 */
-->
<!-- <style>
    [v-cloak] {
        display: none;
    }
</style> -->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css'); ?>">
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content" id="download_validator">
      <div class="row">
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">发表下载：</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="form-group">
                <input class="form-control" placeholder="标题" name="title" v-model="download.title" required>
              </div>
              <div class="form-group">
                <textarea class="form-control" rows="3" placeholder="描述" name="description" v-model="download.description"></textarea>
              </div>
                <div class="form-group">
                    <label>缩略图：</label><br/>
                    <template v-if="download.thumb">
                        <img :src="download.thumb" class="img-responsive margin">
                    </template>
                    <input type="file" class="form-control input-sm file-loading" accept="image/*" name="thumb" data-show-preview="false" id="download_thumb" placeholder="缩略图">
                    <div id="thumb_errorBlock"></div>
                    <p class="help-block">请上传图片格式文件,大小在1M以内.</p>
                </div>
                <div class="form-group">
                    <label>类型：</label><br/>
                    <span class="radio radio-danger radio-inline">
                        <input type="radio" name="type" id="radio_1" :value="1" v-model="download.type" required>
                        <label for="radio_1">Word文档</label>
                    </span>
                    <span class="radio radio-danger radio-inline">
                        <input type="radio" name="type" id="radio_2" :value="2" v-model="download.type" required>
                        <label for="radio_2">PDF文档</label>
                    </span>
                </div>
                <div class="form-group">
                    <label>文件：</label><br/>
                    <ul class="mailbox-attachments clearfix">
                        <li v-if="download.path">
                            <span class="mailbox-attachment-icon">
                                <i class="fa fa-file-pdf-o" v-if="download.type === 2"></i>
                                <i class="fa fa-file-word-o" v-else-if="download.type === 1"></i>
                                <i class="fa fa-file-word-o" v-else="download.type === 0"></i>
                            </span>
                            <div class="mailbox-attachment-info">
                                <a class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> {{ download.path.split("/")[3] }}</a>
                                <span class="mailbox-attachment-size">{{ download.size }} KB
                                    <a :href="download.path" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                </span>
                            </div>
                        </li>
                    </ul>
                    <input type="file" class="form-control input-sm file-loading" name="download_file" id="download_file" placeholder="下载文件">
                    <div id="file_errorBlock"></div>
                    <p class="help-block">请上传Word,PDF格式文件,大小在50M以内.</p>
                </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">基本信息：</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body form-horizontal">
                <div class="form-group">
                  <label for="author" class="col-sm-3 control-label">作者：</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" id="author" name="author" v-model="download.author">
                  </div>
                </div>
                <div class="form-group">
                  <label for="catid" class="col-sm-3 control-label">栏目：</label>
                  <div class="col-sm-9">
                    <select class="form-control" name="catid" v-model="download.catid" required min="1" data-bv-notempty-message="请选择文章栏目" data-bv-greaterthan-message="请选择文章栏目" data-bv-greaterthan-inclusive="true">
                        <option v-bind:value="0" checked>--请选择栏目--</option>
                        <?php foreach ($this->categorys as $key => $value):?>
                            <option value="<?php echo $value["id"];?>"><?php echo $value["name"];?></option>
                        <?php endforeach;?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="down" class="col-sm-3 control-label">下载量：</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" id="down" name="down" v-model="download.down">
                  </div>
                </div>
                <div class="form-group">
                  <label for="sort" class="col-sm-3 control-label">排序：</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" id="sort" name="sort" v-model="download.sort">
                  </div>
                </div>
            </div>
          </div>
          <button class="btn btn-info btn-block margin-bottom" @click="add(-1);">存草稿</button>
          <button class="btn btn-primary btn-block margin-bottom" @click="add(1);">发布</button>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<?php include VIEWPATH . 'footer_anchor.php'; ?>
<!-- ueditor -->
<script type="text/javascript" src="/assets/plugins/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/assets/plugins/ueditor/ueditor.all.js"></script>
<!-- fileinput -->
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/js/locales/zh.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/themes/fa/theme.js'); ?>"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#download_validator").bootstrapValidator();
    G.fileinput($("#download_thumb"),{
        uploadUrl:"<?php echo base_url('download/upload_thumb');?>",
        maxFileSize:2048,
        elErrorContainer: "#thumb_errorBlock",
    },function(e,params){
        if (params.response.status) {
            vm.download.thumb = params.response.path;
        }else{
            layer.msg(params.response.msg, {icon: 5, time:3000,shade:0.5,shadeClose:true});
        }
    });
    G.fileinput($("#download_file"),{
        uploadUrl:"<?php echo base_url('download/upload_file');?>",
        maxFileSize:51200,
        allowedFileExtensions:['doc','pdf','docx'],
        elErrorContainer: "#file_errorBlock",
    },function(e,params){
        if (params.response.status) {
            vm.download.path = params.response.path;
            vm.download.size = params.response.size;
        }else{
            layer.msg(params.response.msg, {icon: 5, time:3000,shade:0.5,shadeClose:true});
        }
    });
});
var vm = new Vue({
    el:"#app",
    data:{
        id:<?php echo $download["id"];?>,
        download:<?php echo json_encode($download);?>,
    },
    methods:{
        add:function(status){
            var result = $('#download_validator').data('bootstrapValidator').validate().isValid();
            if (!result) return false;
            this.download.status = parseInt(status);
            if (empty(this.download.path)) {
                layer.msg('请上传文件', {icon: 5, time:3000,shade:0.5,shadeClose:true});
                return false;
            }
            <?php if((int)$download["id"]):?>
            G.post("<?php echo base_url('download/edit');?>"+"/"+this.id,this.download);
            <?php else:?>
            G.post("<?php echo base_url('download/add');?>",this.download);
            <?php endif;?>
        },
    },
});
</script>