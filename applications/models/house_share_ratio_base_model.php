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
 * House_share_ratio_base_model CLASS
 *
 * 出售出租合作房源佣金比例管理类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class House_share_ratio_base_model extends MY_Model
{

  /**
   * 佣金比例表
   *
   * @access private
   * @var string
   */
  private $_house_share_ratio_tbl = NULL;


  //构造
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 设置佣金表
   *
   * @access  public
   * @param  strting $tbl_name 佣金比例表名称
   * @return  void
   */
  public function set_ratio_tbl($tbl_name)
  {
    if (!empty($tbl_name)) {
      $this->_house_share_ratio_tbl = strip_tags($tbl_name);
    }
  }


  /**
   * 获取佣金比例表
   *
   * @access  public
   * @param  void
   * @return  string 获取佣金比例表名称
   */
  public function get_ratio_tbl()
  {
    return $this->_house_share_ratio_tbl;
  }


  /**
   * 添加佣金比例
   *
   * @access  public
   * @param  int $rowid 查询条件
   * @param  float $seller_ratio 买方所付佣金比例
   * @param  float $buyer_ratio 卖方付佣金比例
   * @param  float $a_ratio 甲方获取佣金比例
   * @param  float $b_ratio 乙方获取佣金比例
   * @return  boolean 是否添加成功
   */
  public function add_house_cooperate_ratio($rowid, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio)
  {
    $ret = FALSE;
    $rowid = intval($rowid);
    $tbl_name = $this->get_ratio_tbl();

    if ($tbl_name != NULL && $rowid > 0) {
      $data_info['rowid'] = $rowid;
      $data_info['buyer_ratio'] = floatval($buyer_ratio);
      $data_info['seller_ratio'] = floatval($seller_ratio);
      $data_info['a_ratio'] = floatval($a_ratio);
      $data_info['b_ratio'] = floatval($b_ratio);
      $this->db_city->insert($tbl_name, $data_info);

      $ret = ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
    }

    return $ret;
  }


  /**
   * 查询佣金比例
   *
   * @access  public
   * @return  array 出售出租信息
   */
  public function get_house_ratio_by_rowid($rowid)
  {
    $ratio_info = array();

    $rowid = intval($rowid);
    if ($rowid <= 0) {
      return $ratio_info;
    }

    $cond_where = "rowid = " . $rowid;
    $ratio_info = $this->get_info_by_cond($cond_where);

    return $ratio_info;
  }


  /**
   * 查询佣金比例个数
   *
   * @access  public
   * @return  int 个数
   */
  public function get_num_by_rowid($rowid)
  {
    $info_num = 0;

    $rowid = intval($rowid);
    if ($rowid <= 0) {
      return $ratio_info;
    }

    $cond_where = "rowid = " . $rowid;
    $info_num = $this->get_num_by_cond($cond_where);

    return $info_num;
  }


  /**
   * 更新佣金比例
   *
   * @access  public
   * @param int $rowid 房源编号
   * @param  float $seller_ratio 买方所付佣金比例
   * @param  float $buyer_ratio 卖方付佣金比例
   * @param  float $a_ratio 甲方获取佣金比例
   * @param  float $b_ratio 乙方获取佣金比例
   * @return  boolean 是否删除成功，TRUE-成功，FAlSE-失败。
   */
  public function update_house_ratio_by_rowid($rowid, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio)
  {
    $rowid = intval($rowid);

    $tbl_name = $this->get_ratio_tbl();

    if ($tbl_name == '') {
      return FALSE;
    }

    if ($rowid > 0) {
      $num = $this->get_num_by_rowid($rowid);

      if ($num > 0) {
        $this->db_city->set('buyer_ratio', floatval($buyer_ratio));
        $this->db_city->set('seller_ratio', floatval($seller_ratio));
        $this->db_city->set('a_ratio', floatval($a_ratio));
        $this->db_city->set('b_ratio', floatval($b_ratio));

        //设置条件
        $cond_where = "rowid = '" . $rowid . "' ";
        $this->db_city->where($cond_where);

        //更新数据
        $this->db_city->update($tbl_name);
      } else {
        $this->add_house_cooperate_ratio($rowid, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
      }

      $ret = ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
    } else {
      return FALSE;
    }
  }


  /**
   * 删除佣金比例
   *
   * @access  public
   * @param int $rowid 房源编号
   * @return  boolean 是否删除成功，TRUE-成功，FAlSE-失败。
   */
  public function del_house_ratio_by_rowid($rowid)
  {
    $tbl_name = $this->get_ratio_tbl();

    $rowid = intval($rowid);

    if ($rowid > 0) {
      //查询字段
      $cond_where = "rowid = " . $rowid;

      $this->db_city->where($cond_where);
      $this->db_city->delete($tbl_name);
    }

    return ($this->db_city->affected_rows() >= 1) ? TRUE : FALSE;
  }


  /**
   * 根据多个房源编号批量删除佣金比例信息
   *
   * @access  protected
   * @param  array $arr_ids 需求信息编号数组
   * @return  boolean 是否删除成功，TRUE-成功，FAlSE失败。
   */
  protected function delete_info_by_ids($arr_ids)
  {
    $tbl_name = $this->get_ratio_tbl();

    if (isset($arr_ids) && !empty($arr_ids)) {
      //查询字段
      $arr_ids_str = implode(',', $arr_ids);
      $cond_where = "rowid IN(" . $arr_ids_str . ")";

      $this->db_city->where($cond_where);
      $this->db_city->delete($tbl_name);
    }

    return ($this->db_city->affected_rows() >= 1) ? TRUE : FALSE;
  }


  /**
   * 根据条件获取佣金比例信息
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  array 佣金比例数据
   */
  protected function get_info_by_cond($cond_where)
  {
    $arr_data = array();

    //获取表名称
    $ratio_tbl = $this->get_ratio_tbl();

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //查询
    $arr_data = $this->dbback_city->get($ratio_tbl)->row_array();

    return $arr_data;
  }


  /**
   * 根据条件获取佣金比例信息个数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int 个数
   */
  protected function get_num_by_cond($cond_where)
  {
    $info_num = 0;

    //获取表名称
    $ratio_tbl = $this->get_ratio_tbl();

    //查询条件
    if ($cond_where != '') {
      $this->db_city->where($cond_where);
    }

    //查询
    $info_num = $this->db_city->count_all_results($ratio_tbl);

    return $info_num;
  }
}

/* End of file house_share_ratio_base_model.php */
/* Location: ./application/models/house_share_ratio_base_model.php */
