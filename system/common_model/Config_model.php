<?php

/**
 * +-----------------------------------
 * 公共配置类
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

class CI_Config_model {

    protected $CI;

    private $master_db = null;

    function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
    }
    /**
     * API类别
     */
    public function api_type()
    {
        $return = [
            'interface' => '接口',
        ];
        return $return;
    }

    public function api_verify_type()
    {
        $return = [
            '1' => [
                'desc' => 'MD5(密钥+参数按键名正序后URL字符串)',
                'secret' => 'hfn60mkk06XbzPQs'
            ],
        ];
        return $return;
    }

    public function feeyo_department()
    {
        $return = [
            'A' => '行软事业部',
            'B' => '数据事业部',
            'C' => '移动事业部',
            'D' => '基础技术部',
            'E' => '总经办',
            'F' => '北京分公司',
            'M' => '传媒资讯部',
            'S' => '服务资讯部',
            'T' => '航联研发部',
        ];
        return $return;
    }

    public function feeyo_post()
    {
        $return = array(
            0 => ['id' => 0, 'name' => '无岗位', 'description' => ''],
            1 => ['id' => 1, 'name' => 'PHP工程师', 'description' => ''],
            2 => ['id' => 2, 'name' => '产品经理', 'description' => ''],
            3 => ['id' => 3, 'name' => '数据分析', 'description' => ''],
            4 => ['id' => 4, 'name' => '售后支持', 'description' => ''],
            5 => ['id' => 5, 'name' => 'JAVA工程师', 'description' => ''],
            6 => ['id' => 6, 'name' => 'IOS工程师', 'description' => ''],
            7 => ['id' => 7, 'name' => '安卓工程师', 'description' => ''],
            8 => ['id' => 8, 'name' => '测试工程师', 'description' => ''],
            9 => ['id' => 9, 'name' => 'WEB前端工程师', 'description' => ''],
            10 => ['id' => 10, 'name' => '.NET工程师', 'description' => ''],
            11 => ['id' => 11, 'name' => '技术工程师', 'description' => ''],
            12 => ['id' => 12, 'name' => '算法工程师', 'description' => ''],
            13 => ['id' => 13, 'name' => '市场经理', 'description' => ''],
            14 => ['id' => 14, 'name' => '公关经理', 'description' => ''],
            15 => ['id' => 15, 'name' => '总经理', 'description' => ''],
            16 => ['id' => 16, 'name' => '部门助理', 'description' => ''],
            99 => ['id' => 99, 'name' => '上海团队', 'description' => '']
        );
        /*
        $this->CI->db->select('value')->from('system_config as sc');
        $this->CI->db->where("sc.name='post_config'");
        $result = $this->CI->db->get()->row_array();
        if(!empty($result))
        {
            $result = json_decode($result['value'], TRUE);
            foreach($result as $item)
            {
                $return[$item['id']] = $item;
            }
        }
        */
        return $return;
    }
}