<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<form action="" method="post">
    <input type='hidden' name='submit_flag' value='1'/>
    <input type='hidden' name='id' value='<?php echo @$info['id'];?>'/>
    考勤是否分上下午：
    <select name="is_am_pm">
        <option value="0" <?php if(@$info['is_am_pm'] == 0){echo "selected='selected'";}?>>否</option>
        <option value="1" <?php if(@$info['is_am_pm'] == 1){echo "selected='selected'";}?>>是</option>
    </select>
    <br/>
    规定上班时间：<input type="text" name="start_time"  onclick="WdatePicker({lang:'zh-cn',dateFmt:'HH:mm:ss'})" value="<?php echo @$info['start_time'];?>">
    <br/>
    规定下班时间：<input type="text" name="end_time"  onclick="WdatePicker({lang:'zh-cn',dateFmt:'HH:mm:ss'})" value="<?php echo @$info['end_time'];?>">
    <br/>
    每天第一次登录自动考勤
    <select name="is_first">
        <option value="0" <?php if(@$info['is_first'] == 0){echo "selected='selected'";}?>>否</option>
        <option value="1" <?php if(@$info['is_first'] == 1){echo "selected='selected'";}?>>是</option>
    </select>
    <br/>
    系统自动防护时间<input type="text" name="protect_time" value="<?php echo @$info['protect_time'];?>"/>分钟内（误操作自动安全切换到登录窗口）
    <br/>
    滚屏刷新时间<input type="text" name="refresh_time" value="<?php echo @$info['refresh_time'];?>"/>秒
    <br/>
    未处理跟进和任务是否自动弹出提醒
    <select name="is_remind">
        <option value="0" <?php if(@$info['is_remind'] == 0){echo "selected='selected'";}?>>否</option>
        <option value="1" <?php if(@$info['is_remind'] == 1){echo "selected='selected'";}?>>是</option>
    </select>
    <br/>
    <input type="submit" value="保存所有设置"/>
</form>
