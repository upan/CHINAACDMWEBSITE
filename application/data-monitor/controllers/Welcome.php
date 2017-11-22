<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 默认入口
 * Class Welcome
 */
class Welcome extends CI_Controller
{
    public function index()
    {
        redirect('console/dashboard/index');
    }

}