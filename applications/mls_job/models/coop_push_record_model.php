<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

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
 * Coop_push_record_model CLASS
 *
 * 房客源推送消息记录
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Coop_push_record_model extends MY_Model
{

  /**
   * 房客源推送消息记录表
   *
   * @access private
   * @var string
   */
  private $_tbl = 'coop_push_record';

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 插入消息记录数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的id 失败 false
   */
  public function insert($insert_data)
  {
    $insert_data['create_time'] = time();
    if ($this->db_city->insert($this->_tbl, $insert_data)) {
      return $this->db_city->insert_id();
    }
    return false;
  }

  /**
   * 获取房客源的推送信息
   * @param int $tbl 类型
   * @param int $house_id 房客源编号
   */
  public function get_by_tbl_house_id($tbl, $house_id)
  {
    $this->dbback_city->where('tbl', $tbl);
    $this->dbback_city->where('house_id', $house_id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }
}

/* End of file Coop_push_record_model.php */
/* Location: ./applications/mls/models/Coop_push_record_model.php */
