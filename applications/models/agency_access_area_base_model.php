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
 * Agency_access_area_base_model CLASS
 *
 * 门店访问数据范围类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Agency_access_area_base_model extends MY_Model
{

  /**
   * 中介表
   * @var string
   */
  private $_tbl = 'agency_access_area';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }
    $this->_select_fields = $select_fields_str;
  }

  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  /**
   * 获取访问范围
   * @param int $companyid 公司ID
   * @param int $agencyid 门店ID
   * @return array 返回可访问数据范围数组
   */
  public function get_agency_area($companyid, $agencyid)
  {
    //查询字段
    $this->dbback_city->select('id, area');
    //查询条件
    $this->dbback_city->where(array('company_id' => $companyid, 'agency_id' => $agencyid));
    //返回结果
    $agencyarea = $this->dbback_city->get($this->_tbl)->row_array();

    //自己必须存在
    if (is_full_array($agencyarea) && '' != $agencyarea['area']) {
      $agencyarea['area'] = $agencyid . ',' . $agencyarea['area'];
    } else {
      $agencyarea['area'] = $agencyid;
    }

    return $agencyarea;
  }

  /**
   * 初始化门店访问范围
   * @param int $companyid 公司ID
   * @param int $agencyid 门店ID
   * @param array $area 范围
   * @return array 返回多条公司记录组成的二维数组
   */
  public function init_agency_area($companyid, $agencyid, $area)
  {
    $areastr = is_full_array($area) ? implode(',', $area) : '';
    $data = array(
      'company_id' => $companyid,
      'agency_id' => $agencyid,
      'area' => $areastr,
    );

    return $this->replace($data);
  }

  /**
   * 更新门店访问范围
   * @param int $accessareaid 范围ID
   * @param int $companyid 公司ID
   * @param int $agencyid 门店ID
   * @param array $area 范围
   * @return array 更新结果
   */
  public function update_agency_area($accessareaid, $companyid, $agencyid, $area)
  {
    $this->db_city->where('id', $accessareaid);
    $this->db_city->where('company_id', $companyid);
    $this->db_city->where('agency_id', $agencyid);
    $this->db_city->update($this->_tbl, $area);
    return $this->db_city->affected_rows();
  }

  /**
   * 插入数据
   * @param array $data 插入数据源数组
   * @return int 成功 返回插入成功后的id 失败 false
   */
  public function replace($data)
  {
    return $this->db_city->replace($this->_tbl, $data);
  }
}

/* End of file agency_base_model.php */
/* Location: ./applications/models/agency_base_model.php */
