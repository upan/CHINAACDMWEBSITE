<form id="exec_form">
    <input type="hidden" name="id" value="<?php echo $info['feeyo_staff_id'];?>" />
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-body">
                <div class="form-group">
                    <label>姓名</label><span class="small-desc-red">*</span>
                    <input class="form-control width-120" placeholder="员工姓名" name="name" id="name" value="<?php echo $info['name'];?>" />
                </div>

                <div class="form-group">
                    <label>手机</label><span class="small-desc-red">*</span>
                    <input class="form-control width-200" placeholder="员工手机号码" name="mobile" id="mobile" value="<?php echo $info['mobile'];?>" />
                </div>

                <div class="form-group">
                    <label>邮箱</label><span class="small-desc-red">*</span>
                    <input class="form-control width-400" placeholder="员工邮箱地址" name="email" id="email" value="<?php echo $info['email'];?>" />
                </div>

                <div class="form-group">
                    <label>部门</label><span class="small-desc-red">*</span>
                    <select class="form-control width-300" name="department" id="department">
                        <option value="">请选择</option>
                        <?php
                        foreach ($department_list as $key => $item) {
                            ?>
                            <option value="<?php echo $key;?>"<?php echo $info['department'] == $key ? ' selected="selected"' : '';?>>(<?php echo $key;?>)<?php echo $item;?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>岗位</label>
                    <select class="form-control width-300" name="post_id" id="post_id">
                        <?php
                        foreach ($post_list as $key => $item) {
                            ?>
                            <option value="<?php echo $item['id'];?>"<?php echo $info['post_id'] == $item['id'] ? ' selected="selected"' : '';?>><?php echo $item['name'];?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <div><label>是否在职</label><span class="small-desc-999">离职后将无法登录系统</span></div>
                    <label class="radio-inline">
                        <input type="radio" name="is_valid" value="1"<?php echo $info['is_valid'] == '1' ? ' checked="checked"' : '';?> />在职
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="is_valid" value="0"<?php echo $info['is_valid'] == '0' ? ' checked="checked"' : '';?>>离职
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>