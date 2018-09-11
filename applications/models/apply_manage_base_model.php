<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Apply_manage_base_model extends MY_Model
{
  private $table = 'apply_manage';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function get_tbl()
  {
    return $this->table;
  }

  /**
   * 获取单条数据(app升级)
   */
  public function get_apply($data)
  {
    $arr_data = array();

    //获取表名称
    $tbl_name = $this->get_tbl();

    //获得需要查询的求购、求租信息字段
    $select_fields = array('apply_name', 'version', 'is_forced', 'apply_size', 'update_url', 'update_content');
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
      $this->dbback->select($select_fields_str);
    }

    $cond_where = array();
    if (!empty($data['type'])) {
      $cond_where['type'] = $data['type'];
    }
    if (!empty($data['version_type'])) {
      $cond_where['version_type'] = $data['version_type'];
    }

    //查询条件
    if (!empty($cond_where)) {
      $this->dbback->where($cond_where);
    }

    //排序条件
    $this->dbback->order_by('id', 'desc');

    $this->dbback->limit(1, 0);

    //查询
    $arr_data = $this->dbback->get($tbl_name)->row_array();

    return $arr_data;
  }
}
/* End of file apply_manage_base_model.php */
/* Location: ./fang100/models/apply_manage_base_model.php */
