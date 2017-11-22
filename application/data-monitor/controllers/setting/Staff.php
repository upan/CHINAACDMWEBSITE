<?php
/**
 * 员工管理
 *
 * @package path
 * @access  public
 * @author  yuanyu <yuanyu@feeyo.com>
 * @link    https://
 */

class Staff extends ACDM_Controller{


    private $config_model;

    function __construct()
    {
        parent::__construct();
        $this->load->model('Basic_table_model');
        $this->config_model = & load_class('Config_model', 'common_model');
    }

    /*
    public function array_import()
    {
        $array = array(
            array("宣彤","总经理","18949872828","xtong@variflight.com"),
            array("王丽群","部门助理","13956000029","wangliqun@variflight.com"),
            array("卢厚辛","市场经理","18013366111","luhouxin@variflight.com"),
            array("孙宵宇","市场经理","18956022311","sunxiaoyu@variflight.com"),
            array("袁森","市场经理","13696785187","yuansen@variflight.com"),
            array("李昂","市场经理","18655198353","liang-a@variflight.com"),
            array("万超","产品经理","18326069502","wanchao@variflight.com"),
            array("华春辉","产品经理","18005691534","huachunhui@variflight.com"),
            array("李阳","产品经理","15155139550","liyang@variflight.com"),
            array("汪明明","产品经理","15856968053","wangmingming@variflight.com"),
            array("薛诚","产品经理","15212458723","xuecheng@variflight.com"),
            array("付越","产品经理","18956588007","fuyue@variflight.com"),
            array("周洁","产品经理","13956985021","zhoujie@variflight.com"),
            array("冯二磊","IOS工程师","15505511581","fengerlei@variflight.com"),
            array("葛春喜","IOS工程师","18256921853","gechunxi@variflight.com"),
            array("黄磊","IOS工程师","18656562702","huanglei@variflight.com"),
            array("王月云","IOS工程师","18356071358","wangyueyun@variflight.com"),
            array("王坤","IOS工程师","18156056263","wangkun@variflight.com"),
            array("肖扬","IOS工程师","15805511109","xiaoyang@variflight.com"),
            array("张敏超","IOS工程师","17727573930","zhangminchao@variflight.com"),
            array("张志伟","安卓工程师","13915988570","zhangzhiwei@variflight.com"),
            array("朱琪艳","安卓工程师","18355102505","zhuqiyan@variflight.com"),
            array("陆睿","安卓工程师","15212239132","lurui@variflight.com"),
            array("陶然","安卓工程师","15156323812","taoran@variflight.com"),
            array("魏雪松","安卓工程师","13681885398","weixuesong@variflight.com"),
            array("贾存欣","安卓工程师","15709482549","jiacunxin@variflight.com"),
            array("马松城","安卓工程师","18385655631","masongcheng@variflight.com"),
            array("朱欢","安卓工程师","15755165200","zhuhuan@variflight.com"),
            array("阚威","PHP工程师","18510329563","kanwei@variflight.com"),
            array("武文杰","PHP工程师","18255115332","wuwenjie@variflight.com"),
            array("赵斌","PHP工程师","18119690622","zhaobin@variflight.com"),
            array("曹啸航","PHP工程师","15955131508","caoxiaohang@variflight.com"),
            array("陈昌华","PHP工程师","13003066374","chenchanghua@variflight.com"),
            array("方海保","PHP工程师","18324704752","fanghaibao@variflight.com"),
            array("袁裕","PHP工程师","15395120818","yuanyu@variflight.com"),
            array("郭小强","PHP工程师","18500391240","guoxiaoqiang@variflight.com"),
            array("吴君志","PHP工程师","15001843305","wujunzhi@variflight.com"),
            array("许建","PHP工程师","15256008579","xujian@variflight.com"),
            array("马根节","PHP工程师","15856388392","magenjie@variflight.com"),
            array("徐萍","PHP工程师","18956583791","xuping@variflight.com"),
            array("储涛滔","PHP工程师","18297982515","chutaotao@variflight.com"),
            array("柏祝园","测试工程师","17756014083","baizhuyuan@variflight.com"),
            array("叶秋月","测试工程师","17755178067","yeqiuyue@variflight.com"),
            array("徐婷婷","测试工程师","18119681213","xutingting@variflight.com"),
            array("代安朋",".NET工程师","18356016505","daianpeng@variflight.com"),
            array("张重阳","技术工程师","18096409095","zhangchongyang@variflight.com"),
            array("郑立芳","售后支持","13966791141","zhenglifang@variflight.com"),
            array("周艺","售后支持","15755193682","zhouyi@variflight.com"),
            array("刘昕","售后支持","17756031169","liuxin@variflight.com"),
            array("王锦琦","售后支持","15255166035","wangjinqi@variflight.com"),
            array("陈曼莉","售后支持","13956306587","chenmanli@variflight.com"),
            array("罗帅","售后支持","18098604691","luoshuai@variflight.com"),
            array("李雯","公关经理","13955154757","liwen@variflight.com"),
            array("刘涛","公关经理","15505510920","liutao@variflight.com"),
            array("张岩","数据分析","13637080991","zhangyan@variflight.com"),
            array("单妍妍","算法工程师","13127552959","shanyanyan@variflight.com"),
            array("郭鹏飞","算法工程师","13035098660","guopengfei@variflight.com"),
            array("刘磊","JAVA开发工程师","13966727871","liulei@variflight.com"),
            array("李民","WEB前端工程师","15955156420","limin@variflight.com"),
            array("缪杨","上海团队","15117936070","miaoyang@variflight.com"),
            array("申思","上海团队","13524216948","shensi@variflight.com"),
            array("刘怀远","上海团队","18840821741","liuhuaiyuan@variflight.com"),
            array("栗辉","上海团队","15117936070","lihui@variflight.com")
        );
        $post_list = $this->config_model->feeyo_post();
        $ids = array();
        foreach($post_list as $item)
        {
            $ids[$item['name']] =  $item['id'];
        }
        $insert = [];
        foreach($array as $item)
        {
            $tmp = [
                'name' => $item[0],
                'post_id' => isset($ids[$item[1]]) ? $ids[$item[1]] : 0,
                'mobile' => $item[2],
                'email' => $item[3],
                'department' => 'A'
            ];
            $insert[] = $tmp;
        }
        $this->Basic_table_model->bas_batch_add_record('feeyo_staff', $insert);
    }
    */

