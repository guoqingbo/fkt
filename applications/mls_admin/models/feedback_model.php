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
 * Feedback_model CLASS
 *
 * 意见反馈业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Feedback_base_model");

class Feedback_model extends Feedback_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->feedback = 'feedback';
  }

  /**
   * 获得意见反馈
   */
  public function get_feedback($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback')
  {
    $data = $this->get_data(array('form_name' => $this->feedback, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize, 'order_by' => array('dateline', 'asc')), $database);
    return $data;
  }

  /**
   * 根据ID获得详情
   */
  public function get_feedback_by_id($id = '', $database = 'dbback')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->feedback, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 获取意见反馈总数
   */
  function get_feedback_num($where, $database = 'dbback')
  {
    $node = $this->get_data(array('form_name' => $this->feedback, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $node[0]['num'];
  }

  /**
   * 添加意见反馈
   */
  function add_feedback($paramlist = array(), $database = 'db')
  {
    $result = $this->add_data($paramlist, $database, $this->feedback);
    return $result;
  }

  /**
   * 修改意见反馈
   */
  function modify_feedback($id, $paramlist = array(), $database = 'db')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->feedback);
    return $result;
  }

  /**
   * 删除意见反馈
   */
  function del_feedback($id = '')
  {
    $result = $this->del(array('id' => $id), 'db', $this->feedback);
    return $result;
  }


}

/* End of file feedback_model.php */
/* Location: ./app/models/feedback_model.php */
