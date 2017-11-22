<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Script extends ACDM_Controller
{

    public function __construct()
    {
        parent::__construct(FALSE);
    }

    public function record_init()
    {
        $pass = $this->I('pass', '');
        if($pass !== 'ryinit112211')
        {
            exit;
        }
        $data = array(
            ['id' => 0, 'name' => 'PHP工程师', 'description' => ''],
            ['id' => 0, 'name' => '产品经理', 'description' => ''],
            ['id' => 0, 'name' => '数据分析', 'description' => ''],
            ['id' => 0, 'name' => '售后支持', 'description' => ''],
            ['id' => 0, 'name' => 'JAVA工程师', 'description' => ''],
            ['id' => 0, 'name' => 'IOS工程师', 'description' => ''],
            ['id' => 0, 'name' => '安卓工程师', 'description' => ''],
            ['id' => 0, 'name' => '测试工程师', 'description' => ''],
            ['id' => 0, 'name' => 'WEB前端工程师', 'description' => ''],
            ['id' => 0, 'name' => '.NET工程师', 'description' => ''],
            ['id' => 0, 'name' => '技术工程师', 'description' => ''],
            ['id' => 0, 'name' => '算法工程师', 'description' => ''],
            ['id' => 0, 'name' => '市场经理', 'description' => ''],
            ['id' => 0, 'name' => '公关经理', 'description' => ''],
            ['id' => 0, 'name' => '总经理', 'description' => ''],
            ['id' => 0, 'name' => '部门助理', 'description' => ''],
            ['id' => 99, 'name' => '上海团队', 'description' => '']
        );
    }


}