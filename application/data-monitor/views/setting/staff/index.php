<?php $this->load->view('include/header');?>
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">
                         员工管理<small> 飞友员工管理</small>
                        <div class="pull-right">
                            <button class="btn btn-primary btn-sm btn-add">新增</button>
                        </div>
                    </h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="data_table">
                                    <thead>
                                    <tr>
                                        <th class="no-auto-space">ID</th>
                                        <th class="no-auto-space">姓名</th>
                                        <th class="no-auto-space">手机号</th>
                                        <th class="no-auto-space">EMAIL</th>
                                        <th class="no-auto-space">部门</th>
                                        <th class="no-auto-space">岗位</th>
                                        <th class="no-auto-space">操作</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var GET_JSON_DATA_URL = '<?php echo base_url('setting/staff/json_data');?>';
    var CREATE_URL = '<?php echo base_url('setting/staff/create');?>';
    var CREATE_EXEC_URL = '<?php echo base_url('setting/staff/create_exec');?>';
    var EDIT_URL = '<?php echo base_url('setting/staff/edit');?>';
    var EDIT_EXEC_URL = '<?php echo base_url('setting/staff/edit_exec');?>';
    var DELETE_EXEC_URL = '<?php echo base_url('setting/staff/delete_exec');?>';
    var column_defs = [ {
        "targets" : 6,//操作按钮目标列
        "render" : function(data, type, row) {
            var html = '<button class="btn btn-primary btn-xs btn-edit" data-id="' + row[0] + '"> 编辑</button>&nbsp;<button class="btn btn-danger btn-xs btn-delete" data-id="' + row[0] + '" data-text="' + row[1] + '">删除</button>';
            return html;
        }
    } ];
    var data_table = data_table_init('#data_table',10,GET_JSON_DATA_URL,column_defs);
    //添加弹层
    $(document).on('click','.btn-add', function(){
        var msg_layer = layer.msg('正在加载中...', {icon: 16 ,shade: 0.4, time:0});
        $.get(CREATE_URL,function(html){
            layer.open({
                type: 1,
                title : '<strong>新增</strong>',
                area : ['570px','600px'],
                btn: ['确认','取消'],
                shadeClose: true,
                yes: function() {
                    if ($("#name").val().trim().length < 1) {
                        layer.tips('请填写员工姓名', '#name');
                        $('#name').select();
                        return false;
                    }
                    if ($("#mobile").val().trim().length < 1) {
                        layer.tips('请填写员工手机号码', '#mobile');
                        $('#mobile').select();
                        return false;
                    }
                    if (check_mobile($("#mobile").val()) == false) {
                        layer.tips('手机号码格式不正确', '#mobile');
                        $('#mobile').select();
                        return false;
                    }
                    if ($("#email").val().trim().length < 1) {
                        layer.tips('请填写员工工作邮箱', '#email');
                        $('#email').select();
                        return false;
                    }
                    if (check_email($("#email").val()) == false) {
                        layer.tips('工作邮箱格式错误', '#email');
                        $('#email').select();
                        return false;
                    }
                    if ($("#department").val().length < 1) {
                        layer.tips('请选择员工所属部门', '#department');
                        $('#department').select();
                        return false;
                    }
                    var msg_layer = layer.msg('正在提交中...', {icon: 16 ,shade: 0.4, time:0});
                    $.post(CREATE_EXEC_URL, $('#exec_form').serialize(), function(result){
                        layer.close(msg_layer);
                        var data = eval('(' + result + ')');
                        if (data.success == 1){
                            layer.closeAll();
                            data_table.api().ajax.reload(function(){
                                layer.msg(data.text, {time:2000,skin:'layui-bg-black'});
                            }, false);
                        } else {
                            layer.msg(data.text, {time:3000,skin:'layui-bg-black'});
                        }
                    });
                },
                btn2: function() {
                    layer.closeAll();
                },
                content: html,
                success: function(){
                    layer.close(msg_layer);
                }
            });
        });
    });

    //编辑弹层
    $(document).on('click','.btn-edit',function(){
        var msg_layer = layer.msg('正在加载中...', {icon: 16 ,shade: 0.4, time:0});
        $.get(EDIT_URL, {id:$(this).attr('data-id')}, function(html){
            layer.open({
                type: 1,
                title : '<strong>编辑</strong>',
                area : ['570px','600px'],
                btn: ['确认','取消'],
                shadeClose: true,
                yes: function() {
                    if ($("#name").val().trim().length < 1) {
                        layer.tips('请填写员工姓名', '#name');
                        $('#name').select();
                        return false;
                    }
                    if ($("#mobile").val().trim().length < 1) {
                        layer.tips('请填写员工手机号码', '#mobile');
                        $('#mobile').select();
                        return false;
                    }
                    if (check_mobile($("#mobile").val()) == false) {
                        layer.tips('手机号码格式不正确', '#mobile');
                        $('#mobile').select();
                        return false;
                    }
                    if ($("#email").val().trim().length < 1) {
                        layer.tips('请填写员工工作邮箱', '#email');
                        $('#email').select();
                        return false;
                    }
                    if (check_email($("#email").val()) == false) {
                        layer.tips('工作邮箱格式错误', '#email');
                        $('#email').select();
                        return false;
                    }
                    if ($("#department").val().length < 1) {
                        layer.tips('请选择员工所属部门', '#department');
                        $('#department').select();
                        return false;
                    }
                    var msg_layer = layer.msg('正在提交中...', {icon: 16 ,shade: 0.4, time:0});
                    $.post(EDIT_EXEC_URL, $('#exec_form').serialize(), function(result){
                        layer.close(msg_layer);
                        var data = eval('(' + result + ')');
                        if (data.success == 1){
                            layer.closeAll();
                            data_table.api().ajax.reload(function(){
                                layer.msg(data.text, {time:2000,skin:'layui-bg-black'});
                            }, false);
                        } else {
                            layer.msg(data.text, {time:3000,skin:'layui-bg-black'});
                        }
                    });
                },
                btn2: function() {
                    layer.closeAll();
                },
                content: html,
                success: function(){
                    layer.close(msg_layer);
                }
            });
        });
    });

    //删除弹层
    $(document).on('click','.btn-delete',function(){
        var data_id = $(this).attr('data-id');
        var data_text = $(this).attr('data-text');
        layer.msg('是否确定删除数据：' + data_text + '？', {
            time: 0,
            skin:'layui-bg-black',
            btn: ['确定', '取消'],
            yes: function(){
                var msg_layer = layer.msg('正在提交中...', {icon: 16 ,shade: 0.4, time:0});
                $.post(DELETE_EXEC_URL,{id:data_id}, function(result){
                    layer.close(msg_layer);
                    data = eval('(' + result + ')');
                    if (data.success == 1){
                        layer.closeAll();
                        data_table.api().ajax.reload(function(){
                            layer.msg(data.text, {time:2000,skin:'layui-bg-black'});
                        }, false);
                    } else {
                        layer.msg(data.text, {time:3000,skin:'layui-bg-black'});
                    }
                });
            }
        });
    });
</script>
</body>
</html>