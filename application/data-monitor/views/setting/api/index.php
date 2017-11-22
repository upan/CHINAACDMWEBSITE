<?php $this->load->view('include/header');?>
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">
                         接口管理<small> 监控基站需要监控的接口设置</small>
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
                                        <th class="no-auto-space">接口名</th>
                                        <th class="no-auto-space">应用机场</th>
                                        <th class="no-auto-space">协议</th>
                                        <th class="no-auto-space">URL</th>
                                        <th class="no-auto-space">状态</th>
                                        <th class="no-auto-space">作者</th>
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
    var GET_JSON_DATA_URL = '<?php echo base_url('setting/api/json_data');?>';
    var CREATE_URL = '<?php echo base_url('setting/api/create');?>';
    var CREATE_EXEC_URL = '<?php echo base_url('setting/api/create_exec');?>';
    var EDIT_URL = '<?php echo base_url('setting/api/edit');?>';
    var EDIT_EXEC_URL = '<?php echo base_url('setting/api/edit_exec');?>';
    var DELETE_EXEC_URL = '<?php echo base_url('setting/api/delete_exec');?>';
    var column_defs = [
        {
            "targets" : 6,//操作按钮目标列
            "render" : function(data, type, row) {
                    var html = '<button class="btn btn-primary btn-xs btn-edit" data-id="' + row[6] + '"> 编辑</button>&nbsp;<button class="btn btn-danger btn-xs btn-delete" data-id="' + row[6] + '" data-text="' + row[0] + '">删除</button>';
                    return html;
            }
        },
        {
            "targets" : 0,//操作按钮目标列
            "render" : function(data, type, row) {
                var html = '<span class="show-info" id="api_name' + row[6] + '" data-title="' + row[7] + '">' + row[0] + '</span>';
                return html;
            },
            "width":"170px"
        },
        {
            "targets" : 3,//操作按钮目标列
            "render" : function(data, type, row) {
                var html = '<span id="api_url' + row[6] + '" data-title="' + row[8] + '">' + row[3] + '</span>';
                return html;
            },
            "width":"436px"
        },
        {
            "targets" : 5,//操作按钮目标列
            "width":"43px"
        }
    ];
    var data_table = data_table_init('#data_table',10,GET_JSON_DATA_URL,column_defs);
    //全文本显示
    $(document).on('mouseover','.show-info', function(){
        var id = $(this).attr('id');
        layer.tips($(this).attr('data-title'), '#' + id, {
            tips: [1, '#3595CC']
        });
    });
    $(document).on('mouseout','.show-info', function(){
        layer.closeAll();
    });
    //添加弹层
    $(document).on('click','.btn-add', function(){
        var msg_layer = layer.msg('正在加载中...', {icon: 16 ,shade: 0.4, time:0});
        $.get(CREATE_URL,function(html){
            layer.open({
                type: 1,
                title : '<strong>新增</strong>',
                area : ['1040px','660px'],
                btn: ['确认','取消'],
                shadeClose: true,
                yes: function() {
                    if ($("#url").val().trim().length < 1) {
                        layer.tips('请填写接口URL', '#url');
                        $('#url').select();
                        return false;
                    }
                    if (!check_url_no_protocol($("#url").val().trim())) {
                        layer.tips('URL格式不正确', '#url');
                        $('#url').select();
                        return false;
                    }
                    if ($("#name").val().trim().length < 1) {
                        layer.tips('请填写接口名称', '#name');
                        $('#name').select();
                        return false;
                    }
                    if ($('#check_type').val() == 'status')
                    {
                        if ($("#status_key").val().trim().length < 1) {
                            layer.tips('请填写接口返回状态键名', '#status_key');
                            $('#status_key').select();
                            return false;
                        }
                        if ($("#status_normal_value").val().trim().length < 1) {
                            layer.tips('请填写接口返回状态正常键值', '#status_normal_value');
                            $('#status_normal_value').select();
                            return false;
                        }
                    }
                    if ($("#author_staff").val() == 0) {
                        layer.tips('请选择接口作者', '#author_staff');
                        $('#author_staff').select();
                        return false;
                    }
                    var error_flg = false;
                    var error_node = null;
                    $("input[name='param_key[]']").each(function(){
                        var param_key = $(this);
                        var param_val = $(this).siblings("input[name='param_val[]']");
                        if (param_key.val().trim().length > 0 && param_val.val().trim().length < 1){
                            error_flg = true;
                            error_node = param_val;
                            return false;
                        }
                        if (param_val.val().trim().length > 0 && param_key.val().trim().length < 1){
                            error_flg = true;
                            error_node = param_key;
                            return false;
                        }
                    });
                    if(error_flg == true)
                    {
                        error_node.attr('id',"error_node")
                        layer.tips('如设定参数则键名和键值都需填写', '#error_node');
                        $('#error_node').select();
                        error_node.removeAttr("id");
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
                area : ['1040px','630px'],
                btn: ['确认','取消'],
                shadeClose: true,
                yes: function() {
                    if ($("#url").val().trim().length < 1) {
                        layer.tips('请填写接口URL', '#url');
                        $('#url').select();
                        return false;
                    }
                    if (!check_url_no_protocol($("#url").val().trim())) {
                        layer.tips('URL格式不正确', '#url');
                        $('#url').select();
                        return false;
                    }
                    if ($("#name").val().trim().length < 1) {
                        layer.tips('请填写接口名称', '#name');
                        $('#name').select();
                        return false;
                    }
                    if ($('#check_type').val() == 'status')
                    {
                        if ($("#status_key").val().trim().length < 1) {
                            layer.tips('请填写接口返回状态键名', '#status_key');
                            $('#status_key').select();
                            return false;
                        }
                        if ($("#status_normal_value").val().trim().length < 1) {
                            layer.tips('请填写接口返回状态正常键值', '#status_normal_value');
                            $('#status_normal_value').select();
                            return false;
                        }
                    }
                    if ($("#author_staff").val() == 0) {
                        layer.tips('请选择接口作者', '#author_staff');
                        $('#author_staff').select();
                        return false;
                    }
                    var error_flg = false;
                    var error_node = null;
                    $("input[name='param_key[]']").each(function(){
                        var param_key = $(this);
                        var param_val = $(this).siblings("input[name='param_val[]']");
                        if (param_key.val().trim().length > 0 && param_val.val().trim().length < 1){
                            error_flg = true;
                            error_node = param_val;
                            return false;
                        }
                        if (param_val.val().trim().length > 0 && param_key.val().trim().length < 1){
                            error_flg = true;
                            error_node = param_key;
                            return false;
                        }
                    });
                    if(error_flg == true)
                    {
                        error_node.attr('id',"error_node")
                        layer.tips('如设定参数则键名和键值都需填写', '#error_node');
                        $('#error_node').select();
                        error_node.removeAttr("id");
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

    $(document).on('change','#check_type',function(){
        var type = $(this).val();
        if(type == 'status'){
            $('#status_node').show();
            $('#status_key').val('');
            $('#status_normal_value').val('');
        }else if (type == 'not_empty'){
            $('#status_node').hide();
            $('#status_key').val('');
            $('#status_normal_value').val('');
        }
    });
</script>
</body>
</html>