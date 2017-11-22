<?php
/**
 * 预计时间质量
 *
 * @package path
 * @access  public
 * @author  yuanyu <yuanyu@feeyo.com>
 * @link    https://
 */

class Esttime_quality extends ACDM_Controller
{

    function __construct()
    {
        parent::__construct(FALSE);
    }

    public function json_data()
    {
        

    }

    public function index()
    {
        $this->load->view('statistic/esttime_quality/index');
    }

}