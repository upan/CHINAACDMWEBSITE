<form id="exec_form">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-body">

                <div class="form-group">
                    <label>名称</label>
                    <div class="form-control-static"><?php echo $info['name'];?></div>
                </div>
                <div class="form-group">
                    <label>应用机场</label>
                    <div class="form-control-static"><?php echo $info['airport'];?></div>
                </div>
                <div class="form-group">
                    <label>责任人</label>
                    <div class="form-control-static"><?php echo $info['staff_name'];?></div>
                </div>
                <div class="form-group">
                    <label>指标请求URL</label>
                    <div class="form-control-static" style="word-wrap:break-word;"><?php echo $info['url'];?></div>
                </div>
                <div class="form-group">
                    <label>判定信息</label>
                    <div class="form-control-static" style="word-wrap:break-word;"><?php echo $info['info'];?></div>
                </div>
                <div class="form-group">
                    <label>告警内容</label>
                    <div class="form-control-static" style="word-wrap:break-word;"><?php echo html_entity_decode(stripslashes($info['content']));?></div>
                </div>
                <div class="form-group">
                    <label>中断时间 / 恢复时间</label>
                    <div class="form-control-static"><?php echo date('Y-m-d H:i:s', $info['stoppage_time']);?> / <?php echo !empty($info['recover_time']) ? date('Y-m-d H:i:s', $info['recover_time']) : '-';?></div>
                </div>

            </div>
        </div>
    </div>
</form>