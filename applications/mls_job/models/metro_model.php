<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
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
 * district_model CLASS
 *
 * 地铁线路数据模型类
 *
 * @package         mls
 * @subpackage      Models
 * @category        Models
 * @author          wang
 */
//load_m('Metro_base_model');
class Metro_model extends MY_Model
{
  /**
   * 地铁线路里表名称
   *
   * @access private
   * @var string
   */
  protected $_metro_line_tbl = 'metro_line';

  /**
   * 地铁站点表名称
   *
   * @access private
   * @var string
   */
  protected $_metro_site_tbl = 'metro_site';

  protected $_block_metro_tbl = 'block_metro';

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获得地铁站点
   */
  public function get_metro_site($where = array(), $database = 'dbback_city')
  {
    $data = $this->get_data(array('form_name' => $this->_metro_site_tbl, 'where' => $where, 'select' => array('id', 'metro_id', 'b_map_x', 'b_map_y')), $database);
    return $data;
  }

  public function set_block_metro($data)
  {
    return $this->add_data($data, 'db_city', $this->_block_metro_tbl, 1);
  }
  /*
  public function set_new_block_metro($data)
  {
      return $this->add_data($data, 'db_jjr','keeper_block_metro', 1);
  }
  */

  /**
   * 获得地铁站点
   */
  public function get_new_metro_site($where = array(), $database = 'dbback_city')
  {
    $data = $this->get_data(array('form_name' => $this->_metro_site_tbl, 'where' => $where, 'select' => array('id', 'metro_id', 'b_map_x', 'b_map_y')), $database);
    return $data;
  }


  /**
   * 通过lp_id和城市拼音查看是否有数据
   */
  /*
    public function get_one_metro($where=array(),$database='dbback_jjr')
    {
        $data =  $this->get_data(array('form_name' => 'keeper_block_metro','where'=>$where, 'select'=>array('id')),$database);
        return $data;
    }
    */
}

/* End of file district_model.php */
/* Location: ./applications/mls_admin/models/district_model.php */
