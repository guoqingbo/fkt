<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Issue_msg_model CLASS
 *
 * 后台发布消息模型类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          kang
 */
class Issue_msg_model extends MY_Model
{

  //消息链接地址
  private $_arr_url_type = array(
    array('url' => '/sell/lists_pub/', 'name' => '合作中心', 'type' => 1),
    array('url' => '/house_collections/collect_sell/', 'name' => '采集中心', 'type' => 2),
    array('url' => '/entrust_center/ent_sell/', 'name' => '营销中心', 'type' => 3),
    array('url' => '', 'name' => '自定义', 'type' => 0),
  );

  public function arr_url_type()
  {
    $this->dbback_city->where('is_display', 1);
    $this->dbback_city->select('name,url');
    $result = $this->dbback_city->get('permission_module')->result_array();
    foreach ($result as $key => $val) {
      if (substr($val['url'], 0, 1) != "/") {
        $result[$key]['url'] = '/' . $val['url'];
      }
    }
    return $result;
  }

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->issue_msg = 'message';  //消息表
    $this->broker_info = 'broker_info';  //经纪人表
    $this->message_broker = 'message_broker'; //经纪人与消息关系表
  }


  /**
   * 获取消息总数
   */
  function get_num($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->issue_msg, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $result[0]['num'];
  }

  /**
   * 获得所有消息
   */
  public function get_issue_msg($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->issue_msg, 'where' => $where, 'order_by' => 'createtime', 'limit' => $offset, 'offset' => $pagesize), $database);
    return $result;
  }

  /**
   * 发布新消息
   */
  function add($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->issue_msg);
    return $result;
  }

  /**
   * 根据消息ID获得详情
   */
  public function getinfo_byid($id = '', $database = 'dbback_city')
  {
    $wherecond = array('id' => $id);
    $result = $this->get_data(array('form_name' => $this->issue_msg, 'where' => $wherecond), $database);
    return $result;
  }

  /**
   * 保存修改后的消息
   */
  function modify($id, $paramlist = array(), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->issue_msg);
    return $result;
  }

  /**
   * 删除消息
   */
  function del_issue_msg($id = '')
  {
    $result = $this->del(array('id' => $id), 'db_city', $this->issue_msg);
    return $result;
  }

  /**
   * 获取所有经纪人信息
   */
  function get_all_broker($where, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->broker_info, 'select' => array('broker_id'), 'where' => $where), $database);
    return $result;
  }

  /**
   * 添加数据到message_broker #发布时用到
   */
  function add_message_broker($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->message_broker);
    return $result;
  }

  /**
   * 根据msg_id删除message_broker中的数据
   */
  function del_message_broker($id = '')
  {
    $result = $this->del(array('msg_id' => $id), 'db_city', $this->message_broker);
    return $result;
  }

  /**
   * 获取消息链接地址
   * @param int $url_type 链接类型
   * @param string $url 链接地址
   * @return string
   */
  function get_msg_url($url_type, $url = '')
  {
    $new_url = '';
    if ($url_type == 0) //自定义
    {
      $new_url = $url;
    } else {
      $arr_url_type = change_to_key_array($this->_arr_url_type, 'type');
      $new_url = $arr_url_type[$url_type]['url'];
    }
    return $new_url;
  }

  /**
   * 通过url找到 type
   * @param type $url
   * @return type
   */
  public function get_msg_url_type($url)
  {
    $url_type = 0;
    $arr_url_type = change_to_key_array($this->_arr_url_type, 'url');
    if (isset($arr_url_type[$url])) {
      $url_type = $arr_url_type[$url]['type'];
    }
    return $url_type;
  }
}

/* End of file issue_msg_model.php */
/* Location: ./app/models/issue_msg_model.php */
