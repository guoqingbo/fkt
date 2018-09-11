<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Group_refresh_base_model");

class Group_refresh_model extends Group_refresh_base_model
{
  public function __construct()
  {
    parent::__construct();
  }

  //获取 执行队列 :wjy
  public function get_refresh_time($time)
  {
    $sql = 'select * from group_refresh_time where refreshtime<' . $time . ' ';
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  //删除 队列 :wjy
  public function delete_queue($ids)
  {
    if (is_array($ids)) {
      $id_arr = $ids;
    } else {
      $id_arr[0] = $ids;
    }
    $this->db_city->where_in('id', $id_arr);
    return $this->db_city->delete('group_refresh_time');
  }

}

//$bool = $this->db->where($where)->delete('users');
//$bool = $this->db->insert('users', $data);
//$bool = $this->db->where($where)->update('users', $data);
