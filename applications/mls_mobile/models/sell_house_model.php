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
load_m('House_base_model');

class Sell_house_model extends House_base_model
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
   * @access  public
   * @param   array $data_info 出售房源信息数组
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function add_sell_house_info($data_info)
  {
    $result = parent::add_info($data_info);
    return $result;
  }

  /**
   * 获取符合条件的房源需求信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
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
      'fitment', 'forward', 'price', 'buildarea', 'buildyear', 'pic', 'cooperate_reward', 'reward_type', 'commission_ratio');
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
    $comm = $this->get_data(array('form_name' => $this->browse_sell_mess_log, 'where' => $where, 'select' => array('count(*) as num')), 'db_city');
    return $comm[0]['num'];
  }


  /**
   * 获得浏览记录
   * @param array $where where字段
   * @return array 以客源浏览日志信息组成的多维数组
   */
  public function get_brower_log($where = array(), $offset = 0, $pagesize = 0, $order_by_array = array(), $group_by = '', $database = 'db_city')
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
    $this->dbselect('db_city');
    $sql = "";
    if (!empty($house_id)) {
      $sql = "SELECT COUNT(*) as group_num FROM ";
      $sql .= " (SELECT * FROM (`browse_sell_mess_log`) ";
      $sql .= " WHERE `house_id` = $house_id GROUP BY `broker_id`) AS NUM";
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
    $this->dbselect('db_city');
    $sql = "";
    if (!empty($house_id)) {
      $sql = " SELECT * FROM  `sell_house` WHERE id IN ($house_id) ";
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
  public function get_today_brower_log_num($house_id, $broker_id, $today_browertime)
  {
    $this->dbselect('db_city');
    $where_sql = '';
    if (!empty($house_id) && !empty($broker_id) && !empty($today_browertime)) {
      $where_sql = "SELECT count( * ) AS num FROM (";
      $where_sql .= "`" . $this->browse_sell_mess_log . "`";
      $where_sql .= ")";
      $where_sql .= " WHERE house_id =$house_id";
      $where_sql .= " AND broker_id =$broker_id";
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
}

/* End of file sell_house_model.php */
/* Location: ./applications/mls/models/sell_house_model.php */
