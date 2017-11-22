<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-10-27T13:54:06+0800
 */
-->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css'); ?>">
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content" id="article_validator">
      <div class="row">
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">发表文章：</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="form-group">
                <input class="form-control" placeholder="标题" name="title" v-model="article.title" required>
              </div>
              <div class="form-group">
                <select class="form-control select2" multiple="multiple" data-placeholder="请选择文章关键词标签" style="width: 100%;" v-model="tags">
                    <?php foreach ($tags as $key => $value):?>
                        <option value="<?php echo $value["name"];?>"><?php echo $value["name"];?></option>
                    <?php endforeach;?>
                </select>
                <span>&nbsp;</span>
              </div>
              <div class="form-group">
                <textarea class="form-control" rows="3" placeholder="描述" name="description" v-model="article.description"></textarea>
              </div>
                <div class="form-group">
                    <label>缩略图：</label><br/>
                    <template v-if="article.thumb">
                        <img :src="article.thumb" class="img-responsive margin">
                    </template>
                    <input type="file" class="form-control input-sm file-loading" name="thumb" data-show-preview="false" id="article_thumb" placeholder="缩略图">
                    <div id="thumb_errorBlock"></div>
                    <p class="help-block">请上传图片格式文件,大小在1M以内.</p>
                </div>
              <div class="form-group">
                    <textarea id="container" name="content">{{article.content}}</textarea> 
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
                    <input type="text" class="form-control input-sm" id="author" name="author" v-model="article.author">
                  </div>
                </div>
                <div class="form-group">
                  <label for="from" class="col-sm-3 control-label">来源：</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" id="from" name="from" v-model="article.from">
                  </div>
                </div>
                <div class="form-group">
                  <label for="catid" class="col-sm-3 control-label">栏目：</label>
                  <div class="col-sm-9">
                    <select class="form-control" name="catid" v-model="article.catid" required min="1" data-bv-notempty-message="请选择文章栏目" data-bv-greaterthan-message="请选择文章栏目" data-bv-greaterthan-inclusive="true">
                        <option v-bind:value="0" checked>--请选择栏目--</option>
                        <?php foreach ($this->categorys as $key => $value):?>
                            <option value="<?php echo $value["id"];?>"><?php echo $value["name"];?></option>
                        <?php endforeach;?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="read" class="col-sm-3 control-label">阅读量：</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" id="read" name="read" v-model="article.read">
                  </div>
                </div>
                <div class="form-group">
                  <label for="like" class="col-sm-3 control-label">点赞量：</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" id="like" name="like" v-model="article.like">
                  </div>
                </div>
                <div class="form-group">
                  <label for="sort" class="col-sm-3 control-label">排序：</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" id="sort" name="sort" v-model="article.sort">
                  </div>
                </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
          <!-- <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">推荐展示：</h3>
                <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <table>
                <tr><td>
                    <span class="checkbox checkbox-success checkbox-inline">
                        <input type="checkbox" class="styled" value="1">
                        <label for="pid_1">首页幻灯</label>
                    </span>
                </td></tr>
                <tr><td>
                    <span class="checkbox checkbox-success checkbox-inline">
                        <input type="checkbox" class="styled" value="2">
                        <label for="pid_2">热门推荐</label>
                    </span>
                </td></tr>
                </table>
            </div>
          </div>
          /.box -->
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
<!-- select2 -->
<script type="text/javascript" src="/assets/plugins/select2/select2.full.min.js"></script>
<!-- fileinput -->
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/js/locales/zh.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/themes/fa/theme.js'); ?>"></script>
<script type="text/javascript">
var ue = UE.getEditor('container',{initialFrameHeight:500});
$(document).ready(function() {
    $("#article_validator").bootstrapValidator();
    G.fileinput($("#article_thumb"),{
        uploadUrl:"<?php echo base_url('article/upload_thumb');?>",
        maxFileSize:2048,
        elErrorContainer: "#thumb_errorBlock",
    },function(e,params){
        if (params.response.status) {
            vm.article.thumb = params.response.path;
        }else{
            layer.msg(params.response.msg, {icon: 5, time:3000,shade:0.5,shadeClose:true});
        }
    });
    $(".select2").select2({
        tags: true,
    }).on('change', function() {
        vm.tags = $(this).val();
    });
});
var vm = new Vue({
    el:"#app",
    data:{
        id:<?php echo $article["id"];?>,
        article:<?php echo json_encode($article);?>,
        tags:<?php echo json_encode(explode(",",$article["keywords"]));?>,
    },
    methods:{
        add:function(status){
            var result = $('#article_validator').data('bootstrapValidator').validate().isValid();
            if (!result) return false;
            this.article.status = parseInt(status);
            this.article.content = ue.getContent();
            this.article.keywords = this.tags.join(",");
            <?php if((int)$article["id"]):?>
            G.post("<?php echo base_url('article/edit');?>"+"/"+this.id,this.article);
            <?php else:?>
            G.post("<?php echo base_url('article/add');?>",this.article);
            <?php endif;?>
        },
    },
});
</script>