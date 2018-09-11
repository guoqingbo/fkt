<body>
<!--权证添加和修改弹窗-->
    <div class="achievement_money_pop qz_add_step_w610" id="js_temp_pop">
        <dl class="title_top">
            <dd id="title_top3"><?=$temp_id?'编辑':'添加';?>权证流程步骤</dd>
        </dl>
        <form action="" id="transfer_form" method="post">
        <div style="height: 400px;overflow-y: auto;overflow-X: hidden;width: 100%;">
             <div class="qz_moudle_add_step" style="width:96%;padding:0 2%;">
                  <dl class="qz_moudle_add_step_type">
                      <dd>权证流程模板：<b><?=$transfer_info['template_name'];?></b></dd>
                      <dt>步骤：<b id="step_name"><?=$temp_id?$stage_conf[$step['step_id']]['text']:$stage_conf[$total_step+1]['text'];?></b></dt>
                  </dl>
                  <dl class="qz_moudle_add_step_stage"  style="width:96%;padding:0 2%;">
                        <dd>流程阶段：</dd>

                        <?php if($stage){foreach($stage as $key=>$val){?>
                            <?php if($key%3 == 0){?>
                            <p class="step_checkboxW"><input type="checkbox" name="step" value='<?=$val['id'];?>' <?php if($step['stage_id']){if(in_array($val['id'],$step['stage_id'])){echo 'checked';}}?>><?=$val['stage_name'];?></p>
                            <?php }elseif($key%3 == 1){?>
                            <p class="step_checkboxW2"><input type="checkbox" name="step" value='<?=$val['id'];?>' <?php if($step['stage_id']){if(in_array($val['id'],$step['stage_id'])){echo 'checked';}}?>><?=$val['stage_name'];?></p>
                            <?php }elseif($key%3 == 2){?>
                            <p class="step_checkboxW3"><input type="checkbox" name="step" value='<?=$val['id'];?>' <?php if($step['stage_id']){if(in_array($val['id'],$step['stage_id'])){echo 'checked';}}?>><?=$val['stage_name'];?></p>
                            <?php }?>
                        <?php }}?>
                            <div class="errorBox" id="step_error"></div>
                        </dt>
                  </dl>
                  <dl class="qz_moudle_add_step_stage"  style="width:96%;padding:0 2%;">
                        <dd>备注：</dd>
                        <dt>
                        <textarea class="qz_add_step_textarea" name="transfer_remark"><?=$step['remark'];?></textarea>
                        <div class="errorBox"></div>
                        </dt>
                  </dl>
            </div>
            <span class="qz_add_step_textarea_underline"></span>
            <div class="qz_add_step_remind" style="width:96%;margin:14px 2%;">
                <p><input type="checkbox" value="1" name="is_remind" <?=1==$step['is_remind']?'checked':'';?>><b>提醒</b></p>

                <span style="width:86%;float:left;display:inline;">
                    <dl class="qz_moudle_add_step_stage content_W100">
                        <dd>提醒时间：</dd>
                        <dt  style="float:left;">
                        <input type="text" class="aad_pop_select_W100 time_bg" name="remind_time" value="<?=$step['remind_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off" <?=0==$step['is_remind']?'disabled':'';?>>
                        <div class="errorBox"></div>
                        </dt>
                  </dl>
                  <dl class="qz_moudle_add_step_stage content_W100">
                        <dd style="float:left;display:inline;">提醒对象：</dd>
                        <dt style="width:320px;float:left;display:inline;">
                            <div style="float: left;display: inline;width: 126px;">
                            <select class="step_select bottomP14" style="width:120px;float:left;display:inline;padding:0 0 0 5px;" name="remind_department_id" <?=0==$step['is_remind']?'disabled':'';?>>
                                <?php foreach($departments as $key =>$val){?>
                                <option value="<?=$val['id']?>" <?=$val['id']==$step['remind_department_id']?'selected':'';?>><?=$val['name']?></option>
                                <?php }?>
                            </select>
                            <span class="errorBox zws_block" style="float:none;"></span>
                            </div>
                            <div  style="float: left;display: inline;">
                            <select class="step_select bottomP14" style="float:left;display:inline;" name="remind_signatory_id" <?=0==$step['is_remind']?'disabled':'';?>>
                                <?php foreach($signatorys as $key =>$val){?>
                                <option value="<?=$val['signatory_id']?>" <?=$val['signatory_id']==$step['remind_signatory_id']?'selected':'';?>><?=$val['truename']?></option>
                                <?php }?>
                            </select>
                            </div>
                        </dt>
                  </dl>
                  <dl class="qz_moudle_add_step_stage content_W100">
                        <dd>提醒内容：</dd>
                        <dt style="width:85%;float:left;">
                            <textarea style="float:left;" class="qz_add_step_textarea bottomP14" name="remind_remark" <?=0==$step['is_remind']?'disabled':'';?>><?=$step['remind_remark'];?></textarea>
                            <div class="errorBox"></div>
                        </dt>
                  </dl>
                </span>
            </div>
            <input type="hidden" id="bargain_id" value="<?=$c_id?>">
            <input type="hidden" id="stage_id" value="<?=$temp_id?>">
            <div  class="qz_moudle_con1 TB11"><button type="submit" style="font-weight: normal;border:none;background: #1cc681;padding:0 10px;color: #FFF;line-height: 24px;border-radius: 3px;">保存</button></div>
        </div>
        </form>
    </div>
</body>

                <script>
                    $(function(){
                        $("input[type='checkbox']").click(function(){
                            $("#step_error").text('');
                        });

                        $("input[name='is_remind']").click(function(){
                            if($("input[name='is_remind']:checked").val() ==1){
                                $("select[name='remind_department_id']").removeAttr('disabled');
                                $("select[name='remind_signatory_id']").removeAttr('disabled');
                                $("input[name='remind_time']").removeAttr('disabled');
                                $("textarea[name='remind_remark']").removeAttr('disabled');
                            }else{
                                $('.qz_add_step_remind .errorBox').text('');
                                $("select[name='remind_department_id']").attr('disabled',true);
                                $("select[name='remind_signatory_id']").attr('disabled',true);
                                $("input[name='remind_time']").attr('disabled',true);
                                $("textarea[name='remind_remark']").attr('disabled',true);
                            }
                        })

                        $("select[name='remind_department_id']").change(function(){
                        var department_id = $("select[name='remind_department_id']").val();
                        if(department_id){
                            $.ajax({
                                url:"/bargain_earnest_money/signatory_list",
                                type:"GET",
                                dataType:"json",
                                data:{
                                   department_id:department_id
                                },
                                success:function(data){
                                    if(data['result'] == 1){
                                    var html = "<option value=''>请选择</option>";
                                    for(var i in data['list']){
                                        html+="<option value='"+data['list'][i]['signatory_id']+"'>"+data['list'][i]['truename']+"</option>";
                                    }
                                    $("select[name='remind_signatory_id']").html(html);
                                    }else{
                                    $("select[name='remind_signatory_id']").html("<option value=''>请选择</option>");
                                    }
                                }
                            })
                        }else{
                            $("select[name='remind_signatory_id']").html("<option value=''>请选择</option>");
                        }
                        })
                    })
				</script>
    <!---->
