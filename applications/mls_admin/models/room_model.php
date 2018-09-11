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
class room_model extends MY_Model
{

  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';

  /**
   * 房间表名称
   * @var string
   */
  private $_cmt_tbl = 'cmt_room';

  public function __construct()
  {
    parent::__construct();
    $this->mls_community = 'community';
    $this->cmt_room = 'cmt_room';
  }

  /**
   * 添加房间
   * @param array $paramlist 房间字段
   * @return insert_id or 0
   */
  function add_room($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db_city', $this->cmt_room);
    return $result;
  }

  /**
   *  修改房间
   * @param array $paramlist 房间字段
   * @return insert_id or 0
   */
  function modify_room($room_id, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $room_id), $paramlist, 'db_city', $this->cmt_room);
    return $result;
  }

  /**
   * 根据房间ID获得房间详情
   * @param string $roomd 房间ID
   * @return array 以楼盘信息组成的多维数组
   */
  public function get_room_by_id($roomid = '')
  {
    $where_cond = array('id' => $roomid);
    $roomData = $this->get_data(array('form_name' => $this->cmt_room, 'where' => $where_cond), 'dbback_city');
    return $roomData[0];
  }

  /**
   * 筛选房间
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以房间信息组成的多维数组
   */
  public function get_room($where = array(), $database = 'dbback_city')
  {
    $room_data = $this->get_data(array('form_name' => $this->cmt_room, 'where' => $where), $database);
    return $room_data;
  }

  /**
   * 根据楼栋号删除房间
   * @param string $floor_id 楼栋ID
   * @return 0 or 1
   */
  public function del_room($floor_id = '')
  {
    $result = $this->del(array('floor_id' => $floor_id), 'db_city', $this->cmt_room);
    return $result;
  }


}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
