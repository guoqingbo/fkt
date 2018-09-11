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
 * 出售房源信息管理类,提供增加、修改、删除、查询 出售房源信息的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */

//加载父类文件
load_m('Cooperation_base_model');

class Cooperation_model extends Cooperation_base_model
{

  /**
   * 表名
   *
   * @access private
   * @var string
   */
  private $_sell_house_tbl = 'sell_house';


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    //初始化表名称
    $this->set_tbl($this->_sell_house_tbl);
    $this->browse_sell_mess_log = 'browse_sell_mess_log';
  }


  /**
   * 添加出售信息
   *
   * @access    public
   * @param   array $data_info 出售房源信息数组
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function add_sell_house_info($data_info)
  {
    $result = parent::add_info($data_info);
    return $result;
  }

  public function change_department_id_by_borker_id($signatory_id, $department_id)
  {
    $signatory_id = intval($signatory_id);
    $department_id = intval($department_id);
    if ($signatory_id && $department_id) {
      $data = array();
      $data['department_id'] = $department_id;
      $cond_where = "signatory_id = '$signatory_id' and nature = 1 ";
      $result = parent::update_info_by_cond($data, $cond_where);
    }
  }

  /**
   * 获取符合条件的房源需求信息条数
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的信息条数
   */
  public function get_housenum_by_cond($cond_where)
  {
    $num = 0;
    $num = parent::get_count_by_cond($cond_where);
    return $num;
  }

  /**
   * 插入房源图片
   * @param type $pics
   * @param type $house_id
   * @param type $block_id
   */
  public function insert_house_pic($pics, $tbl, $house_id, $block_id, $deleteupload = 0)
  {
    //调用pic_model
    $this->load->model('pic_model');
    $return = array();
    $pictbl = 'upload';

    if (!$pics) {
      return $return;
    }
    $createtime = time();
    $pic_ids = '';

    if ($pics['p_filename2']) {
      foreach ($pics['p_filename2'] as $key => $val) {
        if (empty($pics['p_fileids2'][$key])) {
          //房源图片的参数
          $insert_data_house = array(
            'tbl' => $tbl,
            'type' => '1',
            'rowid' => $house_id,
            'url' => $val,
            'block_id' => $block_id,
            'createtime' => $createtime
          );
          $picid = $this->pic_model->insert_house_pic($insert_data_house, $pictbl);
          $pic_ids .= $picid . ',';
        } else {
          $pic_ids .= $pics['p_fileids2'][$key] . ',';
        }
      }
    }
    if ($pics['p_filename1']) {
      foreach ($pics['p_filename1'] as $key => $val) {
        if (empty($pics['p_fileids1'][$key])) {
          //房源图片的参数
          $insert_data_house = array(
            'tbl' => $tbl,
            'type' => '2',
            'rowid' => $house_id,
            'url' => $val,
            'block_id' => $block_id,
            'createtime' => $createtime
          );
          $picid = $this->pic_model->insert_house_pic($insert_data_house, $pictbl);
          $pic_ids .= $picid . ',';
        } else {
          $pic_ids .= $pics['p_fileids1'][$key] . ',';
        }
      }
    }
    $result = array(
      'pic_tbl' => $pictbl,
      'pic_ids' => $pic_ids
    );
    return $result;
  }


  /**
   * 获取合作房源必要信息
   * @param type $house_id
   */
  function get_hezuo_info($house_id)
  {
    $select_fields = array('id', 'block_name', 'block_id', 'district_id',
      'street_id', 'title', 'address', 'telno1', 'telno2', 'telno3', 'room', 'hall', 'toilet',
      'fitment', 'forward', 'price', 'buildarea', 'buildyear', 'pic');
    $this->set_search_fields($select_fields);
    $this->set_id($house_id);
    $house_detail = $this->get_info_by_id();

    $this->load->model('district_model');
    if (!empty($house_detail['district_id']) && intval($house_detail['district_id']) > 0) {
      $house_detail['district_name'] = $this->district_model->get_distname_by_id($house_detail['district_id']);
    } else {
      $house_detail['district_name'] = array();
    }

    if (!empty($house_detail['street_id']) && intval($house_detail['street_id']) > 0) {
      $house_detail['street_name'] = $this->district_model->get_streetname_by_id($house_detail['street_id']);
    } else {
      $house_detail['street_name'] = array();
    }

    $house_detail['photo'] = !empty($house_detail['pic']) ? $house_detail['pic'] : '';

    return $house_detail;
  }


  /**
   * 根据条件获得出售浏览记录数
   * @param array $where where字段
   * @return string 浏览记录数
   */
  public function get_brower_log_sell_num($where = array())
  {
    $comm = $this->get_data(array('form_name' => $this->browse_sell_mess_log, 'where' => $where, 'select' => array('count(*) as num')), 'dbback_city');
    return $comm[0]['num'];
  }


  /**
   * 获得浏览记录
   * @param array $where where字段
   * @return array 以客源浏览日志信息组成的多维数组
   */
  public function get_brower_log($where = array(), $offset = 0, $pagesize = 0, $order_by_array = array(), $group_by = '', $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->browse_sell_mess_log, 'group_by' => $group_by, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize, 'order_by_array' => $order_by_array), $database);
    return $comm;
  }

  /**
   * 根据条件获得浏览记录数分组总数
   * @param int $customer_id house_id字段
   * @return string 浏览记录数
   */
  public function get_brower_log_group_num($house_id)
  {
    $this->dbselect('dbback_city');
    $sql = "";
    if (!empty($house_id)) {
      $sql = "SELECT COUNT(*) as group_num FROM ";
      $sql .= " (SELECT * FROM (`browse_sell_mess_log`) ";
      $sql .= " WHERE `house_id` = $house_id GROUP BY `signatory_id`) AS NUM";
      $query = $this->db->query($sql);
      $result_arr = $query->result();
      $result = $result_arr[0]->group_num;
    }
    return $result;
  }

  /**
   * 根据多个house_id查询多条房源信息
   * @param  house_id字段
   * @return array
   */

  public function get_all_house($house_id)
  {
    $this->dbselect('dbback_city');
    $sql = "";
    if (!empty($house_id)) {
      $sql = " SELECT * FROM  `sell_house` WHERE id IN ($house_id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }

  /**
   * 根据多个id查询多条房源合作的信息
   * @param  id字段
   * @return array
   */

  public function get_all_isshare_by_ids($id)
  {
    $this->dbselect('dbback_city');

    if (!empty($id)) {
      $sql = " SELECT isshare FROM  `sell_house` WHERE id IN ($id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }


  /**
   * 根据条件获得当天浏览记录数
   * @param array $where where字段
   * @return string 浏览记录数
   */
  public function get_today_brower_log_num($house_id, $signatory_id, $today_browertime)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    if (!empty($house_id) && !empty($signatory_id) && !empty($today_browertime)) {
      $where_sql = "SELECT count( * ) AS num FROM (";
      $where_sql .= "`" . $this->browse_sell_mess_log . "`";
      $where_sql .= ")";
      $where_sql .= " WHERE house_id =$house_id";
      $where_sql .= " AND signatory_id =$signatory_id";
      $where_sql .= " AND browertime";
      $where_sql .= " BETWEEN $today_browertime[0]";
      $where_sql .= " AND $today_browertime[1]";
      $query = $this->db->query($where_sql);
      $result_arr = $query->result();
      $result = $result_arr[0]->num;
    }
    return $result;
  }

  /**
   * 添加一条浏览记录
   * @param array $paramlist 添加字段
   * @return insert_id or 0
   */
  function add($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db_city', $this->browse_sell_mess_log);
    return $result;
  }

  /**
   *
   * @param type $house_id
   * @param type $area_auth
   * @param type $frame_id
   * @return type
   */
  public function validate_func_area($house_id, $area_auth, $frame_id = '')
  {
    // $this->load->model('api_signatory_purview_model');
    $this->set_search_fields(array("id, signatory_id, department_id, company_id"));
    if (!strstr($house_id, ',')) {
      $this->set_id($house_id);
      $house_info = $this->get_info_by_id();
      return array('is_auth' => 1, 'result' => $house_id);
    } else {
      $new_str = '';
      $cond_where = "id IN (0," . $house_id . ")";
      $list = $this->get_list_by_cond($cond_where);
      if (is_full_array($list)) {
        foreach ($list as $v) {
          $new_str .= $v['id'] . ',';
        }
      }
      $new_str = trim($new_str, ',');

      if ($new_str == '') {
        return array('is_auth' => 0);
      } else {
        return array('is_auth' => 1, 'result' => $new_str);
      }
    }
  }

  //修改合作的方法 isshare 默认0是不合作 当为2的时候是审核状态
  function change_isshare_status($where, $data)
  {
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->_sell_house_tbl);
    return $result;
  }
//	//根据房客源id获取相关信息
//	public function get_info_by_id($id , $tbl )
//    {
//        $arr_data = array();
//		$this->db_city->select( 'signatory_id' );
//        $cond_where = "id = ".$id;
//        //查询条件
//        if( $cond_where != '')
//        {
//            $this->db_city->where( $cond_where );
//        }
//
//        //查询
//        $arr_data = $this->db_city->get( $tbl )->row_array();
//        return $arr_data;
//    }

}

/* End of file sell_house_model.php */
/* Location: ./applications/mls_guli/models/sell_house_model.php */
