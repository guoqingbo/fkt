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
 * broker_info_model CLASS
 *
 * 经纪人信息基础类 提供挂靠公司的关系
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Signatory_info_base_model");

class Signatory_info_model extends Signatory_info_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获取搜索配置文件
   * @return array
   */
  public function get_where_config()
  {
    $this->load->model('agency_model');
    $where_config = array();
    //搜索条件
    $where_config['search_where'] = array(
      'phone' => '手机号码', 'truename' => '真实姓名'
    );
    //搜索时间条件
    $where_config['search_time'] = array(
      'register_time' => '注册时间', 'login_time' => '登录时间', 'auth_time' => '认证时间',
    );
    //用户组
      //$where_config['group'] = $this->broker_info_config['group'];
    //套餐
      //$where_config['package'] = $this->broker_info_config['package'];
    //经纪人是否有效
    $where_config['status'] = array(
      1 => '有效', 2 => '无效'
    );
//    $where_config['area'] = array(
//      1 => '联网经纪人', 2 => '非联网经纪人'
//    );
    return $where_config;
  }

  /**
   * 经纪人数组格式化成以经纪人编号组成的一维数组
   * @param array $brokers 经纪人信息
   * @return array
   */
  public function format_brokers($brokers)
  {
    $broker_ids = array();
    if (is_full_array($brokers)) {
      foreach ($brokers as $v) {
        $broker_ids[] = $v['broker_id'];
      }
    }
    return $broker_ids;
  }

  /**
   * 获取临时表数据
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 临时表的多维数组
   */
  public function get_tmp($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => 'tmp_uploads', 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }
}

/* End of file Broker_info_model.php */
/* Location: ./app/models/Broker_info_model.php */
