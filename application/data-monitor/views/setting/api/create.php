<form id="exec_form">
<div class="col-lg-12">
    <div class="panel">
        <div class="panel-body">

            <div class="col-lg-12">
                <div class="form-group">
                    <label style="color:red;">注意：为避免无效告警，新增和编辑接口会当即判断接口是否正常。如果操作失败并提示该类错误信息，请检查参数，验证，URL等各项设置后再次提交。</label>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <div><label>URL</label><span class="small-desc-red">*</span></div>
                    <label class="radio-inline">
                        <input type="radio" name="protocol" value="http" checked="checked">http
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="protocol" value="https">https
                    </label>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="form-group">
                    <div><label></label></div>
                        <input class="form-control" placeholder="不包含协议名和参数的URL地址，如www.chinaacdm.com/api/output" name="url" id="url" />
                </div>
            </div>

            <div class="col-lg-8">
                <div class="col-left">
                    <div class="form-group">
                        <label>应用机场</label><span class="small-desc-red">*</span>
                        <select class="form-control" name="airport_iata">
                            <?php
                            foreach ($member_airport_list as $item) {
                                ?>
                                <option value="<?php echo $item['airport_iata'];?>"><?php echo $item['airport_iata'];?> <?php echo $item['cn_name'];?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>接口作者</label><span class="small-desc-red">*</span>
                        <select class="form-control" name="author_staff" id="author_staff">
                            <option value="0">请选择</option>
                            <?php
                            foreach ($feeyo_staff_list as $item) {
                                ?>
                                <option value="<?php echo $item['feeyo_staff_id'];?>"><?php echo !empty($item['department']) ? $item['department'] : '未知';?> - <?php echo $item['name'];?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div><label>接口状态</label><span class="small-desc-999">停用的接口不会被监控</span></div>
                        <label class="radio-inline">
                            <input type="radio" name="is_enable" value="1" checked="checked">启用
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="is_enable" value="0">停用
                        </label>
                    </div>
                </div>
                <div class="col-right">
                    <div class="form-group">
                        <label>名称</label><span class="small-desc-red">*</span>
                        <input class="form-control" placeholder="接口名称" name="name" id="name"/>
                    </div>
                    <div class="form-group">
                        <label>相关员工</label><span class="small-desc-999">选定的员工会一并收到告警</span>
                        <select multiple="" class="form-control" name="relevant_staff[]">
                            <?php
                            foreach ($feeyo_staff_list as $item) {
                                ?>
                                <option value="<?php echo $item['feeyo_staff_id'];?>"><?php echo !empty($item['department']) ? $item['department'] : '未知';?> - <?php echo $item['name'];?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div><label>监控判定方式</label><span class="small-desc-red">*</span><span class="small-desc-999">监控用哪种方式判断接口是否正常</span></div>
                    <select class="form-control width-200" style="display:inline-block" name="check_type" id="check_type">
                        <option value="status">返回状态值等于正常值</option>
                        <option value="not_empty"> 返回数据非空</option>
                    </select>
                    <span id="status_node" style="margin-left:5px;">
                        键：<input class="form-control width-160" style="display:inline-block" placeholder="状态键名，如status" name="status_key" id="status_key" />
                        值：<input class="form-control width-160" style="display:inline-block" placeholder="状态正常值，如200" name="status_normal_value" id="status_normal_value" /></span>
                </div>
                <div class="form-group">
                    <label>说明</label>
                    <textarea class="form-control" rows="3" maxlength="255" name="description"></textarea>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label>类别</label>
                    <select class="form-control" name="type">
                        <?php
                        foreach ($api_type as $id => $item) {
                            ?>
                            <option value="<?php echo $id;?>"><?php echo $item;?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>接口URL验证</label><span class="small-desc-999">有些接口需要验证请求URL是否合法</span>
                    <select class="form-control" name="url_verify">
                        <option value="0">无验证</option>
                        <?php
                        foreach ($api_verify_type as $id => $item) {
                            ?>
                            <option value="<?php echo $id;?>"><?php echo $item['desc'];?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>请求时间戳变量名</label><span class="small-desc-999">有些接口需验证请求时间</span>
                    <input class="form-control" placeholder="如果需验证，请在此填写变量名" name="param_request_time" />
                </div>
                <div class="form-group" id="param_div">
                    <div><label>请求参数设定</label><span class="small-desc-999">如果没有则无需设定</span><button type="button" class="btn btn-primary btn-xs" style='float:right;' id="add_new_param"> 添加设定</button></div>
                </div>
            </div>

        </div>
    </div>
</div>
</form>
<script type="text/javascript">
    $('#add_new_param').on('click',function(){
        var html = '<div style="margin-top:4px;"><input class="form-control width-140" style="display:inline-block" placeholder="请输入键名" name="param_key[]" />：<input class="form-control width-120" style="display:inline-block" placeholder="请输入键值" name="param_val[]" /></div>';
        $('#param_div').append(html);
    });
 </script>