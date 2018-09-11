<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * autocollect_nj controller CLASS
 *
 * 虚拟号码脚本类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          lalala
 */
class Virtual_phone extends My_Controller
{
    protected $pre_month_fee = 10;

    public function __construct()
    {
        parent::__construct();
    }

    //门店虚拟号码月租费到期续费
    public function monthly_fee_recharge()
    {
        $city = $this->input->get('city', true);
        $this->set_city($city);

        $this->load->model('call_phone_apply_model');
        $db_city = $this->call_phone_apply_model->get_db_city();
        //$endTime = time() - 3600 * 24;
        $time = time();

        //先处理减少号码的请求，再处理月租费到期的续费
        $reduceList = $db_city->from('call_phone_apply')->where("status = -1")->order_by('id', 'asc')->get()->result_array();
        if (!empty($reduceList)) {
            foreach ($reduceList as $v) {
                $reduceNum = $v['phone_num'];
                $list = $db_city->from('call_phone_apply')->where("status = 1 and end_time <= $time and company_id = " . $v['company_id'] . ' and agency_id = ' . $v['agency_id'])->order_by('end_time', 'asc')->get()->result_array();
                if (empty($list)) {//当天脚本运行时该门店没有到期的月租费申请
                    continue;
                } else {
                    $db_city->trans_start();

                    //注意：减少号码个数是次月生效，按到期时间的从近到远进行号码减少；若减少后的数据使用号码个数为0，则该记录删除
                    foreach ($list as $val) {
                        if ($val['phone_num'] <= $reduceNum) {
                            $db_city->where('id', $val['id'])->delete('call_phone_apply');
                            $reduceNum -= $val['phone_num'];
                            if (0 == $reduceNum) {
                                $db_city->where('id', $v['id'])->delete('call_phone_apply');
                                break;
                            }
                        } else {
                            $phone_num = $val['phone_num'] - $reduceNum;
                            $monthly_fee = $val['monthly_fee'] - $reduceNum * $this->pre_month_fee;
                            $db_city->where('id', $val['id'])->update('call_phone_apply', ['phone_num' => $phone_num, 'monthly_fee' => $monthly_fee, 'update_time' => $time]);
                            $db_city->where('id', $v['id'])->delete('call_phone_apply');
                            $reduceNum = 0;
                            break;
                        }
                    }
                    if ($reduceNum > 0) {//减去所有的月租到期申请后还有剩余的，留待下一次脚本执行，这里的业务逻辑要和产品确认
                        $db_city->where('id', $v['id'])->update('call_phone_apply', ['phone_num' => $reduceNum, 'update_time' => $time]);
                    }
                    $realReduceNum = $v['phone_num'] - $reduceNum;
                    $sql = 'update call_agency set phone_num = phone_num - ' . $realReduceNum . ', all_phone_num = all_phone_num - ' . $realReduceNum . ', monthly_fee = monthly_fee - ' . ($realReduceNum * $this->pre_month_fee) . ', update_time = ' . $time . ' where company_id = ' . $v['company_id'] . ' and agency_id = ' . $v['agency_id'];
                    $db_city->query($sql);

                    $db_city->trans_complete();
                }
            }
        }

        //月租费到期续费
        $list = $db_city->from('call_phone_apply')->where("status = 1 and end_time <= $time")->order_by('end_time', 'asc')->get()->result_array();
        if (!empty($list)) {
            foreach ($list as $v) {
                $agency = $db_city->where("company_id = " . $v['company_id'] . ' and agency_id = ' . $v['agency_id'])->get('call_agency')->row_array();
                $db_city->trans_start();

                if ($v['monthly_fee'] > $agency['balance']) {//门店余额不足，修改状态为停用，更新门店的月租费和使用号码总数
                    $db_city->where('id', $v['id'])->update('call_phone_apply', ['status' => 2, 'update_time' => $time]);

                    $sql = 'update call_agency set phone_num = phone_num - ' . $v['phone_num'] . ', update_time = ' . $time . ' where id = ' . $agency['id'];
                    $db_city->query($sql);
                } else {
                    $sql = 'update call_agency set balance = balance - ' . $v['monthly_fee'] . ', update_time = ' . $time . ' where id = ' . $agency['id'] . ' and balance >= ' . $v['monthly_fee'];
                    $db_city->query($sql);

                    $start_time = strtotime(date('Y-m-d'));
                    $end_time = strtotime("+30 days", strtotime(date('Y-m-d 23:59:59')));
                    $sql = 'update call_phone_apply set status = 1, start_time = ' . $start_time . ', end_time = ' . $end_time . ', update_time = ' . $time . ' where id = ' . $v['id'];
                    $db_city->query($sql);

                    $sql = "insert into call_fee(`apply_id`, `company_id`, `agency_id`, `broker_id`, `virtual_phone`, `type`, `phone_duration`, `fee`, `phone_start`, `create_time`) values (" . $v['id'] . ", " . $agency['company_id'] . ", " . $agency['agency_id'] . ", 0, '', 1, 0, " . $v['monthly_fee'] . ", 0, " . $time . ")";
                    $db_city->query($sql);

                    $balance = [
                        'company_id' => $v['company_id'],
                        'agency_id' => $v['agency_id'],
                        'fee' => '-' . $v['monthly_fee'],
                        'type' => 3,
                        'balance' => $agency['balance'] - $v['monthly_fee'],
                        'create_time' => $time
                    ];
                    $db_city->insert('call_agency_balance', $balance);
                }

                $db_city->trans_complete();
            }
        }
    }

