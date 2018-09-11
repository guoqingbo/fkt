<?php require APPPATH . 'views/header.php'; ?>
<style>
.tr_hide{display: none;}
.itemOn{color: blue;}
.dd {
    float: left;
    line-height: 25px;
    padding-left: 10px;
    position: relative;
    width: 200px;
}
.h_div {
    background: none repeat scroll 0 0 #fcfcfc;
    border: 1px solid #ccc;
    display: none;
    height: 25px;
    left: 0;
    padding: 0 0 0 6px;
    position: absolute;
    top: -30px;
    width: 180px;
}
.h_div .iconfont {
    cursor: pointer;
    float: right;
    font-size: 10px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    width: 20px;
}
</style>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <?php if ('' == $setResult) {?>
        <form name="search_form" method="post" action="" >
            <input type='hidden' name='submit_flag' value='set'/>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                            <?php
                                $padding = 20;
                                foreach ($module as $key => $val) { ?>
                                <tr class="tr_module_<?php echo $val['id'];?>">
                                    <td>
                                        <label>
                                            <input type="checkbox" name="module[]" value="<?php echo $val['id'];?>" id="<?php echo $val['id'];?>" ><font size="4">&nbsp&nbsp<?php echo  $val['name'];?></font>
                                        </label>
                                   </td>
                                </tr>
                                <?php foreach($val['func'] as $tab_key => $tab) {
                                       $i = 0;
                                       if ($tab['name'] != '') {
                                        $i = 1;
                                    ?>
                                <tr class="tr_tab_<?php echo $tab_key;?>">
                                    <td style="padding-left:<?php echo $i * $padding;?>px;">
                                        <label>
                                            <font size="3">&nbsp&nbsp<?php echo  $tab['name'];?></font>
                                        </label>
                                   </td>
                                </tr>
                                <?php }
                                     ?>
                             <?php
                                    foreach($tab['list'] as $secondtab_key => $secondtab) {
                                       if ($secondtab['name'] != '') {
                                           $i = 2;
                                    ?>
                                <tr class="tr_secondtab_<?php echo $secondtab_key;?>">
                                    <td style="padding-left:<?php echo $i * $padding;?>px;">
                                        <label>
                                            <font size="2">&nbsp&nbsp<?php echo  $secondtab['name'];?></font>
                                        </label>
                                   </td>
                                </tr>
                                <?php }
                                     ?>
                                <tr class="tr_func_<?php echo $secondtab_key;?>">
                                    <td  style="padding-left:<?php $i++; echo $i * $padding;?>px;">
                                      <div class="js_lable_chegked_box">
                                          <?php foreach ($secondtab['list'] as $k1 => $v1) { ?>
                                          <div class="dd">
                                              <label>
                                                  <input id="checkbox<?php echo $val['id'];?>" class="js_role_checkbox" type="checkbox" name="func_auth[]" value="<?php echo $val['id'];?>/<?php echo $v1['pid']?>" <?php if(in_array($v1['pid'],$role_func1)){echo "checked='checked'";}?> >&nbsp<?php echo  $v1['pname']?>
                                              </label>

                                          </div>
                                          <?php } ?>
                                      </div>
                                    </td>
                                </tr>
                            <?php }}}?>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            操作范围:
                                        </label>
                                            &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="range" value="1" <?php echo ($range==1)?checked:"" ?>>公司</label>
                                            &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="range" value="2" <?php echo ($range==2)?checked:"" ?>>门店</label>
                                    </div>
                                </div>
                            </tbody>
                        </table>
                        <div class="col-sm-6" style="width:100%">
                            <div class="dataTables_length" id="dataTables-example_length">
                                <input type="hidden" name="system_group_id" value="<?php echo $id;?>">
                                <input class="btn btn-primary" type="submit" value="提交">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php } else if (0 === $setResult) { ?>
            <div>设置失败</div>
        <?php } else { ?>
            <div>设置成功</div>
        <?php } ?>
    </div>
</div>
<?php if ($setResult != "") { ?>
    <script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/permission/index/'; ?>";
            }, 1000);
        });
    </script>
<?php } ?>
<script type="text/javascript">
$(function() {
    $("#tr_module th").each(function() {
        $(this).click(function(){
            var _id = $(this).attr("rel");
            $(this).addClass("itemOn").siblings().removeClass("itemOn");
            $("tr.tr_module_"+_id).siblings().hide();
            $("tr.tr_module_"+_id).show();
        });
    });
    $(".js_role_checkbox").on("click",function(event){
        $(".js_h_div").hide();
        var p=$(this).parent().siblings(".js_h_div");
        var v=$(this).val();
        if(this.checked){
            if(v>0){
                p.find(":radio[value="+v+"]").attr("checked",true);
            }else{
                p.find(":radio").eq(0).attr("checked",true);
            }
            p.show();
        }else{
            p.find(":radio").attr("checked",false);
            p.hide();
        }
        event.stopPropagation();
    });

    $(".js_c").on("click",function(){
        $(this).parent(".js_h_div").hide();
    });

    $(document).on('click',function(){
        $(".js_h_div").hide();
    });

    $('.js_h_div').on("click",function(event){
        event.stopPropagation();
    });


   $("input[name='module[]']").click(function(){
       var id = $(this).val();
       if($("input[id='"+id+"']").attr("checked")){
            $("input[id='checkbox"+id+"']").each(function(){
                $(this).attr("checked",true);
            });
    }else{
            $("input[id='checkbox"+id+"']").each(function(){
               $(this).attr("checked",false);
                });
            }
    });
    $("input[name='func_auth[]']").click(function(){
        var id_array = $(this).val().split('/');
        for(var i in id_array){
            var id1 = id_array[0];
            var id2 = id_array[1];
        }
        $("input[id='"+id1+"']").attr("checked",false);
    });

  })

</script>
<?php require APPPATH . 'views/footer.php'; ?>
