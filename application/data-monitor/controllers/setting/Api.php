<?php
/**
 * 接口管理
 *
 * @package path
 * @access  public
 * @author  yuanyu <yuanyu@feeyo.com>
 * @link    https://
 */

class Api extends ACDM_Controller{


    private $config_model;

    function __construct()
    {
        parent::__construct();
        $this->load->model('Basic_table_model');
        $this->load->model('Jk_api_model');
        $this->config_model = & load_class('Config_model', 'common_model');
    }

    private function _get_member_airport()
    {
        $default[] = [
            'airport_iata' => 'ACDM',
            'cn_name' => '成员机场通用',
            'cn_name_short' => '通用'
        ];
        $db_data = $this->Basic_table_model->bas_get_record_list('member_airport');
        $return = array_column(array_merge($default, $db_data), NULL, 'airport_iata');
        return $return;
    }

    public function json_data()
    {
        $member_airport_list = $this->_get_member_airport();
        $api_list = $this->Jk_api_model->get_list_with_staff();
        $data = ['data' => []];
        foreach($api_list as $item)
        {
            $data['data'][] = [
                mb_subtext($item['name'], 12),
                $item['airport_iata'] . ' ' . $member_airport_list[$item['airport_iata']]['cn_name_short'],
                $item['protocol'],
                mb_subtext($item['url'], 65),
                $item['is_enable'] == 1 ? '启用' : '停用',
                $item['staff_name'],
                $item['jk_api_id'],
                $item['name'],
                $item['url']
            ];
        }
        echo json_encode($data, TRUE);
    }

    public function index()
    {
        $this->load->view('setting/api/index', $this->bas_var);
    }

    public function create()
    {
        $this->bas_var['member_airport_list'] = $this->_get_member_airport();
        $this->bas_var['feeyo_staff_list'] = $this->Basic_table_model->bas_get_record_list('feeyo_staff', array('is_delete' => 0), '*', 'list', '', '', 'department,convert(name using gbk)');
        $this->bas_var['api_type'] = $this->config_model->api_type();
        $this->bas_var['api_verify_type'] = $this->config_model->api_verify_type();
        $this->load->view('setting/api/create', $this->bas_var);
    }

    public function create_exec()
    {
        $data['airport_iata'] = $this->I('airport_iata');
        $data['name'] = $this->I('name');
        $data['protocol'] = $this->I('protocol');
        $data['url'] = $this->I('url');
        $data['type'] = $this->I('type');
        $data['url_verify'] = $this->I('url_verify');
        $data['url_param'] = [
            'SPECIAL_PARAM_REQUEST_TIME' => $this->I('param_request_time')
        ];
        //特殊变量 比如请求时间戳
        $param_key = $this->I('param_key', []);
        $param_val = $this->I('param_val', []);
        foreach ($param_key as $key => $val)
        {
            if(!empty($val) && !empty($param_val[$key]))
            {
                $data['url_param'][$val] = $param_val[$key];
            }
        }
        $data['url_param'] = json_encode($data['url_param'], TRUE);
        $data['check_type'] = $this->I('check_type');
        $data['status_key'] = $this->I('status_key');
        $data['status_normal_value'] = $this->I('status_normal_value');
        $data['author_staff'] = $this->I('author_staff');
        $relevant_staff = $this->I('relevant_staff');
        $data['relevant_staff'] = !empty($relevant_staff) ? implode(',', $relevant_staff) : '';
        $data['is_enable'] = $this->I('is_enable');
        $description = $this->I('description');
        $data['description'] = !empty($description) ? htmlspecialchars(addslashes($description)) : '';
        //暂时不使用短信 先置空
        $data['notice_sms_cla'] = $data['notice_sms_param_index'] = '';
        //验证接口是否正常
        $api_notice = $this->Jk_api_model->api_status_handler($data);
        if($api_notice['is_normal'] === FALSE)
        {
            //如果有异常则不予入库
            echo json_encode(array("success" => 0, "text" => "操作失败，接口异常，请检查各项设置。<br />[异常：{$api_notice['error_msg']}]"));
            exit;
        }
        $result = $this->Basic_table_model->bas_add_record('jk_api', $data);
        if($result)
        {
            echo json_encode(array("success" => 1, "text" => "操作成功"));
        }
        else
        {
            echo json_encode(array("success" => 0, "text" => "操作失败"));
        }
    }

