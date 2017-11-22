<!--
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-10-27T13:54:06+0800
 */
-->
var REQUEST_LOADING_ENABLED = false;//请求是否 加loading
var echo = function(value){
    console.log(value);
}
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
//自己用的js工具类
var Validator = {  
    // 邮箱  
    isEmail : function(s) {  
        var p = "^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$";  
        return this.test(s, p);  
    },  

    // 手机号码  
    isMobile : function(s) {  
        return this.test(s, /^(180|189|133|134|153|181)\d{8}$/);  
    },  

    // 电话号码  
    isPhone : function(s) {  
        return this.test(s, /^[0-9]{3,4}\-[0-9]{7,8}$/);  
    },  

    // 邮编  
    isPostCode : function(s) {  
        return this.test(s, /^[1-9][0-9]{5}$/);  
    },  

    // 数字  
    isNumber : function(s, d) {  
        return !isNaN(s.nodeType == 1 ? s.value : s)  
                && (!d || !this.test(s, '^-?[0-9]*\\.[0-9]*$'));  
    },  

    // 判断是否为空  
    isEmpty : function(s) {  
        return !jQuery.isEmptyObject(s);  
    },  

    // 正则匹配  
    test : function(s, p) {  
        s = s.nodeType == 1 ? s.value : s;  
        return new RegExp(p).test(s);  
    }  
};
var globalClass = function () {
    var _self = this;
        _self._loading = null;

    _self.gourl = function (_url,stime){
        if (stime) {
            setTimeout(function(){
                window.location.href = _url;
            },stime*1000);
        }else{
            window.location.href = _url;
        }
    },

    //_blank = true 在新标签页打开
    _self.redirect = function(_url,_blank){
        if (_blank) {
            window.open(_url);
        }else{
            window.location.href = _url;
        }
    },

    _self.refresh = function (stime) {
        if (!stime) stime = 2000;
        setTimeout(function() {
            window.location.reload();
        },stime);
    },

    _self.get = function(_get_url,_action){
        REQUEST_LOADING_ENABLED && _self.loading();
        $.get(_get_url, function(res) {
            var data = $.parseJSON(res);
            var _icon_no = data.status ? 6 : 5;
            layer.msg(data.msg, {icon: _icon_no, time:3000,shade:0.5,shadeClose:true},function(){
                if (data.loginout) {
                    _action = LOGINOUT_URL;
                }
                if ($.type(_action) == "string") {
                    window.location.href = _action;
                }else if($.type(_action) == "function"){
                    _action.call(this);
                }else{
                    window.location.reload();
                }
            });
        }).complete(function(){
            REQUEST_LOADING_ENABLED && _self.xloading();
        });
    },

    _self.post = function(_post_url,_post_data,_action){
        REQUEST_LOADING_ENABLED && _self.loading();
        $.post(_post_url,_post_data, function(res) {
            var data = $.parseJSON(res);
            var _icon_no = data.status ? 6 : 5;
            layer.msg(data.msg, {icon: _icon_no, time:3000,shade:0.5,shadeClose:true},function(){
                if (data.loginout) {
                    _action = LOGINOUT_URL;
                }
                if ($.type(_action) == "string") {
                    window.location.href = _action;
                }else if($.type(_action) == "function"){
                    _action.call(this);
                }else{
                    window.location.reload();
                }
            });
        }).complete(function(){
            REQUEST_LOADING_ENABLED && _self.xloading();
        });
    },

    _self.ajax = function(){},

    _self.success = function(msg,call){
        layer.msg(msg, {icon: 6, time:3000,shade:0.5,shadeClose:true},call);
    },

    _self.error = function (msg,call){
        layer.msg(msg, {icon: 5, time:3000,shade:0.5,shadeClose:true},call);
    },

    _self.loading = function(msg){
        _self._loading = layer.msg(msg,{time:0,shadeClose:false,shade:0.3});
    },

    _self.xloading = function (){
        layer.close(_self._loading);
    },

    _self.tip = function(msg,is_success,call){
        if ($.type(_call) != 'function') {
            return false;
        }
        layer.msg(tip, {icon: is_success ? 6 : 5, time:3000,shade:0.5,shadeClose:true},call);
    },

    _self.dataTable = function (el,config){
        var _el,_config,conf;
        el ? _el = el : _el = "table";
        _config = {
            "ordering":false,
            "language": {
                "url": "../assets/plugins/datatables/chinese.json"
            }
        }
        conf = $.extend({}, _config, config);
        return $(_el).DataTable(conf);
    },

    _self.date = function(el,_start,_min,fm,call){
        return $(el).datetimepicker({
            language: 'zh-CN',
            autoclose: 1,
            todayHighlight: 1,
            format: fm ? fm : "yyyy-mm-dd",
            todayBtn: true,
            startView: _start ? _start : 2,
            minView: _min ? _min : 2,
        }).on('changeDate', call);
    },

    _self.fileinput = function (el,config,_cf){
        var _config = {
            maxFileCount:1,
            uploadAsync: true,
            showUpload: true, //是否显示上传按钮
            showCaption: true,//是否显示标题
            showRemove: false,
            showClose: false,
            language: 'zh', //设置语言
            allowedFileExtensions: ['jpg', 'png', 'gif'],//接收的文件后缀
            //maxFileSize: _size,//文件大小 单位为kb
            //uploadUrl：//上传文件地址;
            showPreview:false,
            dropZoneEnabled: false,//是否显示拖拽区域
        }
        var config_ = config ? config : {};
        var conf = $.extend({}, _config, config);
        return el.fileinput(conf).on('fileuploaded',_cf);
    },

    _self.layer = function (_title,_html,_area,_btn,_yes_call,_cancel_call,_success_call){
        var _default_call = function (){};
        return layer.open({
            type : 1,
            title : _title ? _title : "信息",
            area : _area ? _area : ["640px","auto"],
            shadeClose : true,
            content :_html ? _html : "",
            btn : _btn ? _btn : ["确定","取消"],
            move : true,
            yes: _yes_call ? _yes_call : _default_call,
            cancel: _cancel_call ? _cancel_call : _default_call,
            success:_success_call ? _success_call : _default_call,
        });
    },

    _self.confirm = function(content,yes_call,cancel_call){
        var _default_call = function (index){layer.close(index);};
        return layer.confirm(content, {icon: 3, title:'<h4>提示:</h4>'}, yes_call ? yes_call : _default_call,cancel_call ? cancel_call : _default_call);
    },
    
    _self.changepwd = function(_change_url){
        layer.open({
            type: 2, 
            area:['560px','320px'],
            content: _change_url,
        }); 
    }
};
var G = new globalClass();