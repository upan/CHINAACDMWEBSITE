<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @authors MAGENJIE (1299234033@qq.com)
 * @date    2016-06-30 17:36:22
 * @version 1.0.0
 */
-->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">编辑</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                            class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body" id="info_validator">
        		<div class="form-group">
					<label for="name" class="col-sm-2 control-label">名称</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" v-model="info.name" name="name" required>
					</div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
					<label for="icon" class="col-sm-2 control-label">图标:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="icon" v-model="info.icon" required>
                        <p></p>
						<p class="text-green pull-right">
                            点击&nbsp;
                            <a href="<?php echo base_url('admin/icon');?>" target="_blank">链接</a>
                            &nbsp;有很多可选图标!
                            <span class="text-red">请输入图标右侧字符！</span>
                        </p>
					</div>
                </div>
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
                <span>&nbsp;</span>
                <div class="form-group">
					<label for="parent" class="col-sm-2 control-label">父级</label>
					<div class="col-sm-10">
						<select class="form-control" name="pid" v-model="info.pid" required>
	                        <option value="0">--无--</option>
	                        <option v-for="na in nav" v-bind:value="na.id">
							    {{ na.name }}
							</option>
                      	</select>
					</div>
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
            	<button type="button" class="btn btn-info pull-right" @click="update();">保存</button>
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php include VIEWPATH . 'footer_anchor.php'; ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#info_validator").bootstrapValidator();
    });
	var vm = new Vue({
		el:"#app",
        data:{
        	info:<?php echo json_encode($info);?>,
        	nav:<?php echo json_encode($first_nav);?>,
        },
        methods:{
        	update:function(){
                var result = $('#info_validator').data('bootstrapValidator').validate().isValid();
                if (!result) return false;
                <?php if((int)$info["id"]):?>
                    G.post("<?php echo base_url('admin/nav_edit');?>"+"/"+this.info.id,this.info);
                <?php else:?>
                    G.post("<?php echo base_url('admin/nav_add');?>",this.info);
                <?php endif;?>
        	},
        },
	});
</script>