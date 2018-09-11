<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2015
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Notice_access_base_model CLASS
 *
 * 委托房源业务基础类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Notice_access_base_model extends MY_Model
{

  public $department_tbl = 'na_department';
  public $group_tbl = 'na_group';
  public $message_tbl = 'na_message';
  public $notice_type_tbl = 'na_notice_type';
  public $user_tbl = 'na_user';
  public $job_tabl = 'na_job';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  //获取全部通知消息类型
  public function get_notice_type()
  {
    $return_arr = $type_arr = array();
    $this->dbback->select('id, type_name');
    $this->dbback->limit(20, 0);
    //查询
    $type_arr = $this->dbback->get($this->notice_type_tbl)->result_array();

    if (is_array($type_arr) && !empty($type_arr)) {
      foreach ($type_arr as $type) {
        $return_arr[$type['id']] = $type['type_name'];
      }
    }

    return $return_arr;
  }

  //增加通知消息
  public function add_message($insert_arr)
  {
    return $this->add_data($insert_arr, 'db', $this->job_tabl);
  }

  //查询通知信息
  public function get_jobs_info($where = array(), $like = array(), $limit = 10, $database = 'dbback')
  {
    $order_by = 'id';
    return $this->get_data(array('form_name' => $this->job_tabl, 'where' => $where, 'like' => $like, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
  }
}

/* End of file notice_access_base_model.php */
/* Location: ./application/models/notice_access_base_model.php */
