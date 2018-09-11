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
 * Community_model CLASS
 *
 * 楼盘数据模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class floor_model extends MY_Model
{

  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';

  /**
   * 楼栋表名称
   * @var string
   */
  private $_cmt_tbl = 'cmt_floor';

  public function __construct()
  {
    parent::__construct();
    $this->mls_community = 'community';
    $this->cmt_floor = 'cmt_floor';
  }

  /**
   * 添加楼栋
   * @param array $paramlist 楼栋字段
   * @return insert_id or 0
   */
  function add_floor($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db_city', $this->cmt_floor);
    return $result;
  }

  /**
   * 根据楼栋ID获得楼栋详情
   * @param string $floorid 楼栋ID
   * @return array 以楼栋信息组成的多维数组
   */
  public function get_floor_by_id($floorid = '')
  {
    $where_cond = array('id' => $floorid);
    $floorData = $this->get_data(array('form_name' => $this->cmt_floor, 'where' => $where_cond), 'dbback_city');
    return $floorData[0];
  }

  /**
   * 筛选楼栋
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼栋信息组成的多维数组
   */
  public function get_floor($where = array(), $database = 'dbback_city')
  {
    $floor_data = $this->get_data(array('form_name' => $this->cmt_floor, 'where' => $where), $database);
    return $floor_data;
  }

  /**
   * 删除楼栋
   * @param string $floor_id 楼栋ID
   * @return 0 or 1
   */
  public function del_floor($floor_id = '')
  {
    $result = $this->del(array('id' => $floor_id), 'db_city', $this->cmt_floor);
    return $result;
  }


}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
