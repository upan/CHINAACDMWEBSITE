<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2016-10-27T13:54:06+0800
 */
-->
  <div class="content-wrapper">
    <section class="content">
      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">文章列表</h3>
        </div>
        <div class="box-body">
            <div class="pull-right">
                <form class="form-horizontal form-inline" method="GET" action="<?php echo base_url('article/index');?>">
                    <select class="form-control input-sm" name="filter[catid]">
                        <option value="0" selected>全部分类</option>
                        <?php foreach ($this->categorys as $value):?>
                            <option value="<?php echo $value['id'];?>" <?php if($filter['catid']==$value['id']):?>selected<?php endif;?>>
                                <?php echo $value['name'];?>
                            </option>
                        <?php endforeach;?>
                    </select>
                    <span>&nbsp;</span>
                    <?php $status = array(
                        "0"=>"所有状态",
                        "1"=>"正常",
                        // "-2"=>"关闭",
                        "-1"=>"草稿",
                    );?>
                    <select class="form-control input-sm" name="filter[status]">
                        <?php foreach($status as $key => $value):?>
                            <option value="<?php echo $key;?>" <?php if($filter['status']==$key):?>selected<?php endif;?>><?php echo $value;?></option>
                        <?php endforeach;?>
                    </select>
                    <span>&nbsp;</span>
                    <label>标题:</label>
                    <input type="text" name="filter[title]" value="<?php echo $filter['title'];?>" class="form-control input-sm">
                    <span>&nbsp;</span>
                    <button type="submit" class="btn btn-default">搜索</button>
                    <a href="<?php echo base_url('article/index');?>" type="button" class="btn btn-primary">重置</a>
                </form>
            </div>
            <table class="table table-hover no-footer">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>标题</th>
                        <th>阅读</th>
                        <th>点赞</th>
                        <th>作者</th>
                        <th>栏目</th>
                        <th>状态</th>
                        <th>排序</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $key => $value):?>
                        <tr>
                            <td><?php echo $value["id"];?></td>
                            <td><?php echo $value["title"];?></td>
                            <td><?php echo $value["read"];?></td>
                            <td><?php echo $value["like"];?></td>
                            <td><?php echo $value["author"];?></td>
                            <td><?php echo isset($this->categorys[$value["catid"]]) ? $this->categorys[$value["catid"]]["name"] : "<span class='text-danger'>栏目 {$value['catid']} 不存在</span>";?></td>
                            <?php $icon = $value['status'] ? "ok" : "remove";
                                switch ((int)$value['status']) {
                                    case -1:
                                        $font = "remove";
                                        $icon = "eye-close";
                                        break;
                                    case -2:
                                        $font = $icon = "remove";
                                        break;
                                    case 1:
                                        $font = $icon = "ok";
                                        break;
                                }
                                $span = "<span data-tag=\"{$icon}\" class=\"glyphicon glyphicon-{$icon} font-{$font}\"></span>";
                            ?>
                            <td><?php echo $span;?></td>
                            <td><?php echo $value["sort"];?></td>
                            <td><?php echo empty($value["create_time"]) ? "" : date("Y-m-d H:i",$value["create_time"]);?></td>
                            <td><?php echo empty($value["update_time"]) ? "" : date("Y-m-d H:i",$value["update_time"]);?></td>
                            <td>
                                <?php echo gen_a_button('article/edit','btn-default btn-sm','编辑',$value['id']);?>
                                <?php $url = base_url("article/delete/{$value['id']}");
                                    $call = "G.confirm('确定删除文章 {$value['title']}?',function(){G.get('{$url}')});";
                                    echo gen_button('article/delete','btn-primary btn-sm','删除',$call);?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <span class="pull-right">
                <?php echo $pagesInfo;?>
            </span>
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include VIEWPATH . 'footer_anchor.php'; ?>