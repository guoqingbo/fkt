<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Customer Controller CLASS
 *
 * 区属板块管理 控制器
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          xz
 */
class district_street extends MY_Controller
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 根据区属ID获取板块信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_streetinfo_by_distid()
  {
    //加载区属板块MODEL
    $this->load->model('district_model');

    //板块信息
    $streetinfo = array();
    $dist_id = $this->input->get('dist_id', TRUE);
    $dist_id = intval($dist_id);

    if ($dist_id > 0) {
      $streetinfo = $this->district_model->get_street_bydist($dist_id);
    }

    echo json_encode($streetinfo);
  }
}

/* End of file district_street.php */
/* Location: ./applications/mls/controllers/district_street.php */
