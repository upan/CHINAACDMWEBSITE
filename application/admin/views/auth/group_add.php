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
            <div class="box-body" id="group_validator">
                <div class="form-group">
                    <label class="col-sm-2 control-label">名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" v-model="info.name" name="name" required>
                    </div>
                </div>
                <span>&nbsp;</span>

                <div class="form-group">
                    <label class="col-sm-2 control-label">状态</label>

                    <div class="col-sm-10">
                        <span class="radio radio-danger radio-inline">
                            <input type="radio" name="status" id="radio_1" v-bind:value="1" v-model="info.status"
                                   required>
                            <label for="radio_1">
                                正常
                            </label>
                        </span>
                        <span class="radio radio-danger radio-inline">
                            <input type="radio" name="status" id="radio_2" v-bind:value="0" v-model="info.status"
                                   required>
                            <label for="radio_2">
                                禁用
                            </label>
                        </span>
                    </div>
                </div>
                <span>&nbsp;</span>

                <div class="form-group">
                    <label class="col-sm-2 control-label">权限</label>

                    <div class="col-sm-10">
                        <div class="box-body no-padding">
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th style="width:150px;">Categroy</th>
                                    <th>Auth</th>
                                </tr>
                                <?php foreach ($auth as $key => $value): ?>
                                    <?php $id = $value['id']; ?>
                                    <tr>
                                        <td>
                                            <span class="checkbox checkbox-success checkbox-inline">
                                                <?php 
                                                    $cat_auth = $categroy[$value['id']];
                                                    $_1 = !empty(array_intersect($categroy[$value['id']],$info['auth']));
                                                    $_2 = !empty(array_intersect($categroy[$value['id']],$system));
                                                ?>
                                                <input type="checkbox" class="styled" value="<?php echo $value['id']; ?>" @click="checkParent(<?php echo $value['id'];?>);" id="pid_<?php echo $value['id'];?>" <?php if($_1 || $_2):?>checked<?php endif;?> <?php if($_2):?>disabled<?php endif;?> >
                                                <label for="pid_<?php echo $value['id']; ?>">
                                                    <?php echo $value['name']; ?>
                                                </label>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($value['child'])): ?>
                                                <?php foreach ($value['child'] as $ke => $val): ?>
                                                    <?php if(in_array($val['id'],$system)):?>
                                                    <span class="checkbox checkbox-success checkbox-inline">
                                                        <input type="checkbox" class="styled" value="<?php echo $val['id'];?>" checked disabled>
                                                        <label for="cid_<?php echo $val['id'];?>">
                                                            <?php echo $val['name'];?>
                                                        </label>
                                                    </span>
                                                    <?php else:?>
                                                    <span class="checkbox checkbox-success checkbox-inline">
                                                        <input type="checkbox" class="styled" v-model="info.auth"
                                                               v-bind:value="<?php echo $val['id']; ?>" id="cid_<?php echo $val['id']; ?>" @click="childCheck(<?php echo $value['id'];?>);">
                                                        <label for="cid_<?php echo $val['id']; ?>">
                                                            <?php echo $val['name']; ?>
                                                        </label>
                                                    </span>
                                                    <?php endif;?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
    $(document).ready(function () {
        $("#group_validator").bootstrapValidator();
    });
    var vm = new Vue({
        el: "#app",
        data: {
            info:<?php echo json_encode($info);?>,
            categroy:<?php echo json_encode($categroy);?>,
            system:<?php echo json_encode($system);?>
        },
        methods: {
            update: function () {
                var result = $('#group_validator').data('bootstrapValidator').validate().isValid();
                if (!result) return false;
                <?php if((int)$info["id"]):?>
                G.post("<?php echo base_url('auth/group_edit');?>"+"/"+this.info.id, this.info);
                <?php else:?>
                G.post("<?php echo base_url('auth/group_add');?>", this.info);
                <?php endif;?>
            },
            checkParent:function (pid){
                var child = this.categroy[pid];
                //console.log(pid,child);
                var ischecked = $('#pid_'+pid).is(':checked');
                var _ = this;
                $.each(child, function(index,cid) {
                    $('#cid_'+cid).prop('checked', ischecked);
                    var i = _.info.auth.indexOf(cid);
                    if (i >= 0) {
                        !ischecked && _.info.auth.splice(i,1);
                    }else{
                        ischecked && _.info.auth.push(cid);
                    }
                });
            },
            childCheck:function (pid){
                var child = this.categroy[pid];
                var ischecked = false;
                $.each(child, function(index,cid) {
                    if ($('#cid_'+cid).is(':checked')) ischecked = true;
                });
                $('#pid_'+pid).prop('checked',ischecked);
            }
        },
    });
</script>