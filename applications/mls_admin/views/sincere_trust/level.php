<?php require APPPATH . 'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?b=mls&amp;f=css/v1.0/home.css" rel="stylesheet" type="text/css">
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>信用等级</th>
                                    <th>下限</th>
                                    <th>上限</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($level) && !empty($level)) {
                                foreach ($level as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td style="width: 30%;">
                                            <?php echo $value['name']; ?>
                                        </td>
                                        <td style="width: 30%;">
                                        <span class="score"><?php echo $value['down']; ?></span>
                                        </td>
                                        <td style="width: 30%;"> 
                                        <span class="score" ><?php echo $value['up']; ?></span>
                                        </td>
                                        <td style="width: 10%;">
                                        <!--
                                        <input type="hidden" name="id" value="<?php echo $value['id']; ?>">
                                        <a class="editBtn" status="1" href="#">修改</a>  --></td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(".editBtn").click(function() {
    var btn = this;
    var status = $(btn).attr('status');
    var setStatus = function(obj, status) {
        var statusO = {1: {name: '修改', color: 'black'},
            2: {name: '保存', color: 'green'}, 3: {name: '正在保存', color: 'black'}}
        $(obj).attr('status', status);
        $(obj).css('color', statusO[status].color)
        $(obj).html(statusO[status].name);
    }
    var scores = $(btn).parents('tr').find('.score');
    if (status == 1) {
        scores.each(function(i, n) {
            var v = $(n).html();
            var x = 'onkeyup="(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,\'\');}).call(this)" onblur="this.v();" ';
            $(n).html("<input type='text' name='score' " + x + " value='" + v + "'/>")
        })
        setStatus(this, 2);
    }
    if (status == 2) {
        var data = [];
        scores.each(function(i, n) {
            var v = $(n).find('input').val();
            var temp = {};
            temp.score = parseInt(v);
            temp.way_alias = $(n).next().val();
            temp.id = $(btn).prev().val();
            data.push(temp);
        })
        setStatus(btn, 3);
        $.post('/sincere_weight/save/', {data: data}, function(re) {
            if (re.result == 1) {
                scores.each(function(i, n) {
                  var v = $(n).find('input').val();
                    v = parseInt(v);
                    if(isNaN(v)) { v = 0;}
                    if (v < 0) {
                        $(n).css('color', 'red');
                    } else {
                        $(n).css('color', 'black');
                    }
                    $(n).html(v)
                })
                setStatus(btn, 1);
            } else {
                alert("保存失败！")
            }
        }, 'json')
    
    }
    return false;
});
</script>
<?php require APPPATH . 'views/footer.php'; ?>
