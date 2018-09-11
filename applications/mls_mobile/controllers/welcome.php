<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller
{
  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  public function index()
  {
    echo '欢迎进入app项目';
  }

  public function test()
  {
    $title = '欢迎';
    ?>

    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="Generator" content="EditPlus®">
      <meta name="Author" content="">
      <meta name="Keywords" content="">
      <meta name="Description" content="">
      <title>Document</title>
    </head>
    <body>
    <script type="text/javascript"
            src="<?php echo MLS_SOURCE_URL; ?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js"></script>
    <script type="text/javascript">
      function request() {
        var method = $("#method").val();

        if (method == "post") {
          do_post();
        } else {
          do_get();
        }
      }

      function do_get() {
        var url = $("#url").val();
        var parme = $("#parme").val();

        $.ajax({
          url: url + parme,
          data: {},
          type: "get",
          cache: false,
          dataType: "text",
          success: function (data) {
            $("#return").html(data);
          },
          error: function () {
            alert("异常！");
          }
        });
      }

      function do_post() {
        var url = $("#url").val();
        var parme = $("#parme").val();
        var parme2 = $("#parme2").val();
        var scodestr = $("#scode").val();
        var dotype = $("#do").val();

        parme = parme.replace('scodeinfo', scodestr);
        parme = parme2 != '' ? '{' + parme + ',' + parme2 + '}' : '{' + parme + '}';

        if (dotype == 1) {
          $.ajax({
            url: url,
            data: parme,
            type: "post",
            cache: false,
            dataType: "text",
            success: function (data) {
              $("#return").html(data);
            },
            error: function () {
              alert("异常！");
            }
          });
        }
        else {
          url = "<?php echo MLS_MOBILE_URL;?>/login/signin/";

          $.ajax({
            url: url,
            data: {phone: "15951634202", password: "123456", deviceid: "99999123456", api_key: "android"},
            type: "post",
            cache: false,
            dataType: "text",
            success: function (data) {
              $("#return").html(data);
            },
            error: function () {
              alert("异常！");
            }
          });
        }
      }
    </script>
    <table>
      <tr>
        <td>提交方式:</td>
        <td><input type="text" id="method" value="post"/></td>
      </tr>
      <tr>
        <td>操作:</td>
        <td><input type="text" id="do" value="1"/>0：登录 1：其他</td>
      </tr>
      <tr>
        <td>scode:</td>
        <td><input type="text" id="scode" style="width:800px;" value=""/></td>
      </tr>
      <tr>
        <td>提交地址:</td>
        <td><input type="text" id="url" style="width:1200px;"
                   value="<?php echo MLS_MOBILE_URL; ?>/house_collections/collect_sell/"/></td>
      </tr>
      <tr>
        <td>固定参数:</td>
        <td><input type="text" id="parme" style="width:1200px;"
                   value='api_key:"android",scode:"scodeinfo",app_channel:"<?= $title ?>",deviceid:"99999123456",version:"1.0.3"'/>
        </td>
      </tr>
      <tr>
        <td>提交参数:</td>
        <td><input type="text" id="parme2" style="width:1200px;"
                   value='page_size:"20",page:"1",district_cj:"3",house_name:"瑞金路"'/></td>
      </tr>
      <tr>
        <td>提交方式:</td>
        <td><input type="button" onclick="request();" value="开始"/></td>
      </tr>
    </table>
    <div id="return" style="width:800px;height:400px;">

    </div>
    </body>
    </html>

    <?php
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
