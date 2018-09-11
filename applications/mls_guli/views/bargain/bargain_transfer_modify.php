<body>
<!--权证添加和修改弹窗-->
    <div class="achievement_money_pop qz_add_step_w610" id="js_temp_pop">
        <dl class="title_top">
            <dd id="title_top3"><?=$temp_id?'编辑':'添加';?>权证流程步骤</dd>
        </dl>
        <form action="" id="transfer_form_modify" method="post">
            <input type="hidden" name="step" value='<?=$step['stage_id'];?>'>
            <div style="height: 400px;overflow-y: auto;overflow-X: hidden;width: 100%;">
                <div class="qz_moudle_add_step" style="width:96%;padding:0 2%;">
                      <dl class="qz_moudle_add_step_type">
                          <dd>权证流程模板：<b><?=$transfer_info['template_name'];?></b></dd>
                          <dt>步骤：<b id="step_name"><?=$temp_id?$stage_conf[$step['step_id']]['text']:$stage_conf[$total_step+1]['text'];?></b></dt>
                      </dl>
                    <dl class="qz_moudle_add_step_stage">
                        <dd>流程分类：</dd>
                        <dt  style="float:left;">
                            <select class="step_select"  name="stage_type">
                                <option value="">请选择</option>
                                <?php if($config['stage_type']){foreach($config['stage_type'] as $key =>$val){?>
                                    <option value="<?=$key;?>" <?=$step['stage_type']==$key?'selected':''?>><?=$val;?></option>
                                <?php }}?>
                            </select>
<!--                            <input type="text" class="qz_add_step_text" name="stage_type" value="--><?//=$step['stage_type'];?><!--"  autocomplete="off" ;?>-->
                            <div class="errorBox"></div>
                        </dt>
                    </dl>
                    <dl class="qz_moudle_add_step_stage">
                        <dd>天数：</dd>
                        <dt  style="float:left;">
                            <input type="text" class="qz_add_step_text" name="number_days" value="<?=$step['number_days'];?>"  autocomplete="off" ;?>
                        <div class="errorBox"></div>
                        </dt>
                    </dl>
                    <dl class="qz_moudle_add_step_stage">
                        <dd>开始日期：</dd>
                        <dt  style="float:left;">
                            <input type="text" class="qz_add_step_text time_bg" name="start_time"  value="<?=isset($step['start_time'])?date('Y-m-d',$step['start_time']):'';?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off" ;?>
                        <div class="errorBox"></div>
                        </dt>
                    </dl>
                    <dl class="qz_moudle_add_step_stage">
                        <dd>完成日期：</dd>
                        <dt  style="float:left;">
                            <input type="text" class="qz_add_step_text time_bg" name="complete_time" value="<?=isset($step['complete_time'])?date('Y-m-d',$step['complete_time']):'';?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off" ;?>
                        <div class="errorBox"></div>
                        </dt>
                    </dl>
                    <dl class="qz_moudle_add_step_stage">
                        <dd>结案人：</dd>
                        <dt  style="float:left;">
                            <select  class="step_select" name="complete_signatory_id">
                                <option value="">请选择</option>
                                <?php foreach($signatorys as $key=>$val){?>
                                    <option value="<?=$val['signatory_id'];?>" <?=$val['signatory_id']==$signatory_id?'selected':''?>><?=$val['truename'];?></option>
                                <?php }?>
                            </select>
                        <div class="errorBox"></div>
                        </dt>
                    </dl>
                    <script>
                        $("select[name='complete_department_id']").change(function(){
                            var department_id = $("select[name='complete_department_id']").val();
                            if(department_id){
                                $.ajax({
                                    url:"/bargain/signatory_list",
                                    type:"GET",
                                    dataType:"json",
                                    data:{
                                        department_id:department_id
                                    },
                                    success:function(data){
                                        var html = "<option>请选择人员</option>";
                                        if(data['result'] == 1){
                                            for(var i in data['list']){
                                                html+="<option value='"+data['list'][i]['signatory_id']+"'>"+data['list'][i]['truename']+"</option>";
                                            }
                                        }
                                        $("select[name='complete_signatory_id']").html(html);
                                    }
                                })
                            }else{
                                $("select[name='complete_signatory_id']").html("<option value=''>请选择</option>");
                            }
                        })
                    </script>
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
