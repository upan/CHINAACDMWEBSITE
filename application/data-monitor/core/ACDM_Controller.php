<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class ACDM_Controller extends CI_Controller{
    /**
     * 基础控制器视图变量数组
     *
     * @var array
     */
    protected $bas_var = array();

    // 性能分析
    protected $enable_profiler = FALSE;

    /**
     * 用户SESSION数据
     *
     * @var array
     */
    protected $user_session = array();

    public function __construct($AUTH = TRUE){

        parent::__construct();
        $this->user_session = $this->session->userdata('user_auth');
        // 复制默认导航模块
        $this->bas_var['url_seg1'] = $this->uri->segment(1);
        $this->bas_var['url_seg2'] = $this->uri->segment(2);
        $this->bas_var['url_seg3'] = $this->uri->segment(3);
        $this->bas_var['system_name'] = $this->config->item('system_name');
        // 最新静态文件版本
        $this->bas_var['web_static_file_ver'] = array();
        // 是否开启CI框架程序分析器
        $this->enable_profiler && $this->output->enable_profiler(TRUE);
        // 默认页面设置
        $this->bas_var['system_index_url'] = base_url('console/dashboard/index');
        $this->bas_var['system_login_url'] = base_url('account/operate/login');
        if($AUTH === TRUE)
        {
            //验证用户
            $account_auth = $this->account_auth();
            if($account_auth === FALSE)
            {
                redirect($this->bas_var['system_login_url']);
            }
        }
    }

    //判断是否登录
    protected function account_auth()
    {
        //判断是否有session数据
        $user_auth_sign = $this->session->userdata('user_auth_sign');
        $auth = FALSE;
        if( !empty($this->user_session) && !empty($user_auth_sign))
        {
            if($user_auth_sign === data_build_string($this->user_session))
            {
                $auth = TRUE;
            }
        }
        return $auth;
    }

    //自定义获取请求参数
    protected function I($var = '', $default = NULL){
        $get = $this->input->get(NULL, TRUE);
        $post = $this->input->post(NULL, TRUE);
        if(empty($var)){
            if(!empty($get)){
                return $get;
            }elseif(!empty($post)){
                return $post;
            }else{
                return $default;
            }
        }else{
            if(array_key_exists($var, $get)){
                return $get[$var];
            }elseif(array_key_exists($var, $post)){
                return $post[$var];
            }else{
                return $default;
            }
        }
    }
    
    //默认中文转义
    public function json_exit($response = array("status"=>FALSE,"msg"=>"操作失败！"),$is_unescaped = FALSE,$gzip = FALSE){
        if (empty($response["msg"]) && isset($response["status"])) {
            $response["msg"] = $response["status"] ? "操作成功！" : "操作失败！";
        }
        header('Cache-Control: no-cache, must-revalidate');
        header("Content-Type: text/plain; charset=utf-8");
        $gzip && ob_start('ob_gzip');
        if ($is_unescaped) {
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode($response);
        }
        $gzip && ob_end_flush();//输出压缩成果
        exit;
    }



    /**
     * EXCEL基本导出方法
     * @param $title
     * @param $data
     * @param string $filename
     * @param string $merge
     * @param int $width
     * @param int $height
     */
    protected function excel_export_basic($title, $data, $filename = "", $merge = "", $width = 12, $height= 22)
    {
        $this->load->library("PHPExcel");
        $this->load->library('PHPExcel/IOFactory');
        $php_excel = new PHPExcel();
        $php_excel->getDefaultStyle()->getFont()->setName( 'Arial');
        $cols_array = array(
            "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
            "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
            "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
            "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ",
            "DA","DB","DC","DD","DE","DF","DG","DH","DI","DJ","DK","DL","DM","DN","DO","DP","DQ","DR","DS","DT","DU","DV","DW","DX","DY","DZ",
            "EA","EB","EC","ED","EE","EF","EG","EH","EI","EJ","EK","EL","EM","EN","EO","EP","EQ","ER","ES","ET","EU","EV","EW","EX","EY","EZ",
            "FA","FB","FC","FD","FE","FF","FG","FH","FI","FJ","FK","FL","FM","FN","FO","FP","FQ","FR","FS","FT","FU","FV","FW","FX","FY","FZ",
            "GA","GB","GC","GD","GE","GF","GG","GH","GI","GJ","GK","GL","GM","GN","GO","GP","GQ","GR","GS","GT","GU","GV","GW","GX","GY","GZ",
            "HA","HB","HC","HD","HE","HF","HG","HH","HI","HJ","HK","HL","HM","HN","HO","HP","HQ","HR","HS","HT","HU","HV","HW","HX","HY","HZ",
            "IA","IB","IC","ID","IE","IF","IG","IH","II","IJ","IK","IL","IM","IN","IO","IP","IQ","IR","IS","IT","IU","IV","IW","IX","IY","IZ",
            "JA","JB","JC","JD","JE","JF","JG","JH","JI","JJ","JK","JL","JM","JN","JO","JP","JQ","JR","JS","JT","JU","JV","JW","JX","JY","JZ",
            "KA","KB","KC","KD","KE","KF","KG","KH","KI","KJ","KK","KL","KM","KN","KO","KP","KQ","KR","KS","KT","KU","KV","KW","KX","KY","KZ",
            "LA","LB","LC","LD","LE","LF","LG","LH","LI","LJ","LK","LL","LM","LN","LO","LP","LQ","LR","LS","LT","LU","LV","LW","LX","LY","LZ",
            "MA","MB","MC","MD","ME","MF","MG","MH","MI","MJ","MK","ML","MM","MN","MO","MP","MQ","MR","MS","MT","MU","MV","MW","MX","MY","MZ",
            "NA","NB","NC","ND","NE","NF","NG","NH","NI","NJ","NK","NL","NM","NN","NO","NP","NQ","NR","NS","NT","NU","NV","NW","NX","NY","NZ",
            "OA","OB","OC","OD","OE","OF","OG","OH","OI","OJ","OK","OL","OM","ON","OO","OP","OQ","OR","OS","OT","OU","OV","OW","OX","OY","OZ",
            "PA","PB","PC","PD","PE","PF","PG","PH","PI","PJ","PK","PL","PM","PN","PO","PP","PQ","PR","PS","PT","PU","PV","PW","PX","PY","PZ",
            "QA","QB","QC","QD","QE","QF","QG","QH","QI","QJ","QK","QL","QM","QN","QO","QP","QQ","QR","QS","QT","QU","QV","QW","QX","QY","QZ",
            "RA","RB","RC","RD","RE","RF","RG","RH","RI","RJ","RK","RL","RM","RN","RO","RP","RQ","RR","RS","RT","RU","RV","RW","RX","RY","RZ",
            "SA","SB","SC","SD","SE","SF","SG","SH","SI","SJ","SK","SL","SM","SN","SO","SP","SQ","SR","SS","ST","SU","SV","SW","SX","SY","SZ",
            "TA","TB","TC","TD","TE","TF","TG","TH","TI","TJ","TK","TL","TM","TN","TO","TP","TQ","TR","TS","TT","TU","TV","TW","TX","TY","TZ",
            "UA","UB","UC","UD","UE","UF","UG","UH","UI","UJ","UK","UL","UM","UN","UO","UP","UQ","UR","US","UT","UU","UV","UW","UX","UY","UZ",
            "VA","VB","VC","VD","VE","VF","VG","VH","VI","VJ","VK","VL","VM","VN","VO","VP","VQ","VR","VS","VT","VU","VV","VW","VX","VY","VZ",
            "WA","WB","WC","WD","WE","WF","WG","WH","WI","WJ","WK","WL","WM","WN","WO","WP","WQ","WR","WS","WT","WU","WV","WW","WX","WY","WZ",
            "XA","XB","XC","XD","XE","XF","XG","XH","XI","XJ","XK","XL","XM","XN","XO","XP","XQ","XR","XS","XT","XU","XV","XW","XX","XY","XZ",
            "YA","YB","YC","YD","YE","YF","YG","YH","YI","YJ","YK","YL","YM","YN","YO","YP","YQ","YR","YS","YT","YU","YV","YW","YX","YY","YZ",
            "ZA","ZB","ZC","ZD","ZE","ZF","ZG","ZH","ZI","ZJ","ZK","ZL","ZM","ZN","ZO","ZP","ZQ","ZR","ZS","ZT","ZU","ZV","ZW","ZX","ZY","ZZ"
        );
        $param = array();
        foreach($title as $key => $value){
            //表的第一行标题填充
            $param['style']['setvalue'][$cols_array[$key] . "1"] = $value;
            $php_excel->getActiveSheet()->getColumnDimension($cols_array[$key])->setWidth($width);
        }


        $title_count = count($title);
        $last_siffux = $title_count - 1;
        $data_count = count($data);
        $all_count = $data_count + 1;
        //$php_excel->getActiveSheet()->getStyle("A1:{$cols_array[$last_siffux]}{$all_count}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $param['style']["A1:{$cols_array[$last_siffux]}1"]= array(
            'font'=> array (
                'bold'      => TRUE
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $param['style']["A1:{$cols_array[$last_siffux]}{$all_count}"]= array(
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
                    'color' => array('argb' => 'FF555555'),
                )
            )
        );
        $php_excel->getActiveSheet()->getRowDimension(1)->setRowHeight(28);
        $i = 2;
        foreach($data as $key => $val)
        {
            $php_excel->getActiveSheet()->getRowDimension($i)->setRowHeight($height);
            $j = 0;
            foreach($val as $sub_val)
            {
                $param['style']['setvalue'][$cols_array[$j] . $i] = $sub_val;
                $j ++;
            }
            $i ++;
        }


        if (!empty($merge))
        {
            $param['style']["merge"] = array($merge);
        }

        foreach($param['style'] as $key => $style)
        {
            // 多个连续合并
            if ($key == 'merge' && is_array($style))
            {
                foreach($style as $merge)
                {
                    foreach($merge as $val)
                    {
                        if (preg_match('/(([A-Z]+)(\d+)(:)([A-Z]+)(\d+)$)/', $val))
                        {
                            $php_excel->getActiveSheet()->mergeCells($val);
                            continue;
                        }
                    }
                }
            }
            else
            {
                // 单个合并
                if($key == 'merge' && preg_match('/(([A-Z]+)(\d+)(:)([A-Z]+)(\d+)$)/', $style)) {
                    $php_excel->getActiveSheet()->mergeCells($style);
                    continue;
                }
            }

            if (preg_match('/([A-Z]+)(\d*)(:([A-Z]+)(\d*))?$/', $key))
            {
                $php_excel->getActiveSheet()->getStyle($key)->applyFromArray($style);
                continue;
            }

            if($key=="setvalue")
            {
                foreach($style as $k =>$val)
                {
                    $php_excel->getActiveSheet()->setCellValue($k,$val);
                }
                continue;
            }

            if($key=="setheight")
            {
                foreach($style as $k=>$val)
                {
                    if(intval($k)>0 && intval($val)>0)
                    {
                        $php_excel->getActiveSheet()->getRowDimension($k)->setRowHeight($val);
                    }
                }

            }

            if($key=="setwidth")
            {
                foreach($style as $k => $val)
                {
                    if($val === TRUE)
                    {
                        $php_excel->getActiveSheet()->getColumnDimension($k)->setAutoSize(TRUE);
                    }
                    else if(intval($val)>0)
                    {
                        $php_excel->getActiveSheet()->getColumnDimension($k)->setWidth($val);
                    }
                }
            }

            if($key=="header")
            {
                for($i=0;$i<count($style['cols']);$i++)
                {
                    //设置单元格值
                    $php_excel->getActiveSheet()->setCellValue($style['cols'][$i]."1",$style['value'][$i]);
                    //设置单元格样式
                    $php_excel->getActiveSheet()->getStyle($style['cols'][$i]."1")->applyFromArray($style['style']);
                }
                foreach($style['merge'] as $value)
                {
                    //合并单元格
                    $php_excel->getActiveSheet()->mergeCells($value);
                }
            }
        }

        //  $objWriter = IOFactory::createWriter($php_excel, 'Excel2007');
        /// $objWriter->save('php://output');
        $objWriter = IOFactory::createWriter($php_excel, 'Excel5');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
}