    public function edit()
    {
        $id = $this->I('id');
        $this->bas_var['member_airport_list'] = $this->_get_member_airport();
        $this->bas_var['feeyo_staff_list'] = $this->Basic_table_model->bas_get_record_list('feeyo_staff', array('is_delete' => 0), '*', 'list', '', '', 'department,convert(name using gbk)');
        $this->bas_var['api_type'] = $this->config_model->api_type();
        $this->bas_var['api_verify_type'] = $this->config_model->api_verify_type();
        $this->bas_var['info'] = $this->Basic_table_model->bas_get_record_by_id('jk_api', $id);
        $this->bas_var['info']['url_param'] = json_decode($this->bas_var['info']['url_param'], TRUE);
        $this->bas_var['info']['relevant_staff'] = explode(',', $this->bas_var['info']['relevant_staff']);
        $this->load->view('setting/api/edit', $this->bas_var);
    }

    public function edit_exec()
    {
        $id = $this->I('id');
        $data['airport_iata'] = $this->I('airport_iata');
        $data['name'] = $this->I('name');
        $data['protocol'] = $this->I('protocol');
        $data['url'] = $this->I('url');
        $data['type'] = $this->I('type');
        $data['url_verify'] = $this->I('url_verify');
        $data['url_param'] = [
            'SPECIAL_PARAM_REQUEST_TIME' => $this->I('param_request_time')
        ];
        //特殊变量 比如请求时间戳
        $param_key = $this->I('param_key', []);
        $param_val = $this->I('param_val', []);
        foreach ($param_key as $key => $val)
        {
            if(!empty($val) && !empty($param_val[$key]))
            {
                $data['url_param'][$val] = $param_val[$key];
            }
        }
        $data['url_param'] = json_encode($data['url_param'], TRUE);
        $data['check_type'] = $this->I('check_type');
        $data['status_key'] = $this->I('status_key');
        $data['status_normal_value'] = $this->I('status_normal_value');
        $data['author_staff'] = $this->I('author_staff');
        $relevant_staff = $this->I('relevant_staff');
        $data['relevant_staff'] = !empty($relevant_staff) ? implode(',', $relevant_staff) : '';
        $data['is_enable'] = $this->I('is_enable');
        $description = $this->I('description');
        $data['description'] = !empty($description) ? htmlspecialchars(addslashes($description)) : '';
        //暂时不使用短信 先置空
        $data['notice_sms_cla'] = $data['notice_sms_param_index'] = '';
        //验证接口是否正常
        $api_notice = $this->Jk_api_model->api_status_handler($data);
        if($api_notice['is_normal'] === FALSE)
        {
            //如果有异常则不予入库
            echo json_encode(array("success" => 0, "text" => "操作失败，接口异常，请检查各项设置。<br />[异常：{$api_notice['error_msg']}]"));
            exit;
        }
        $result = $this->Basic_table_model->bas_update_record('jk_api', $id, $data);
        if($result)
        {
            echo json_encode(array("success" => 1, "text" => "操作成功"));
        }
        else
        {
            echo json_encode(array("success" => 0, "text" => "操作失败"));
        }
    }

    public function delete_exec()
    {
        $id = $this->I('id');
        $data['is_delete'] = 1;
        $result = $this->Basic_table_model->bas_update_record('jk_api', $id, $data);
        if($result)
        {
            echo json_encode(array("success" => 1, "text" => "删除成功"));
        }
        else
        {
            echo json_encode(array("success" => 0, "text" => "删除失败"));
        }
    }
}