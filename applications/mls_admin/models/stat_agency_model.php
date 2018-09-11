<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls_job
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * @package         mls_job
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Stat_agency_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('agency_model');
    }

    //门店统计量

    public function stat_agency($where_arr)
    {
        $result = [];

        $stat_sell = $this->get_stat_sell($where_arr);
        $stat_rent = $this->get_stat_rent($where_arr);

        $stat_login = $this->get_login_num($where_arr);
        $stat_signing = $this->get_signing_data_by($where_arr);

        $sell_count = count($stat_sell);
        $rent_count = count($stat_sell);

        $login_count = count($stat_login);
        $siging_count = count($stat_signing);

        $result['tatal_sell'] = 0;
        $result['tatal_rent'] = 0;
        $result['tatal_login'] = 0;
        $result['tatal_sign'] = 0;
        //数据重构
        $list = array();
        foreach ($stat_login as $login_key => $login_val) {

            $list[$login_key] = $login_val;
            $list[$login_key]['sell_num'] = 0;
            $list[$login_key]['rent_num'] = 0;
            $list[$login_key]['signing_num'] = 0;

            foreach ($stat_sell as $sell_key => $sell_val) {
                if ($login_val['agency_id'] == $sell_val['agency_id']) {
                    $list[$login_key]['sell_num'] = $sell_val['sell_num'];
                }
            }

            foreach ($stat_rent as $rent_key => $rent_val) {
                if ($login_val['agency_id'] == $rent_val['agency_id']) {
                    $list[$login_key]['rent_num'] = $rent_val['rent_num'];
                }
            }

            foreach ($stat_signing as $signing_key => $signing_val) {
                if ($login_val['agency_id'] == $signing_val['agency_id']) {
                    $list[$login_key]['signing_num'] = $signing_val['signing_num'];
                }
            }

            $agency_info = $this->agency_model->get_by_id($login_val['agency_id']);
            $list[$login_key]['agency_addr'] = $agency_info['address'];

            $result['tatal_login'] += intval($list[$login_key]['login_num']);
            $result['tatal_sign'] += intval($list[$login_key]['signing_num']);
            $result['tatal_sell'] += intval($list[$login_key]['sell_num']);
            $result['tatal_rent'] += intval($list[$login_key]['rent_num']);

        }

        $result['list'] = $list;
        return $result;
    }

//以门店为单位统计出租出售房源量
    public function get_stat_sell($cond_arr)
    {
        $sell_sql = "select a.name as agency_name,s.agency_id ,count(s.agency_id) as sell_num 
FROM agency as a
INNER JOIN  sell_house as s 
on a.id = s.agency_id and s.createtime < {$cond_arr['end_time']} and s.createtime > {$cond_arr['start_time']} and s.agency_id > 0 and s.status !=5
group by a.id";


        $sell_data = $this->dbback_city->query($sell_sql)->result_array();


//        $sell_count = count($sell_data);
//        $rent_count = count($rent_data);

//        $house_data = array();
//        foreach ($sell_data as $sell_key => $sell_val) {
//            $house_data[$sell_key] = $sell_val;
//            $house_data[$sell_key]['rent_num'] = 0;
//            foreach ($rent_data as $rent_key => $rent_val) {
//                if ($rent_val['agency_id'] == $sell_val['agency_id']) {
//                    $house_data[$sell_key]['rent_num'] = $rent_val['rent_num'];
//                } else if ($rent_key == $rent_count - 1 && $house_data[$sell_count]['sell_num'] != 0) {
//                    $house_data[$sell_count]['sell_num'] = 0;
//                    $sell_count++;
//                }
//            }
//        }
        return $sell_data;

    }

    //以门店为单位统计出租出售房源量
    public function get_stat_rent($cond_arr)
    {

        $rent_sql = "select a.name as agency_name,r.agency_id ,count(r.agency_id) as rent_num 
FROM agency as a
INNER JOIN  rent_house as r 
on a.id = r.agency_id and r.createtime < {$cond_arr['end_time']} and r.createtime > {$cond_arr['start_time']} and r.agency_id > 0 and r.status !=5
group by a.id";

        $rent_data = $this->dbback_city->query($rent_sql)->result_array();

        return $rent_data;
    }

    //以门店为单位统计登陆量

    public function get_login_num($where_arr)
    {
        $sql = 'select count(*) as login_num,agency_id,agency_name from login_log where dateline > ' . $where_arr['start_time'] . ' and dateline < ' . $where_arr['end_time'] . ' and agency_id > 0  group by agency_id';
        $query = $this->dbback_city->query($sql);
        $result = $query->result_array();
        return $result;
    }

    //签约量统计
    public function get_signing_data_by($where_arr)
    {
        $sql = 'select count(*) as signing_num,agency_name_a as agency_name, agency_id_a as agency_id from bargain where signing_time > ' . $where_arr['start_time'] . ' and signing_time < ' . $where_arr['end_time'] . ' and agency_id_a > 0  group by agency_id_a';
        $query = $this->dbback_city->query($sql);
        $result = $query->result_array();
        return $result;
    }
}

/* End of file stat_login_model.php */
/* Location: ./application/mls_admin/models/stat_login_model.php */
