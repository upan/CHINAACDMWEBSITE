<?php $this->load->view('include/header');?>
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">
                         告警日志<small> 监控基站和各成员机场A-CDM监控告警记录</small>
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
                                        <th class="no-auto-space">名称</th>
                                        <th class="no-auto-space">应用机场</th>
                                        <th class="no-auto-space">信息</th>
                                        <th class="no-auto-space">责任人</th>
                                        <th class="no-auto-space">中断时间</th>
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
    var GET_JSON_DATA_URL = '<?php echo base_url('logs/notice_log/json_data');?>';
    var DETAIL_URL = '<?php echo base_url('logs/notice_log/detail');?>';
    var column_defs = [ {
        "targets" : 5,//操作按钮目标列
        "render" : function(data, type, row) {
            var html = '<button class="btn btn-info btn-xs btn-detail" data-id="' + row[5] + '">详情';
            return html;
        }
    } ];
    data_table_init('#data_table',10,GET_JSON_DATA_URL,column_defs);

    //详情
    $(document).on('click','.btn-detail', function(){
        var msg_layer = layer.msg('正在加载中...', {icon: 16 ,shade: 0.4, time:0});
        $.get(DETAIL_URL, {id:$(this).attr('data-id')}, function(html){
            layer.open({
                type: 1,
                title : '<strong>详情</strong>',
                area : ['740px','620px'],
                shadeClose: true,
                content: html,
                success: function(){
                    layer.close(msg_layer);
                }
            });
        });
    });
</script>
</body>
</html>