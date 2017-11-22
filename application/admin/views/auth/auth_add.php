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
            <div class="box-body" id="auth_validator">
        		<div class="form-group">
					<label for="name" class="col-sm-2 control-label">名称</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" v-model="info.name" name="name" required>
					</div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="parent" class="col-sm-2 control-label">父级(左侧导航)</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="pid" v-model="info.pid" required>
                            <option v-for="na in nav" v-bind:value="na.id">
                                {{ na.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="parent_node" class="col-sm-2 control-label">父节点(权限继承)</label>
                    <div class="col-sm-10">
                        <select class="form-control select2 parent_node" multiple="multiple" name="parent_node" v-model="info.parent_node" required>
                            <option v-bind:value="0">--无--</option>
                            <option v-for="au in auth" v-bind:value="au.id">
                                {{ au.id + "_" +au.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="related_node" class="col-sm-2 control-label">关联节点(权限关联)</label>
                    <div class="col-sm-10">
                        <select class="form-control select2 related_node" multiple="multiple" name="related_node" v-model="info.related_node" required>
                            <option v-bind:value="0">--无--</option>
                            <option v-for="au in auth" v-bind:value="au.id">
                                {{ au.id + "_" +au.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="uri" class="col-sm-2 control-label">Uri</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" v-model="info.uri" name="uri" required>
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
                    <label for="auth_limit" class="col-sm-2 control-label">权限限制</label>
                    <div class="col-sm-10">
                        <span class="radio radio-danger radio-inline">
                            <input type="radio" name="auth_limit" id="radio_3" v-bind:value="1" v-model="info.auth_limit" required>
                            <label for="radio_3">开启</label>
                        </span>
                        <span class="radio radio-danger radio-inline">
                            <input type="radio" name="auth_limit" id="radio_4" v-bind:value="0" v-model="info.auth_limit" required>
                            <label for="radio_4">关闭</label>
                        </span>
                    </div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="is_left_nav" class="col-sm-2 control-label">左侧菜单显示</label>
                    <div class="col-sm-10">
                        <span class="radio radio-danger radio-inline">
                            <input type="radio" name="is_left_nav" id="radio_5" v-bind:value="1" v-model="info.is_left_nav" required>
                            <label for="radio_5">开启</label>
                        </span>
                        <span class="radio radio-danger radio-inline">
                            <input type="radio" name="is_left_nav" id="radio_6" v-bind:value="0" v-model="info.is_left_nav" required>
                            <label for="radio_6">关闭</label>
                        </span>
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
<!-- select2 -->
<script src="/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2().on('change', function() {
            vm.info[$(this).attr("name")] = $(this).val();
        });
        $("#auth_validator").bootstrapValidator();
    });
	var vm = new Vue({
		el:"#app",
        data:{
        	info:<?php echo json_encode($info);?>,
        	nav:<?php echo json_encode($second_nav);?>,
            auth:<?php echo json_encode($auth);?>
        },
        methods:{
        	update:function(){
                var result = $('#auth_validator').data('bootstrapValidator').validate().isValid();
                if (!result) return false;
                <?php if((int)$info["id"]):?>
                    G.post("<?php echo base_url('auth/auth_edit');?>"+"/"+this.info.id,this.info);
                <?php else:?>
        		    G.post("<?php echo base_url('auth/auth_add');?>",this.info);
                <?php endif;?>
        	},
        },
	});
</script>