<?php
/**
 * 接口警告日志
 *
 * @package path
 * @access  public
 * @author  yuanyu <yuanyu@feeyo.com>
 * @link    https://
 */

class Notice_log extends ACDM_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Basic_table_model');
    }

    public function json_data()
    {
        $member_airport_list = $this->Basic_table_model->bas_get_record_list('member_airport');
        $member_airport_list['ACDM'] = [
            'airport_iata' => 'ACDM',
            'cn_name' => '成员机场通用',
            'cn_name_short' => '通用'
        ];
        $member_airport_list = array_column($member_airport_list, NULL, 'airport_iata');
        $this->load->model('Jk_notice_log_model');
        $jk_notice_logs = $this->Jk_notice_log_model->get_list_with_staff([], '', '', 'jnl.stoppage_time', 'DESC');
        $data = ['data' => []];
        foreach($jk_notice_logs as $item)
        {
            $data['data'][] = [
                $item['name'],
                $item['airport_iata'] . ' ' . $member_airport_list[$item['airport_iata']]['cn_name_short'],
                $item['info'],
                $item['staff_name'],
                date('y-m-d H:i:s', $item['stoppage_time']),
                $item['jk_notice_log_id']
            ];
        }
        echo json_encode($data, TRUE);
    }

    public function index()
    {
        $this->load->view('logs/notice_log/index', $this->bas_var);
    }

    public function detail()
    {
        $member_airport_list = $this->Basic_table_model->bas_get_record_list('member_airport');
        $member_airport_list = array_column($member_airport_list, NULL, 'airport_iata');
        $member_airport_list['ACDM'] = [
            'airport_iata' => 'ACDM',
            'cn_name' => '成员机场通用',
            'cn_name_short' => '通用'
        ];
        $id = $this->I('id');
        $this->load->model('Jk_notice_log_model');
        $this->bas_var['info'] = $this->Jk_notice_log_model->get_info_by_id($id);
        $this->bas_var['info']['airport'] =  $this->bas_var['info']['airport_iata'] . ' ' . $member_airport_list[ $this->bas_var['info']['airport_iata']]['cn_name_short'];
        $this->load->view('logs/notice_log/detail', $this->bas_var);
    }
}