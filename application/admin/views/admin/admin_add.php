<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @authors MAGENJIE (1299234033@qq.com)
 * @date    2016-06-30 17:36:22
 * @version 1.0.0
 */
-->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css'); ?>">
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
            <div class="box-body" id="admin_validator">
        		<div class="form-group">
					<label for="username" class="col-sm-2 control-label">用户名</label>
					<div class="col-sm-10" id="username">
                        <span v-if="info.id" class="text-light-blue" style="font-size:18px;"><b>{{info.username}}</b></span>
						<input v-else type="text" class="form-control" v-model.lazy="info.username" name="username" required pattern="[a-zA-Z0-9_\.]+" data-bv-regexp-message="用户名只能是字母 数字 下划线以及.的组合"
                        minlength="5" maxlength="10" data-bv-stringlength-message="用户名要求长度在5~10范围内字符的组合">
					</div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="password" class="col-sm-2 control-label">密码</label>
                    <div class="col-sm-10">
                        <input v-if="info.id" type="text" class="form-control" v-model="info.password" name="password" minlength="6" data-bv-stringlength-message="密码至少为6个字符长度！">
                        <input v-else type="text" class="form-control" v-model="info.password" name="password" required minlength="6" data-bv-stringlength-message="密码至少为6个字符长度！">
                    </div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="company" class="col-sm-2 control-label">工作单位</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" v-model="info.company" name="company" required placeholder="请输入工作单位">
                    </div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="photo" class="col-sm-2 control-label">用户头像</label>
                    <div class="col-sm-10">
                        <template v-if="info.photo">
                            <img :src="info.photo" class="img-responsive margin">
                        </template>
                        <input type="file" class="form-control input-sm file-loading" name="photo" data-show-preview="false" id="photo" placeholder="用户头像">
                        <p class="help-block">请上传图片格式文件,大小在2M以内.</p>
                    </div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="truename" class="col-sm-2 control-label">真实姓名</label>
                    <div class="col-sm-10" id="truename">
                        <span v-if="info.id" class="text-light-blue" style="font-size:18px;"><b>{{info.truename}}</b></span>
                        <input v-else type="text" class="form-control" v-model="info.truename" name="truename" required placeholder="该管理员的真实姓名,保存后不可修改！">
                    </div>
                </div>
                <span>&nbsp;</span>
                <div class="form-group">
                    <label for="phone" class="col-sm-2 control-label">手机号码</label>
                    <div class="col-sm-10" id="phone">
                        <span v-if="info.id" class="text-light-blue" style="font-size:18px;"><b>{{info.phone}}</b></span>
                        <input v-else type="text" class="form-control" v-model="info.phone" name="phone" required placeholder="该管理员的手机号码,保存后不可修改！">
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
					<label for="parent" class="col-sm-2 control-label">所属角色组</label>
					<div class="col-sm-10">
						<select class="form-control" name="gid" v-model="info.gid" required>
	                        <option v-for="gr in group" v-bind:value="gr.id">
							    {{ gr.name }}
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
<!-- fileinput -->
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/js/locales/zh.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-fileinput/themes/fa/theme.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#admin_validator").bootstrapValidator({
            fields: {
                username: {
                    message: '用户名已存在!',
                    validators: {
                        remote: {
                            type: 'POST',
                            url: "<?php echo base_url('admin/check_username_exists');?>",
                            message: '用户名已存在！',
                            delay: 1000
                        },
                    }
                },
                password: {
                    validators: {
                        different: {
                            field: 'username',
                            message: '密码不能与用户名相同！'
                        },
                    }
                },
               
            },
        });
        G.fileinput($("#photo"),{
            uploadUrl:"<?php echo base_url('admin/upload_photo');?>",
            maxFileSize:10240,
        },function(e,params){
            if (params.response.status) {
                vm.info.photo = params.response.path;
            }else{
                layer.msg(params.response.msg, {icon: 5, time:3000,shade:0.5,shadeClose:true});
            }
        });
    });
	var vm = new Vue({
		el:"#app",
        data:{
        	info:<?php echo json_encode($info);?>,
        	group:<?php echo json_encode($group);?>,
        },
        methods:{
        	update:function(){
        		var result = $('#admin_validator').data('bootstrapValidator').validate().isValid();
                if (!result) return false;
                <?php if((int)$info["id"]):?>
                    G.post("<?php echo base_url('admin/admin_edit');?>"+"/"+this.info.id,this.info);
                <?php else:?>
                    G.post("<?php echo base_url('admin/admin_add');?>",this.info);
                <?php endif;?>
        	},
        },
	});
</script>