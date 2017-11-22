<?php $this->load->view('include/header');?>
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">
                         预达时间质量<small> 统计</small>
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
                                        <th class="no-auto-space">日期</th>
                                        <th class="no-auto-space">机场</th>
                                        <th class="no-auto-space">落地前2H</th>
                                        <th class="no-auto-space">落地前1H</th>
                                        <th class="no-auto-space">落地前30M</th>
                                        <th class="no-auto-space">落地前15M</th>
                                        <th class="no-auto-space">落地前5M</th>
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
    var GET_JSON_DATA_URL = '<?php echo base_url('statistic/esttime_quality/json_data');?>';
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
</script>
</body>
</html>