    public function json_data()
    {
        $department_list = $this->config_model->feeyo_department();
        $post_list = $this->config_model->feeyo_post();
        $where = ['is_delete' => 0];
        $staff_list = $this->Basic_table_model->bas_get_record_list('feeyo_staff', $where);
        $data = ['data' => []];
        foreach($staff_list as $item)
        {
            $data['data'][] = [
                $item['feeyo_staff_id'],
                $item['name'],
                $item['mobile'],
                $item['email'],
                '(' . $item['department'] . ')' . $department_list[$item['department']],
                $post_list[$item['post_id']]['name'],
            ];
        }
        echo json_encode($data, TRUE);
    }


    public function index()
    {
        $this->load->view('setting/staff/index', $this->bas_var);
    }

    public function create()
    {
        $this->bas_var['post_list'] = $this->config_model->feeyo_post();
        $this->bas_var['department_list'] = $this->config_model->feeyo_department();
        $this->load->view('setting/staff/create', $this->bas_var);
    }

    public function create_exec()
    {
        $data['name'] = $this->I('name');
        $data['mobile'] = $this->I('mobile');
        $data['email'] = $this->I('email');
        $data['department'] = $this->I('department');
        $data['post_id'] = $this->I('post_id');
        $data['is_valid'] = $this->I('is_valid', 1);
        $result = $this->Basic_table_model->bas_add_record('feeyo_staff', $data);
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
        $this->bas_var['info'] = $this->Basic_table_model->bas_get_record_by_id('feeyo_staff', $id);
        $this->bas_var['post_list'] = $this->config_model->feeyo_post();
        $this->bas_var['department_list'] = $this->config_model->feeyo_department();
        $this->load->view('setting/staff/edit', $this->bas_var);
    }

    public function edit_exec()
    {
        $id = $this->I('id');
        $data['name'] = $this->I('name');
        $data['mobile'] = $this->I('mobile');
        $data['email'] = $this->I('email');
        $data['department'] = $this->I('department');
        $data['post_id'] = $this->I('post_id');
        $data['is_valid'] = $this->I('is_valid');
        $result = $this->Basic_table_model->bas_update_record('feeyo_staff', $id, $data);
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
        // 删除之前查询其是否为系统唯一责任人，如果是，则至少指定另外一位，否则当监控本身出现问题时无法告警
        // 指定后需要联系飞友短信平台负责人将其手机号放入白名单，否则会有条数限制
        $where = ['is_system_author' => 1];
        $staff_list = $this->Basic_table_model->bas_get_record_list('feeyo_staff', $where);
        if(count($staff_list) == 1 && $staff_list[0]['feeyo_staff_id'] == $id)
        {
            echo json_encode(array("success" => 0, "text" => "删除失败，本系统只有该员工一位作者，请联系技术指定其他至少一位作者后再次删除。"));
            exit;
        }
        //删除之前查询其是否为接口作者
        $where = ['author_staff' => $id];
        $staff_api = $this->Basic_table_model->bas_get_record_list('jk_api', $where);
        if (!empty($staff_api))
        {
            $_count = count($staff_api);
            echo json_encode(array("success" => 0, "text" => "删除失败，该员工是{$_count}个接口的作者，请先移交这些接口至其他员工。"));
            exit;
        }
        $data['is_delete'] = 1;
        $result = $this->Basic_table_model->bas_update_record('feeyo_staff', $id, $data);
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