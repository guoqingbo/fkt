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
 * sell_house_model CLASS
 *
 * 平安好房
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */
class Pinganhouse_base_model extends MY_Model
{

  /**
   * 表名
   *
   * @access private
   * @var string
   */
  private $_tbl = 'pinganhouse';

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 出售、出租获取符合条件的房源
   *
   * @access  public
   * @cond_where 查询条件
   * @return  int   符合条件的房源信息
   */
  public function get_list_by_cond($cond_where, $offset = 0, $limit = 10, $order_key = "outside_time", $order_by = "DESC")
  {
    $this->dbback_city->select('p.id,s.id as house_id,s.block_name,district_id,street_id,buildarea,price,room,floor,p.outside_time,is_check,b.phone,b.truename,b.broker_id,p.is_outside');
    if ($cond_where) {
      $this->dbback_city->where($cond_where);
    }
    $this->dbback_city->join('sell_house s', 's.id = p.house_id');
    $this->dbback_city->join('broker_info b', 'b.broker_id = s.broker_id');
    $this->dbback_city->limit($limit, $offset);
    $this->dbback_city->order_by($order_key, $order_by);
    $result = $this->dbback_city->get($this->_tbl . ' p')->result_array();
    return $result;
  }

  /**
   * 获取符合条件的房源数量
   *
   * @access  public
   * @cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_num_by($cond_where)
  {
    if ($cond_where) {
      $this->dbback_city->where($cond_where);
    }
    $this->dbback_city->join('sell_house s', 's.id = p.house_id', 'left');
    $this->dbback_city->join('broker_info b', 'b.id = s.broker_id', 'left');
    $count_num = $this->dbback_city->count_all_results($this->_tbl . ' p');
    return intval($count_num);
  }

  public function add_house($data)
  {
    $this->dbback_city->where('house_id', $data['house_id']);
    $result = $this->dbback_city->get($this->_tbl)->row_array();
    if (empty($result)) {
      $this->db_city->insert($this->_tbl, $data);
      return $this->db_city->affected_rows();
    }
  }

  public function update_house($id, $data)
  {
    $this->db_city->where('id', $id);
    $this->db_city->update($this->_tbl, $data);
    return $this->db_city->affected_rows();
  }

  /**
   * 获取某条房源的图片
   * @param string $tbl 表名
   * @param int $house_id 房源编号
   * @param int $sort 图片类型
   * @return array
   */
  public function find_house_pic_by($tbl, $house_id, $sort = '')
  {
    $uploadtable = 'upload';
    $this->db_city->select('id,type,url,is_top');
    $this->db_city->where('tbl', $tbl);
    $this->db_city->where('rowid', $house_id);
    $this->db_city->order_by('is_top', 'DESC');
    if ($sort !== '') {
      $this->db_city->where('sort', $sort);
    }
    return $this->db_city->get($uploadtable)->result_array();
  }

  public function get_info_by_id($id)
  {
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }
}

/* End of file sell_house_model.php */
/* Location: ./applications/mls/models/sell_house_model.php */
