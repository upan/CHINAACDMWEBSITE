<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <title>中国机场协同决策</title>
    <meta name="keywords" content="ACDM A-CDM 协同放行 非常准ACDM 飞常准ACDM CDM 民航局ACDM 协同决策 机场ACDM 智慧机场 机场信息化 飞友科技">
    <meta name="description" content="ACDM A-CDM 协同放行 非常准ACDM 飞常准ACDM CDM 民航局ACDM 协同决策 机场ACDM 智慧机场 机场信息化 飞友科技">
    <meta name="author" content="Feeyo 飞友科技">
    <link rel="stylesheet" type="text/css" href="/assets/web/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/assets/web/css/index.css" />
    <script src="/assets/web/js/jquery.min.js" type="text/javascript"></script>
    <script src="/assets/web/js/core.js" type="text/javascript"></script>
</head>

<body>
<div class="am-header">
    <div class="header-cont">
        <a href="/"><div class="logo"></div></a>
        <ul>
            <?php foreach ($this->categorys as $key => $value):?>
                <?php $is_active = (int)$this->now_catid === (int)$value['id'] || "/{$this->_uri_string}" === $value['url'];?>
                <li <?php if($is_active):?> class="active" <?php endif;?>><a href="<?php echo $value["url"];?>"><?php echo $value["name"];?></a></li>    
            <?php endforeach;?>
        </ul>
        <div class="search-input"><input type="text" placeholder="机场三字码跳转ACDM" name="acdm_input" id="acdm_input"><a></a></div>
        <p class="tips hide">该机场暂未开通跳转功能</p>
    </div>
</div>