/*------------------------------------------------------
    Author : www.webthemez.com
    License: Commons Attribution 3.0
    http://creativecommons.org/licenses/by/3.0/
---------------------------------------------------------  */

(function ($) {
    "use strict";
    var mainApp = {
        initFunction: function () {
            $('#main-menu').metisMenu();
        },
        initialization: function () {
            mainApp.initFunction();
        }
    }
    // Initializing ///
    $(document).ready(function () {
        mainApp.initFunction();
    });
}(jQuery));

function data_table_init(node, page_length, ajax_url, columnDefs) {
    var page_length = arguments[1] ? arguments[1] : 10;
    var ajax_url = arguments[2] ? arguments[2] : '';
    var columnDefs = arguments[3] ? arguments[3] : '';
    var datatable_source = $(node).dataTable({
        "ajax" : ajax_url,
        "columnDefs" : columnDefs,
        "pageLength": page_length,
        "bSort": false,
        "bLengthChange": false,
        "language": {
            "loadingRecords": "拼命加载中...",
            "lengthMenu" : "每页显示 _MENU_记录",
            "zeroRecords" : "没有相关数据",
            "info" : "第_PAGE_/_PAGES_页  共_TOTAL_条数据",
            "infoEmpty" : "",
            "search" : "搜索: ",
            "infoFiltered" : " 搜索_MAX_条记录",
            "paginate": {
                "previous": "上页",
                "next": "下页",
                "first": "首页",
                "last": "尾页"
            }
        }
    });
    return datatable_source;
}

function check_url_no_protocol(str_url){
    var strRegex = "^(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP形式的URL- 199.194.52.184
        + "|" // 允许IP和DOMAIN（域名）
        + "([0-9a-z_!~*'()-]+\.)*" // 域名- www.
        + "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." // 二级域名
        + "[a-z]{2,6})" // first level domain- .com or .museum
        + "(:[0-9]{1,4})?" // 端口- :80
        + "((/?)|" // a slash isn't required if there is no file name
        + "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$";
    var re=new RegExp(strRegex);
    //re.test()
    if (re.test(str_url)){
        return true;
    }else{
        return false;
    }
}

function check_mobile(mobile){
    //var reg =/^(((13[0-9]{1})|(14[0-9]{1})|(18[0-9]{1})|(15[0-9]{1})|177)+\d{8})$/;
    var reg=/^1\d{10}$/;
    if (reg.test(mobile)) {
        return true;
    } else {
        return false;
    }
}

function check_email(email){
    var reg=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
    if (reg.test(email)) {
        return true;
    } else {
        return false;
    }
}