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
 * Project_cooperate_lol_effect_model CLASS
 *
 * 天下英雄共联盟
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Project_cooperate_lol_base_model");

class Project_cooperate_lol_model extends Project_cooperate_lol_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->mls_effect = 'project_cooperate_lol_effect';
  }

  /**
   * 筛选楼盘
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function geteffect($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'db')
  {
    $comm = $this->get_data(array('form_name' => $this->mls_effect, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize, 'order_by' => array('create_time', 'desc')), $database);
    return $comm;
  }
}

/* End of file Project_cooperate_lol_effect_model.php */
/* Location: ./app/models/Project_cooperate_lol_effect_model.php */