    //门店日报表每日统计
    public function agency_day_statistics()
    {
        $city = $this->input->get('city', true);
        $this->set_city($city);

        $day = $this->input->get('day', true);
        if (empty($day)) {
            $day = date('Y-m-d', strtotime('-1 day'));
        }
        $create_time = time();
        $statis_time = $start_time = strtotime($day);
        $end_time = strtotime($day . ' 23:59:59');

        $this->load->model('call_phone_apply_model');
        $db_city = $this->call_phone_apply_model->get_db_city();

        $list = $db_city->from('call_agency')->order_by('id', 'asc')->get()->result_array();
        if (!empty($list)) {
            foreach ($list as $v) {
                $dayStatistics = $db_city->where("company_id = " . $v['company_id'] . ' and agency_id = ' . $v['agency_id'] . ' and statis_time = ' . $statis_time)->get('call_agency_statistics')->row_array();
                if (empty($dayStatistics)) {
                    $dayBalance = $db_city->where("company_id = " . $v['company_id'] . ' and agency_id = ' . $v['agency_id'] . ' and create_time <= ' . $end_time)->order_by('create_time', 'desc')->get('call_agency_balance')->row_array();
                    $balance = empty($dayBalance) ? '0.00' : $dayBalance['balance'];

                    $recharge_amount = $db_city->select('sum(fee) as fee')->where("company_id = " . $v['company_id'] . ' and agency_id = ' . $v['agency_id'] . ' and create_time >= ' . $start_time . ' and create_time <= ' . $end_time . ' and type in (1,2)')->get('call_agency_balance')->result_array();
                    $recharge_amount = empty($recharge_amount[0]['fee']) ? '0.00' : $recharge_amount[0]['fee'];

                    $consume_amount = $db_city->select('sum(fee) as fee')->where("company_id = " . $v['company_id'] . ' and agency_id = ' . $v['agency_id'] . ' and create_time >= ' . $start_time . ' and create_time <= ' . $end_time . ' and type in (3,4)')->get('call_agency_balance')->result_array();
                    $consume_amount = empty($consume_amount[0]['fee']) ? '0.00' : $consume_amount[0]['fee'];

                    $arr = [
                        'company_id' => $v['company_id'],
                        'agency_id' => $v['agency_id'],
                        'statis_time' => $statis_time,
                        'recharge_amount' => $recharge_amount,
                        'consume_amount' => $consume_amount,
                        'balance' => $balance,
                        'create_time' => $create_time,
                    ];
                    $db_city->insert('call_agency_statistics', $arr);
                }
            }
        }
    }
}
