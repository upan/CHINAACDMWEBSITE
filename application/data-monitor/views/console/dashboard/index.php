<?php $this->load->view('include/header');?>
<div class="row">
    <div class="col-md-12">
        <h3 class="page-header">
            运行实况<small> 监控基站和各成员机场A-CDM监控汇总</small>
        </h3>

        <div class="row">
            <div class="col-xs-3">
                <div class="panel panel-primary text-center no-boder bg-color-green">
                    <div class="panel-footer back-footer-green">
                        <i class="fa fa-link fa-1x"></i> 正常 (<?php echo $status_total['normal'];?>)
                    </div>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="panel panel-primary text-center no-boder bg-color-red">
                    <div class="panel-footer back-footer-red">
                        <i class="fa fa-unlink fa-1x"></i> 中断 (<?php echo $status_total['abnormal'];?>)
                    </div>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="panel panel-primary text-center no-boder bg-color-brown">
                    <div class="panel-footer back-footer-brown">
                        <i class="fa fa-exclamation fa-1x"></i> 今日告警 (<?php echo $status_total['notice'];?>)
                    </div>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="panel panel-primary text-center no-boder bg-color-gray">
                    <div class="panel-footer back-footer-gray">
                        <i class="fa fa-power-off fa-1x"></i> 未监控 (<?php echo $status_total['uncheck'];?>)
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        接口监控状态
                    </div>
                    <div class="panel-body">
                        <?php
                        foreach($api_list as $item) {
                            $_class = 'default';
                            $_text = '未监控';
                            if($item['monitor_status'] != 'uncheck')
                            {
                                $_class = $item['monitor_status'] == 'normal' ? 'success' : 'danger';
                                $_text = $item['monitor_status'] == 'normal' ? '正常' : '中断';
                            }
                        ?>
                            <div class="line line-<?php echo $_class;?>">
                                <span class="badge-primary"><?php echo $item['airport_iata'];?></span>
                                <span class="badge-info"><?php echo $item['staff_department'];?>-<?php echo $item['staff_name'];?></span>
                                &nbsp;<?php echo $item['name'];?>
                                <span class="label label-<?php echo $_class;?> label-right">
                                    <?php echo $_text;?>
                                </span>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        数据源监控状态
                    </div>
                    <div class="panel-body">
                        <?php
                        foreach($data_source as $key => $item) {
                            $_class = 'default';
                            $_text = '未监控';
                            if($item['monitor_status'] != 'uncheck')
                            {
                                $_class = $item['monitor_status'] == 'normal' ? 'success' : 'danger';
                                $_text = $item['monitor_status'] == 'normal' ? '正常' : '中断';
                            }
                            ?>
                            <div class="line line-<?php echo $_class;?>">
                                <span class="badge-primary"><?php echo $item['airport_iata'];?></span>
                                <span class="badge-info"><?php echo $key;?></span>
                                &nbsp;<?php echo $item['name'];?>
                                <span class="label label-<?php echo $_class;?> label-right">
                                    <?php echo $_text;?>
                                </span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
</div>
</div>
</body>
</html>