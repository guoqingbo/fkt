<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Features_notice_model CLASS
 *
 * 功能迭代通知表数据模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Features_notice_model extends MY_Model
{

  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';

  /**
   * 功能迭代通知表表名称
   * @var string
   */
  private $_tbl = 'features_notice';

  /**
   * 功能迭代通知表表名称
   * @var string
   */
  private $_tb2 = 'features_leave_message';

  /**
   * 功能迭代通知上传文件表名称
   * @var string
   */
  private $_tb3 = 'features_notice_file';

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 筛选功能迭代通知表
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以功能迭代通知表信息组成的多维数组
   */
  public function getfeatures_notice($where = array(), $like = array(), $offset = 0, $pagesize = 0, $order_by = '', $database = 'dbback')
  {
    $comm = $this->get_data(array('form_name' => $this->_tbl, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize, 'order_by' => $order_by), $database);
    return $comm;
  }

  /**
   * 筛选功能迭代文件
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以文件表信息组成的多维数组
   */
  public function get_notice_file($where = array(), $database = 'dbback')
  {
    $comm = $this->get_data(array('form_name' => $this->_tb3, 'where' => $where), $database);
    return $comm;
  }

  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->select_fields;
  }


  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';

    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }

    $this->select_fields = $select_fields;
  }

  /**
   * 获得功能迭代通知表总数
   * @param array $where where字段
   * @return string 功能迭代通知表总数
   */
  public function get_features_notice_num($where = array(), $like = array())
  {
    $comm = $this->get_data(array('form_name' => $this->_tbl, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), 'dbback');
    return $comm[0]['num'];
  }


  /**
   * 添加功能迭代通知表
   * @param array $paramlist 功能迭代通知表字段
   * @return insert_id or 0
   */
  function add_features_notice($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db', $this->_tbl);
    return $result;
  }

  /**
   * 添加功能迭代通知上传表
   * @param array $paramlist 功能迭代通知上传文件表字段
   * @return insert_id or 0
   */
  function add_features_notice_file($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db', $this->_tb3);
    return $result;
  }


  /**
   * 根据功能迭代通知表ID,提交数据,修改相关功能迭代通知表详情
   * @param string $commid 功能迭代通知表ID
   * @param array $paramlist 功能迭代通知表修改字段
   * @return 0 or 1
   */
  function modify_features_notice($commid, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $commid), $paramlist, 'db', $this->_tbl);
    return $result;
  }

  /**
   * 修改文档
   * @param string $commid 功能迭代通知表ID
   * @param array $paramlist 功能迭代通知表修改字段
   * @return 0 or 1
   */
  function modify_features_notice_file($commid, $file_num, $paramlist = array())
  {
    $result = $this->modify_data(array('notice_id' => $commid, 'file_num' => $file_num), $paramlist, 'db', $this->_tb3);
    return $result;
  }

  /**
   * 删除
   * @param string $commid 功能迭代通知表ID
   * @return 0 or 1
   */
  function del_features_notice($commid = '')
  {
    $result = $this->del(array('id' => $commid), 'db', $this->_tbl);
    return $result;
  }

  /**
   * 添加留言
   * @param array $paramlist
   * @return insert_id or 0
   */
  function add_features_message($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db', $this->_tb2);
    return $result;
  }


  /**
   * 根据功能迭代通知表ID获得详情
   * @param string $id 功能迭代通知表ID
   * @return array 以功能迭代通知表信息组成的多维数组
   */
  public function get_details_by_id($id = '')
  {
    $wherecond = array('id' => $id);
    $commData = $this->get_data(array('form_name' => $this->_tbl, 'where' => $wherecond), 'dbback');
    return $commData;
  }

  /**
   * 根据通知id，获得留言
   * @param string $id 功能迭代通知表ID
   * @return array 以留言信息组成的多维数组
   */
  public function get_message_by_notice_id($id = '')
  {
    $wherecond = array('notice_id' => $id);
    $commData = $this->get_data(array('form_name' => $this->_tb2, 'where' => $wherecond), 'dbback');
    return $commData;
  }

  /**
   * 根据通知id，获得附件
   * @param string $id 功能迭代通知表ID
   * @return array 以文件信息组成的多维数组
   */
  public function get_file_by_notice_id($id = '')
  {
    $wherecond = array('notice_id' => $id);
    $commData = $this->get_data(array('form_name' => $this->_tb3, 'where' => $wherecond), 'dbback');
    return $commData;
  }


}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
