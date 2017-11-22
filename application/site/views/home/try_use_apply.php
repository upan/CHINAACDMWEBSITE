<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="/assets/web/css/contact.css" />
<div class="am-contact">
    <h4 class="tit">申请获得A-CDM系统</h4>
    <ul>
        <li>
            <span class="require-icon">*</span>
            <input class="am-input" type="text" placeholder="姓名" name="name" />
        </li>
        <li>
            <input class="am-input" type="text" placeholder="公司名称" name="company" />
        </li>
        <li>
            <input class="am-input" type="text" placeholder="电子邮件" name="email" />
        </li>
        <li>
            <input class="am-input" type="text" placeholder="职位" name="job" />
        </li>
        <li>
            <span class="require-icon">*</span>
            <input class="am-input" type="text" placeholder="电话号码" name="phone" />
        </li>
        <li>
            <textarea class="am-textarea" placeholder="留言内容" rows="4" name="content" id="content"></textarea>
        </li>
        <li class="btm">
            <input class="am-btn" type="button" value="免费申请" onclick="submit_apply();" />
        </li>
    </ul>
</div>
<script type="text/javascript" src="/assets/plugins/layer/layer.js"></script>
<script type="text/javascript">
//复选框
$('.am-checkbox input').on('click',function(){
    var _this = $(this),
        label = _this.parents('label');
    if(label.hasClass('active')){
        label.removeClass('active').find('input').prop('checked',false);
    }else{
        label.addClass('active').find('input').prop('checked',true);
    }
});
function submit_apply(){
    var _name = $("input[name=name]").val().trim(),
        _company = $("input[name=company]").val().trim(),
        _email = $("input[name=email]").val().trim(),
        _phone = $("input[name=phone]").val().trim(),
        _job = $("input[name=job]").val().trim(),
        _content = $("#content").val().trim();
    if (empty(_name) || empty(_phone)) {
        layer.msg("请填写必填项！", {icon:5, time:3000,shade:0.5,shadeClose:true});
        return false;
    }
    if (!is_email(_email)) {
        layer.msg("请按正确的邮箱格式填写！", {icon:5, time:3000,shade:0.5,shadeClose:true});   
        return false;
    }
    if (!is_phone(_phone)) {
        layer.msg("请按正确的手机号码格式填写！", {icon:5, time:3000,shade:0.5,shadeClose:true});   
        return false;
    }
    var params = { name:_name,phone:_phone,email:_email,job:_job,company:_company,content:_content};
    //loading 
    var loading = layer.msg("正在请求中",{time:0,shadeClose:false,shade:0.3});
    $.post("<?php echo base_url("home/try_use_apply");?>", params, function(res) {
        var data = $.parseJSON(res);
        layer.close(loading);
        if (data.status) {
            //申请成功弹层
            layer.open({
                type: 1,
                title:false,
                skin:'am-msg-layer',
                closeBtn:0,
                area:['528px','250px'],
                btn:['确定'],
                content:'<div class="am-info-text">您的申请已提交，我们将会在3-5个工作日联系您，请保持联系方式畅通</div>'
            });
        }else{
            layer.msg(data.msg, {icon:5, time:3000,shade:0.5,shadeClose:true});
        }
    });
}
var is_phone = function (num) {
    var reg = /^1\d{10}$/;
    return reg.test(num)
};
var is_email = function (num) {
    var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    return reg.test(num)
};
function empty(mixed_var) {
    var undef, key, i, len;
    var emptyValues = [undef, null, false, 0, "", "0"];
    for (i = 0, len = emptyValues.length; i < len; i++) {
        if (mixed_var === emptyValues[i]) {
            return true
        }
    }
    if (typeof mixed_var === "object") {
        for (key in mixed_var) {
            return false
        }
        return true
    }
    return false
};
</script>
</body>
</html>
