<?php

/**
 * 根据基本设置，房客源变更是否是公共数据（否-》是）
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      yuan
 */
class District_public_change extends MY_Controller
{
    /**
     * 解析函数
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 房源
     */
    public function sell_rent_house()
    {
        $city = $this->input->get('city', true);
        $this->set_city($city);
        $this->load->model('sell_house_model');
        $this->load->model('rent_house_model');
        if (!empty($city)) {
            $this->load->model('cooperate_district_model');
            $this->load->model('follow_model');
            //1.处理公盘内的的房源(采用分批处理法)
            $agency_arr = $this->cooperate_district_model->get_district_agency();
            if (is_array($agency_arr)) {
                foreach ($agency_arr as $k => $v) {
                    $agency_id = $v['agency_id'];
                    $house_public_time = 7;//七天无人跟进，区域公盘内该房源变为公共房源
                    if (!empty($agency_id) && !empty($house_public_time)) {
                        //当天的凌晨时间戳（凌晨执行该定时任务）
                        $today_time = strtotime(date('Y-m-d'));
                        $compare_day = intval($house_public_time);
                        $compare_time = $today_time - $compare_day * 24 * 3600;
                        //获得该门店下的所有出售房源，最后跟进时间超过设置时间的房源id
                        //出售房源
                        $where_cond = array(
                            'agency_id' => intval($agency_id),
                            'type' => 1
                        );
                        $sell_house_id_arr = array();
                        $sell_house_follow_data = $this->follow_model->get_id_order_by_date($where_cond);
                        //根据house_id去重，获得每个房源下的最近数据
                        $sell_house_follow_data_2 = array();
                        if (is_full_array($sell_house_follow_data)) {
                            foreach ($sell_house_follow_data as $key => $value) {
                                $this_house_id = $value['house_id'];
                                if (is_full_array($sell_house_follow_data_2)) {
                                    $is_push = true;
                                    foreach ($sell_house_follow_data_2 as $k => $v) {
                                        if ($v['house_id'] == $this_house_id) {
                                            $is_push = false;
                                        }
                                    }
                                    if ($is_push) {
                                        $sell_house_follow_data_2[] = $value;
                                    }
                                } else {
                                    $sell_house_follow_data_2[] = $value;
                                }
                            }
                        }

                        if (is_full_array($sell_house_follow_data_2)) {
                            foreach ($sell_house_follow_data_2 as $key => $value) {
                                $follow_time = strtotime($value['date']);
                                if ($follow_time < $compare_time) {
                                    $sell_house_id_arr[] = $value['house_id'];
                                }
                            }
                        }
                        //出租房源
                        $where_cond = array(
                            'agency_id' => intval($agency_id),
                            'type' => 2
                        );
                        $rent_house_id_arr = array();
                        $rent_house_follow_data = $this->follow_model->get_id_order_by_date($where_cond);

                        //根据house_id去重，获得每个房源下的最近数据
                        $rent_house_follow_data_2 = array();
                        if (is_full_array($rent_house_follow_data)) {
                            foreach ($rent_house_follow_data as $key => $value) {
                                $this_house_id = $value['house_id'];
                                if (is_full_array($rent_house_follow_data_2)) {
                                    $is_push = true;
                                    foreach ($rent_house_follow_data_2 as $k => $v) {
                                        if ($v['house_id'] == $this_house_id) {
                                            $is_push = false;
                                        }
                                    }
                                    if ($is_push) {
                                        $rent_house_follow_data_2[] = $value;
                                    }
                                } else {
                                    $rent_house_follow_data_2[] = $value;
                                }
                            }
                        }

                        if (is_full_array($rent_house_follow_data_2)) {
                            foreach ($rent_house_follow_data_2 as $key => $value) {
                                $follow_time = strtotime($value['date']);
                                if ($follow_time < $compare_time) {
                                    $rent_house_id_arr[] = $value['house_id'];
                                }
                            }
                        }

                        //出售
                        if (is_full_array($sell_house_id_arr)) {
                            $update_result = $this->sell_house_model->change_is_public_by_house_id(array_unique($sell_house_id_arr));
                            echo "出售房源 所属门店id:$agency_id ，" . " 影响行数：$update_result<br>";
                        } else {
                            echo "出售房源 所属门店id:$agency_id ，" . " 影响行数：0<br>";
                        }
                        //出租
                        if (is_full_array($rent_house_id_arr)) {
                            $update_result = $this->rent_house_model->change_is_public_by_house_id(array_unique($rent_house_id_arr));
                            echo "出租房源 所属门店id:$agency_id ，" . " 影响行数：$update_result<br>";
                        } else {
                            echo "出租房源 所属门店id:$agency_id ，" . " 影响行数：0<br>";
                        }
                    } else {
                        echo '尚未有相关门店设置房源变成没有归属人的公共房源选项<br>';
                    }
                }
            }
        }
    }
}
