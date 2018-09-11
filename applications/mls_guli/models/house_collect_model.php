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


/**
 * Customer_collect CLASS
 *
 * 出售、出租信息收藏
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */

//加载父类文件
load_m('House_base_model');

class House_collect_model extends House_base_model
{

  /**
   * 收藏表名称
   *
   * @access private
   * @var string
   */
  private $_collect_tbl = '';

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();

    //设置收藏表名称
    $this->set_collect_tbl('house_collect');
  }


  /**
   * 设置收藏信息表名称
   *
   * @access    public
   * @param    string $tbl_name 表名称
   * @return    void
   */
  public function set_collect_tbl($tbl_name)
  {
    $this->_collect_tbl = strip_tags($tbl_name);
  }


  /**
   * 获取收藏信息表名称
   *
   * @access    public
   * @param    void
   * @return    string 出售、出租信息表名称
   */
  public function get_collect_tbl()
  {
    return $this->_collect_tbl;
  }


  /**
   * 添加收藏记录
   *
   * @access    public
   * @param   array $data_info 收藏记录
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function add_collection($data_info)
  {
    $msg = TRUE;
    $collect_tbl = $this->get_collect_tbl();

    if ($collect_tbl != '' && !empty($data_info)) {
      $this->db_city->insert($collect_tbl, $data_info);
      $msg = ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
    }

    return $msg;
  }


  /**
   * 取消收藏记录
   *
   * @access    public
   * @param  int $c_id 收藏记录id
   * @param  int $signatory_id 经纪人编号
   * @param  int $type 收藏记录类型
   * @return    int 取消条数
   */
  public function cancel_collection_by_id($c_id, $signatory_id, $type = 'sell_house')
  {
    $up_num = 0;

    $collect_tbl = $this->get_collect_tbl();
    $c_id = intval($c_id);
    $signatory_id = intval($signatory_id);
    $type = strip_tags($type);
    $update_arr = array();

    if ($collect_tbl != '' && $c_id > 0) {
      $update_arr['status'] = 2;
      $this->db_city->where('id', $c_id);
      $this->db_city->where('signatory_id', $signatory_id);
      $this->db_city->where('tbl', $type);
      $this->db_city->update($collect_tbl, $update_arr);
      $up_num = $this->db_city->affected_rows();
    }

    return $up_num;
  }

  /**
   * 取消(删除)收藏记录
   *
   * @access    public
   * @param  int $c_id 收藏记录id
   * @param  int $signatory_id 经纪人编号
   * @param  int $type 收藏记录类型
   * @return    int 删除条数
   */
  public function del_collection_by_id($c_id, $signatory_id, $type = 'sell_house')
  {
    $up_num = 0;
    $collect_tbl = $this->get_collect_tbl();
    $c_id = intval($c_id);
    $signatory_id = intval($signatory_id);
    $type = strip_tags($type);
    if ($collect_tbl != '' && $c_id > 0) {
      $this->db_city->where('rows_id', $c_id);
      $this->db_city->where('signatory_id', $signatory_id);
      $this->db_city->where('tbl', $type);
      $this->db_city->delete($collect_tbl);

      $up_num = $this->db_city->affected_rows();

    }

    return $up_num;
  }

  /**
   * 根据经纪人编号，获取已经收藏的房源信息编号
   *
   * @access    public
   * @param  array $data_info 收藏记录
   * @return    array 收藏记录
   */
  public function get_collect_ids_by_bid($signatory_id, $type = 'sell_house', $status = 1)
  {
    $signatory_id = intval($signatory_id);
    $type = strip_tags($type);
    $status = intval($status);
    $arr_ids = array();

    if ($signatory_id > 0 && $type != '') {
      $collect_tbl = $this->get_collect_tbl();
      $this->dbback_city->select('rows_id');
      $cond_where = "signatory_id = '" . $signatory_id . "' AND tbl = '" . $type . "'";
      $cond_where .= $status != 0 ? " AND status = '" . $status . "'" : "";
      $this->dbback_city->where($cond_where);
      $arr_ids = $this->dbback_city->get($collect_tbl)->result_array();
    }

    return $arr_ids;
  }


  /**
   * 根据房源编号和经纪人编号获取收藏个数
   *
   * @access    public
   * @param   array $data_info 收藏记录
   * @return    int 收藏个数
   */
  public function get_collectionnum_by_cid($rows_id, $signatory_id, $type = 'sell_house', $status = '')
  {
    $rows_id = intval($rows_id);
    $signatory_id = intval($signatory_id);
    $type = strip_tags($type);
    $status = intval($status);

    $cond_where = "rows_id = '" . $rows_id . "' AND signatory_id = '" . $signatory_id . "' AND tbl = '" . $type . "'";
    $cond_where .= $status != 0 ? " AND status = '" . $status . "'" : "";
    $collect_num = $this->_get_collectionnum_by_cond($cond_where);

    return $collect_num;
  }


  /**
   * 根据经纪人编号获取收藏房源个数
   *
   * @access    public
   * @param  array $data_info 收藏记录
   * @return    int 收藏个数
   */
  public function get_collectionnum_by_bid($signatory_id, $type = 'sell_house', $status = 1)
  {
    $signatory_id = intval($signatory_id);
    $type = strip_tags($type);
    $status = intval($status);

    $cond_where = "signatory_id = '" . $signatory_id . "' AND tbl = '" . $type . "'";
    $cond_where .= $status != 0 ? " AND status = '" . $status . "'" : "";
    $collect_num = $this->_get_collectionnum_by_cond($cond_where);

    return $collect_num;
  }


  /**
   * 根据获取收藏数据
   *
   * @access    public
   * @param  string $cond_where 查询条件
   * @param  array $type 收藏房源类型
   * @param  int $offset 读取数据偏移量
   * @param  int $limit 每次读取个数
   * @param  string $order_key 排序字段
   * @param  string $order_by 升序/降序字段
   * @return    array 收藏记录数组
   */
  public function get_collection_list_by_cond($cond_where, $type = 'sell_house', $offset = 0, $limit = 10,
                                              $order_key = 'creattime', $order_by = 'DESC')
  {
    $collect_list = array();

    //房源需求信息表
    $collet_tbl = $this->get_collect_tbl();
    if ($type == 'sell_house') {
      $this->load->model('sell_house_model');
      $house_tbl = $this->sell_house_model->get_tbl();
    } else if ($type == 'rent_house') {
      $this->load->model('rent_house_model');
      $house_tbl = $this->rent_house_model->get_tbl();
    } else {
      return $collect_list;
    }

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
      $this->dbback_city->select($select_fields);
    } else {
      $this->dbback_city->select("$collet_tbl.id AS c_id , $collet_tbl.* , $house_tbl.*");
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //排序条件
    $this->dbback_city->order_by($collet_tbl . '.' . $order_key, $order_by);
    $this->dbback_city->from($collet_tbl);
    $this->dbback_city->join($house_tbl, "$collet_tbl.rows_id =  $house_tbl.id");
    $this->dbback_city->limit($limit, $offset);
    $arr_data = $this->dbback_city->get()->result_array();

    return $arr_data;
  }

  /**
   * 根据获取收藏数据(模糊查询功能)
   *
   * @access    public
   * @param  string $cond_where 查询条件
   * @param  array $type 收藏房源类型
   * @param  int $offset 读取数据偏移量
   * @param  int $limit 每次读取个数
   * @param  string $order_key 排序字段
   * @param  string $order_by 升序/降序字段
   * @return    array 收藏记录数组
   */
  public function get_collection_list_by_cond_or_like($cond_where, $cond_or_like = array(), $type = 'sell_house', $offset = 0, $limit = 10,
                                                      $order_key = 'creattime', $order_by = 'DESC')
  {
    $collect_list = array();

    //房源需求信息表
    $collet_tbl = $this->get_collect_tbl();
    if ($type == 'sell_house') {
      $this->load->model('sell_house_model');
      $house_tbl = $this->sell_house_model->get_tbl();
    } else if ($type == 'rent_house') {
      $this->load->model('rent_house_model');
      $house_tbl = $this->rent_house_model->get_tbl();
    } else {
      return $collect_list;
    }

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
      $this->dbback_city->select($select_fields);
    } else {
      $this->dbback_city->select("$collet_tbl.id AS c_id , $collet_tbl.* , $house_tbl.*");
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    if (isset($cond_or_like) AND !empty($cond_or_like)) {
      if (isset($cond_or_like['like_key']) AND !empty($cond_or_like['like_key'])) {//多个or条件
        foreach ($cond_or_like['like_key'] as $v) {
          $this->dbback_city->or_like(array($v => $cond_or_like['like_value']));
        }
        //特殊干预=>多个or_like加括号
        if (is_full_array($this->dbback_city->ar_like)) {
          $this->dbback_city->ar_like = array(sprintf("( %s )", implode(' ', $this->dbback_city->ar_like)));
        }
      } else {//单个or条件
        $this->dbback_city->or_like($cond_or_like);
      }
    }

    //排序条件
    $this->dbback_city->order_by($collet_tbl . '.' . $order_key, $order_by);
    $this->dbback_city->from($collet_tbl);
    $this->dbback_city->join($house_tbl, "$collet_tbl.rows_id =  $house_tbl.id");
    $this->dbback_city->limit($limit, $offset);
    $arr_data = $this->dbback_city->get()->result_array();

    return $arr_data;
  }


  /**
   * 根据获取收藏数据数量
   *
   * @access    public
   * @param  string $cond_where 查询条件
   * @param  array $type 收藏房源类型
   * @return    int 数组数量
   */
  public function get_collection_num_by_cond($cond_where, $type = 'sell_house', $cond_or_like = array())
  {
    $collect_num = 0;

    //房源需求信息表
    $collet_tbl = $this->get_collect_tbl();

    if ($type == 'sell_house') {
      $this->load->model('sell_house_model');
      $house_tbl = $this->sell_house_model->get_tbl();
    } else if ($type == 'rent_house') {
      $this->load->model('rent_house_model');
      $house_tbl = $this->rent_house_model->get_tbl();
    } else {
      return $collect_num;
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    if (isset($cond_or_like) AND !empty($cond_or_like)) {
      if (isset($cond_or_like['like_key']) AND !empty($cond_or_like['like_key'])) {//多个or条件
        foreach ($cond_or_like['like_key'] as $v) {
          $this->dbback_city->or_like(array($v => $cond_or_like['like_value']));
        }
        //特殊干预=>多个or_like加括号
        if (is_full_array($this->dbback_city->ar_like)) {
          $this->dbback_city->ar_like = array(sprintf("( %s )", implode(' ', $this->dbback_city->ar_like)));
        }
      } else {//单个or条件
        $this->dbback_city->or_like($cond_or_like);
      }
    }
    $this->dbback_city->from($collet_tbl);
    $this->dbback_city->join($house_tbl, "$collet_tbl.rows_id =  $house_tbl.id");
    $collect_num = $this->dbback_city->count_all_results();

    return $collect_num;
  }


  /**
   * 根据条件获取收藏记录条数
   *
   * @access    public
   * @param   array $data_info 收藏记录
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  protected function _get_collectionnum_by_cond($cond_where)
  {
    $count_num = 0;

    //房源需求信息表
    $collect_tbl = $this->get_collect_tbl();

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
      $count_num = $this->dbback_city->count_all_results($collect_tbl);
    }

    return intval($count_num);
  }
  /** 根据房源id查看是否已经收藏*/


}

/* End of file house_collect_model.php */
/* Location: ./applications/mls_guli/models/house_collect_model.php */
