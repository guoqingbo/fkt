<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Update_agentcy_permission extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 初始化公司权限
   * @access public
   * @return void
   * date 2015-09-07
   * author angel_in_us
   */
  public function initialize_agency_permission()
  {
    ini_set("max_execution_time", "86400");
    //数据库连接
    $con_mls = mysql_connect("172.17.1.192", "root", "idontcare");#南京二手房-172.17.1.50 数据库 - house;   数据表 - agency
    //判断连接成功与否
    if (!$con_mls) {
      die('Could not connect: ' . mysql_error());
    }
    $city = $this->input->get('city', TRUE);
    mysql_select_db('mls_' . $city, $con_mls);#选择要操作的数据库
    //查询所需字段
    $agency_result = mysql_query("SELECT id  FROM agency where company_id = 0 limit 0,2000", $con_mls);#要初始化的公司 id（对应 permission_company_group 表中的 company_id）
    $sys_per_result = mysql_query("SELECT id,func_auth FROM permission_system_group limit 0,10", $con_mls);#取出系统权限的角色表中所需字段
    $sys_per_arr = array();
    while ($row = mysql_fetch_assoc($sys_per_result)) {
      $sys_per_arr[] = $row;
    }
//        echo '<pre>';print_r($sys_per_arr);die;
    while ($row = mysql_fetch_assoc($agency_result)) {
      foreach ($sys_per_arr as $key => $value) {
        //组装 sql
        $sql = "insert into permission_company_group (company_id,system_group_id,func_auth) values(" . $row['id'] . "," . $value['id'] . ",'" . $value['func_auth'] . "')";
        $res = mysql_query($sql, $con_mls);
        if (!$res) {
          exit('数据插入有误，请稍后重试。');
        }
      }
    }
    exit('公司初始化权限完成~！');
  }


  /**
   * 刷数据：总经理权限挂靠、其他经纪人权限挂靠
   * （新建公司绑定权限、已开通经纪人权限刷数据）
   * update_agent_permission =》 已开通经纪人权限刷数据
   * @access public
   * @return void
   * date 2015-09-07
   * author angel_in_us
   */
  public function update_agent_permission()
  {
    ini_set("max_execution_time", "86400");
    //数据库连接
    $con_mls = mysql_connect("172.17.1.192", "root", "idontcare");#南京二手房-172.17.1.50 数据库 - house;   数据表 - agency
    //判断连接成功与否
    if (!$con_mls) {
      die('Could not connect: ' . mysql_error());
    }
    $city = $this->input->get('city', TRUE);
    mysql_select_db('mls_' . $city, $con_mls);#选择要操作的数据库
    //查询所需字段：请注意该 查询语句的 limit 用法，请根据 broker_info 表里 的数据量来设置查询下标
    $agent_result = mysql_query("SELECT broker_id,company_id,package_id  FROM broker_info  limit 0,7000", $con_mls);#要刷权限的经纪人信息
    #循环遍历经纪人数据，把表里的 role_id 刷为对应的权限值
    while ($row = mysql_fetch_assoc($agent_result)) {
      //1店长权限 刷数据时给予总经理权限  level =》 1
      if ($row['package_id'] == '1') {
        $com_per_result = mysql_query("SELECT id FROM permission_company_group where company_id = " . $row['company_id'] . " and system_group_id = 1", $con_mls);#取出公司权限的角色表中所需字段
        $rel = mysql_fetch_assoc($com_per_result);
        $result = mysql_query("update broker_info set role_id = " . $rel['id'] . ' where broker_id =' . $row['broker_id'], $con_mls);#改变 broker_info 表中的 role_id 字段
        unset($rel);
        unset($result);
      }
      //2经纪人权限 刷数据时给予见习经纪人权限  level =》 9
      if ($row['package_id'] == '2') {
        $com_per_result = mysql_query("SELECT id FROM permission_company_group where company_id = " . $row['company_id'] . " and system_group_id = 9", $con_mls);#取出公司权限的角色表中所需字段
        $rel = mysql_fetch_assoc($com_per_result);
        $result = mysql_query("update broker_info set role_id = " . $rel['id'] . ' where broker_id =' . $row['broker_id'], $con_mls);#改变 broker_info 表中的 role_id 字段
        unset($rel);
        unset($result);
      }
    }
    exit('已开通经纪人权限刷数据完成~！');
  }
}

/* End of file dldagency.php */
/* Location: ./application/mls_admin/controllers/dldagency.php */
