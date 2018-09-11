<meta charset="gb2312">
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 批量修改南京站的 community 表 （小区/楼盘表中）的 green_rate （绿化率）字段
   * （修改标准依照：admi.houe365.com 中的 house数据库 block  表 中的 b_green 字段）
   * @access public
   * @return void
   * date 2015-07-09
   * author angel_in_us
   */
  public function index()
  {
    ini_set("max_execution_time", "60000000000000");
    //数据库连接
    $con_nj = mysql_connect("172.17.1.50", "root", "idontcare");#南京二手房-172.17.1.50 数据库 - house;   数据表 - block
    $con_ms = mysql_connect("172.17.1.50", "root", "idontcare");#南京/合肥二手房-172.17.1.50 数据库 - mls_nj/mls_hf;数据表 - community
    //判断连接成功与否
    if (!$con_nj) {
      die('Could not connect: ' . mysql_error());
    }


    mysql_select_db("mls_nj", $con_ms);#选择要修改的数据库
    //查询所需字段
    $result1 = mysql_query("SELECT cmt_name  FROM community  limit 1001,1000", $con_ms);


    while ($row1 = mysql_fetch_assoc($result1)) {
      $row2 = array();
      mysql_select_db("house", $con_nj);#选择要参考的数据库
      $sql = 'SELECT blockname,b_green  FROM block where blockname ="' . $row1['cmt_name'] . '"';#按条件组装sql从block表中查出绿化率 b_green
      $result2 = mysql_query($sql, $con_nj);
      $row2 = mysql_fetch_assoc($result2);
      $row2['b_green'] = (float)$row2['b_green'] / 100;#把绿化率由百分比转换为小数点 double 类型的
      echo $sql . $row2['b_green'] . '<hr>';
      $sql = 'update community set green_rate = "' . $row2['b_green'] . '"   where cmt_name ="' . $row1['cmt_name'] . '"';#同步更新community表中的 green_rate
      echo $sql . '<br>';
      mysql_select_db("mls_nj", $con_ms);#选择要修改的数据库
      $rel = mysql_query($sql, $con_ms);
      if ($rel) {
        #更新成功
        echo $row1['cmt_name'] . ' success~!<br>';
      } else {
        #更新失败
        echo $row1['cmt_name'] . ' failed~!<br>';
      }
    }
    exit('update finished~!');
  }
}

/* End of file test.php */
/* Location: ./application/mls_admin/controllers/test.php */
