<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * district_model CLASS
 *
 * 房友录入模型
 *
 * @package         mls
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Import_model extends MY_model
{

  public function __construct()
  {
    parent::__construct();

  }

  private $community = 'community';

  public function community_info($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->community, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }


  /**
   * 获取符合条件的房源需求信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的出售信息条数
   */
  public function get_count_by_cond($cond_where = '', $tbl = '')
  {
    $count_num = 0;
    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
      $this->dbback_city->distinct();
      $count_num = $this->dbback_city->count_all_results($tbl);
    }
    return intval($count_num);
  }
}

/* End of file district_model.php */
/* Location: ./applications/mls/models/district_model.php */
