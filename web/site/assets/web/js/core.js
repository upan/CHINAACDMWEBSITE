// 分页输入页数点击跳转公共方法
function page_redirect(btn){
    var _page = parseInt($(btn).siblings("input[name='page']").val());
    if (isNaN(_page) || _page < 1) { 
        return false;
    } 
    var _action = $(btn).parent("form").attr('action');
    _action += '&page=' + _page;
    window.location.href=_action;
}