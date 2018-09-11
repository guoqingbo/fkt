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
 * Stat_publish_model CLASS
 *
 * 统计房源发布数量
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
class Stat_publish_model extends MY_Model
{
  private $count_tbl = 'stat_publish';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function get_data_by_cond($where, $start = 0, $limit = 20,
                                   $order_key = 'id', $order_by = 'DESC')
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from($this->count_tbl);
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  public function count_data_by_cond($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from($this->count_tbl);
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

    //以门店为单位统计出租出售房源量
    public function get_stat_house($cond_arr)
    {
        $sell_sql = "select a.name,s.agency_id ,count(s.agency_id) as sell_num 
FROM agency as a
INNER JOIN  sell_house as s on a.id = s.agency_id and s.createtime < {$cond_arr['end_time']} and s.createtime > {$cond_arr['start_time']} and s.status != 5
group by a.id";

        $rent_sql = "select a.name,r.agency_id ,count(r.agency_id) as rent_num 
FROM agency as a
INNER JOIN  rent_house as r on a.id = r.agency_id and r.createtime < {$cond_arr['end_time']} and r.createtime > {$cond_arr['start_time']} and r.status != 5
group by a.id";
        $sell_data = $this->dbback_city->query($sell_sql)->result_array();
        $rent_data = $this->dbback_city->query($rent_sql)->result_array();
        $sell_count = count($sell_data);
        $rent_count = count($rent_data);
        $house_data = array();
        foreach ($sell_data as $sell_key => $sell_val) {
            $house_data[$sell_key] = $sell_val;
            $house_data[$sell_key]['rent_num'] = 0;
            foreach ($rent_data as $rent_key => $rent_val) {
                if ($rent_val['agency_id'] == $sell_val['agency_id']) {
                    $house_data[$sell_key]['rent_num'] = $rent_val['rent_num'];
                } else if ($rent_key == $rent_count - 1 && $house_data[$sell_count]['sell_num'] != 0) {
                    $house_data[$sell_count] = $rent_val;
                    $house_data[$sell_count]['sell_num'] = 0;
                    $sell_count++;
                }
            }
        }
        return $house_data;

    }

}

/* End of file stat_group_publish_model.php */
/* Location: ./applications/mls_job/models/stat_group_publish_model.php */
