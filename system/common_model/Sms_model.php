<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 * 短信相关
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

class CI_Sms_model {
    /**
     * 发送短信【飞友短信接口】
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function send_by_feeyo_api($mobile, $tpl_cla, $tpl_param)
    {
        //注意：发送之前需要联系短信接口负责人把号码加入白名单，否则会有数量限制或无法发送
        $template = array(
            // 告警短信模板
            'notice_for_monitor' => 'A-CDM数据监控：在#TIME#监测到#CONTENT#',
            'notice_for_data_input' => 'A-CDM数据监控系统数据接收发生故障，在#TIME#监测到#CONTENT#',
        );
        
        if (isset($template[$tpl_cla]))
        {
            $content = urlencode(mb_convert_encoding(strtr($template[$tpl_cla], $tpl_param), 'gb2312', 'utf-8'));
            
            $url = "http://biz.feeyo.com/106/MmsSendEx2.asp?tel=" . $mobile . "&content=" . $content;
            
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
            $send_result = curl_exec($ch); 
            curl_close($ch);

            $send_result = mb_convert_encoding($send_result, 'utf-8', 'gb2312');
            
            if ($send_result == '发送成功')
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }
}