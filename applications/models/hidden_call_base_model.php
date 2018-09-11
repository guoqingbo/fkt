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
 * Cooperate_base_model CLASS
 *
 * 房客源合作基类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Hidden_call_base_model extends MY_Model
{
    /**
     * 隐号拨打门店管理表
     * @var string
     */
    protected $tbl_call_agency = 'call_agency';
    /**
     * 隐号拨打申请数量管理表
     * @var string
     */
    protected $tbl_call_phone_apply = 'call_phone_apply';
    /**
     *  隐号拨打门店充值表
     * @var string
     */
    protected $tbl_call_agency_recharge = 'call_agency_recharge';
    /**
     *  隐号拨打门店消费表
     * @var string
     */
    protected $tbl_call_fee = 'call_fee';
    /**
     *  隐号拨打经纪人虚拟号业主号码绑定表
     * @var string
     */
    protected $tbl_call_phone_bind = 'call_phone_bind';
    /**
     *  隐号拨打虚拟号管理表
     * @var string
     */
    protected $tbl_call_phone = 'call_phone';
    /**
     *  隐号拨打日报表
     * @var string
     */
    protected $tbl_call_agency_statistics = 'call_agency_statistics';
    /**
     *  隐号拨打日报表
     * @var string
     */
    protected $tbl_call_agency_balance = 'call_agency_balance';

    /**
     *  虚拟号绑定时间（5分钟）
     * @var string
     */
    protected $bind_time = 5;

    /**
     *  三方接口用户名密码
     * @var string
     */
    protected $api_config = array(
//        'url' => 'http://59.110.46.218:6651/v2',
//        'userid' => '76000009',
//        'userpwd' => '512729407b68a6bb4336476be2415969',
    );

    /**
     * 类初始化
     */
    public function __construct()
    {
        parent::__construct();
        $this->api_config = $this->config->item('hidden_call');
        $this->bind_time = $this->api_config['bind_time'];
    }

    /**
     * 设置表名称
     *
     * @access  public
     * @param  string $tblname 表名称
     * @return  void
     */
    public function set_tbl($tblname)
    {
        $this->_tbl = trim(strip_tags($tblname));
    }

    /**
     * 获取表名称
     *
     * @access  public
     * @param  void
     * @return  string
     */
    public function get_tbl()
    {
        return $this->_tbl;
    }

    /**
     * 获取虚拟号绑定时间
     *
     * @access  public
     * @param  void
     * @return  string
     */
    public function get_bind_time()
    {
        return $this->bind_time;
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
     * 返回需要查询的字段
     * @param void
     * @return string 查询字段
     */
    public function get_search_fields()
    {
        return $this->_select_fields;
    }

    /**
     * 获取所有
     * @param void
     * @return string 查询字段
     */
    public function get_call_bind($where, $start = -1, $limit = 20,
                                  $order_key = 'id', $order_by = 'DESC')
    {
        $this->set_tbl($this->tbl_call_phone_bind);
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }
        if ($where) {
            //查询条件
            $this->dbback_city->where($where);
        }
        //排序条件
        $this->dbback_city->order_by($order_key, $order_by);
        if ($start >= 0 && $limit > 0) {
            $this->dbback_city->limit($limit, $start);
        }
        //返回结果
        return $this->dbback_city->get($this->_tbl)->result_array();
    }

    /**
     * 根据查询条件返回一条记录
     * @param string $where 查询条件
     * @return array 返回一条一维数组的记录
     */
    public function get_one_by($where = '')
    {
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }
        //查询条件
        $this->dbback_city->where($where);
        return $this->dbback_city->get($this->_tbl)->row_array();
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
     * 符合条件的行数
     * @param string $where 查询条件
     * @return int
     */
    public function bind_count_by($where = '')
    {
        $this->set_tbl($this->tbl_call_phone_bind);
        if ($where) {
            //查询条件
            $this->dbback_city->where($where);
        }
        return $this->dbback_city->count_all_results($this->_tbl);
    }


    /**
     * 查询门店是否开通隐号拨打
     * @param int $id 编号
     * @return boolean true 成功 false 失败
     */
    public function get_phone_num_by_agencyid($agency_id)
    {
        $this->set_tbl($this->tbl_call_agency);
        $this->set_select_fields(array('phone_num', 'balance'));
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }
        if ($agency_id > 0) {
            //查询条件
            $where = array('agency_id' => $agency_id);
            $this->dbback_city->where($where);
        }
        //返回结果
        return $this->dbback_city->get($this->_tbl)->row_array();
    }

    /**
     * 获取call_agency中的一条记录
     * @param int $id 编号
     * @return boolean true 成功 false 失败
     */
    public function get_one_call_agency($cond_where)
    {
        $this->set_tbl($this->tbl_call_agency);
        if ($cond_where) {
            $this->dbback_city->where($cond_where);
        }
        //返回结果
        return $this->dbback_city->get($this->_tbl)->row_array();
    }

    /**
     *
     * 查询门店正在使用的号码数量
     *
     */
    public function get_busyphone_by_agencyid($agency_id)
    {
        $this->set_tbl($this->tbl_call_phone);
        if ($agency_id > 0) {
            //查询条件
            $deadline = time() - $this->bind_time * 60;//绑定时间是否小于5分钟
            $where = "`agency_id` = $agency_id and (status = 2 or  (status=1 and bind_time>$deadline))";
            $this->dbback_city->where($where);
        }
        //返回结果
        return $this->dbback_city->count_all_results($this->_tbl);
    }

    /**
     *
     * 根据门店id获取25天前的月租单
     *
     */
    public function get_monthly_rent_by_agencyid($agency_id)
    {
        $this->set_tbl($this->tbl_call_phone_apply);
        if ($agency_id > 0) {
            //查询条件
            $deadline2 = strtotime(date('Y-m-d'));//当前凌晨的时间戳
            $deadline1 = $deadline2 + 5 * 24 * 60 * 60;//5天前
            $where = "`agency_id` = $agency_id and status = 1 and end_time<$deadline1 and end_time>$deadline2";
            $this->dbback_city->where($where);
        }
        //返回结果
        return $this->dbback_city->get($this->_tbl)->result_array();
    }

    /**
     *
     * 获得有效虚拟号
     *
     */
    public function get_effective_phone()
    {
        $this->set_tbl($this->tbl_call_phone);
        //查询条件
        $where = "status = 0";
        $this->dbback_city->where($where);
        $result = $this->dbback_city->get($this->_tbl)->result_array();//优先获取未使用的号码
        if (empty($result)) {//没有未使用的号码，则获取解除绑定的号码
            $deadline = time() - $this->bind_time * 60;//绑定时间是否大于5分钟
            $where = "status = 1 and bind_time<$deadline";
            $this->dbback_city->where($where);
            $result = $this->dbback_city->get($this->_tbl)->result_array();
        }
        $key = array_rand($result, 1);//随机获取一个有效的号码
        return $result[$key];
    }

    /**
     *
     * 插入绑定虚拟号
     *
     */
    public function insert_bind_phone($arr)
    {
        //插入数据
        $this->set_tbl($this->tbl_call_phone_bind);
        $insert_id = 0;
        if (is_array($arr) && !empty($arr)) {
            $this->db_city->insert($this->_tbl, $arr);
            //如果插入成功，则返回插入的id
            if (($this->db_city->affected_rows()) >= 1) {
                $insert_id = $this->db_city->insert_id();
            }
        }
        return $insert_id;
    }

    /**
     *
     * 插入余额变动
     *
     */
    public function insert_agency_balance($arr)
    {
        //插入数据
        $this->set_tbl($this->tbl_call_agency_balance);
        $insert_id = 0;
        if (is_array($arr) && !empty($arr)) {
            $this->db_city->insert($this->_tbl, $arr);
            //如果插入成功，则返回插入的id
            if (($this->db_city->affected_rows()) >= 1) {
                $insert_id = $this->db_city->insert_id();
            }
        }
        return $insert_id;
    }

    /**
     *
     * 插入话费变动
     *
     */
    public function insert_call_fee($arr)
    {
        //插入数据
        $this->set_tbl($this->tbl_call_fee);
        $insert_id = 0;
        if (is_array($arr) && !empty($arr)) {
            $this->db_city->insert($this->_tbl, $arr);
            //如果插入成功，则返回插入的id
            if (($this->db_city->affected_rows()) >= 1) {
                $insert_id = $this->db_city->insert_id();
            }
        }
        return $insert_id;
    }

    /**
     *
     * 获取绑定虚拟号
     *
     */
    public function get_one_call_phone($cond_where = "")
    {
        $this->set_tbl($this->tbl_call_phone);
        if ($cond_where) {
            //查询条件
            $this->dbback_city->where($cond_where);
        }
        //返回结果
        return $this->dbback_city->get($this->_tbl)->row_array();
    }

    /**
     *
     * 获取call_phone_bind表虚拟号绑定关系
     *
     */
    public function get_one_call_bind_phone($cond_where = "")
    {
        $this->set_tbl($this->tbl_call_phone_bind);
        if ($cond_where) {
            //查询条件
            $this->dbback_city->where($cond_where);
        }
        //返回结果
        return $this->dbback_city->get($this->_tbl)->row_array();
    }

    /**
     *
     * 更号码绑定表
     *
     */
    public function update_call_phone_bind($where, $update_data)
    {
        //更改虚拟号状态
        $this->set_tbl($this->tbl_call_phone_bind);
        if ($where) {
            $this->db_city->where($where);
        }
        $this->db_city->update($this->_tbl, $update_data);
        return $this->db_city->affected_rows();
    }
    /**
     *
     * 更新虚拟号状态
     *
     */
    public function update_bind_phone($where, $update_data)
    {
        //更改虚拟号状态
        $this->set_tbl($this->tbl_call_phone);
        $this->db_city->where($where);
        $this->db_city->update($this->_tbl, $update_data);
        return $this->db_city->affected_rows();
    }

    /**
     *
     * 更新门店号码
     *
     */
    public function update_call_agency($agency_id, $update_data)
    {
        //更改虚拟号状态
        $this->set_tbl($this->tbl_call_agency);
        $this->db_city->where('agency_id', $agency_id);
        $this->db_city->update($this->_tbl, $update_data);
        return $this->db_city->affected_rows();
    }

    /**
     *
     * 更号码申请表
     *
     */
    public function update_call_phone_apply($where, $update_data)
    {
        //更改虚拟号状态
        $this->set_tbl($this->tbl_call_phone_apply);
        if ($where) {
            $this->db_city->where($where);
        }
        $this->db_city->update($this->_tbl, $update_data);
        return $this->db_city->affected_rows();
    }
    /**
     *
     * 调用第三方绑定虚拟号码
     *
     */
    public function vpost_bindnumber($param, $cookie = '')
    {
        $url = $this->api_config['url'] . "/bindnumber";
        ///userid/76000009/userpwd/512729407b68a6bb4336476be2415969/bindid/XXXX/src/XXXX/dst/XXXX/srcclid/95013345604444";
        $post_fields = array(
            'userid' => $this->api_config['userid'],
            'userpwd' => $this->api_config['userpwd'],
            'bindid' => $param['broker_id'] . "-" . $param['agency_id'] . "-" . time(),
            'dst' => $param['owner_phone'],
            'srcclid' => $param['virtual_phone'],
            'credit' => $param['credit'],
            'timeout' => $this->bind_time,
        );
        $curl = curl_init(); // 启动一个CURL会话
        //  curl_setopt($curl, CURLOPT_PROXY, PROXY_URL);//设置代理服务器
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
        //curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);
        }
        curl_close($curl); // 关键CURL会话
        return json_decode($tmpInfo, true); // 返回数据
    }

    /**
     *
     * 调用第三方解除虚拟号码
     *
     */
    public function vpost_unbindnumber($param, $cookie = '')
    {
        $url = $this->api_config['url'] . "/unbindnumber";
        $post_fields = array(
            'userid' => $this->api_config['userid'],
            'userpwd' => $this->api_config['userpwd'],
            'bindid' => $param['bindid'],
        );
        $curl = curl_init(); // 启动一个CURL会话
        //  curl_setopt($curl, CURLOPT_PROXY, PROXY_URL);//设置代理服务器
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
        //curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);
        }
        curl_close($curl); // 关键CURL会话
        return json_decode($tmpInfo, true); // 返回数据
    }

    /**
     *
     * 通话结束回掉
     *
     */
    public function call_end($ticket)
    {
        $result = array();
        $virtual_phone = $ticket['dh'];
//        $stime = strtotime($ticket['stime']);//拨打虚拟号时间
//        $mintime = strtotime($ticket['stime']) - $this->bind_time * 60;//拨打虚拟号时间减去过期时间
        $bindid = $ticket['requestid'];
        $update_bind_data = array(
            'end_ticket' => json_encode($ticket),
        );
        $unbind_result = $this->update_call_phone_bind("bindid = '{$bindid}'", $update_bind_data);//更新绑定关系表
        $call_phone = $this->get_one_call_bind_phone("bindid = '{$bindid}'");//获取当前通话虚拟号的绑定关系
        if (!empty($call_phone)) {
            $call_agency_info = $this->get_one_call_agency("agency_id = {$call_phone['agency_id']}");//获取门店余额
            $fee = ceil($ticket['duration_time'] / 60) * 0.08;
            $balance = $call_agency_info['balance'] - $fee;
            //判断通话结束时余额是否小于0，小于0，则停用隐号拨打
            $update_call_agency = array();
            if ($balance <= 0) {
                $update_call_agency['phone_num'] = 0;
                //变更申请表中的号码状态为停用
                $where = "agency_id = {$call_phone['agency_id']} and  status = 1 ";
                $update_call_phone_apply = array(
                    'status' => 2,
                );
                $this->update_call_phone_apply($where, $update_call_phone_apply);
            }
            $update_call_agency['balance'] = $balance;
            $update_num = $this->update_call_agency($call_phone['agency_id'], $update_call_agency);//更新门店余额

            if ($update_num > 0) {
                //插入余额变动
                $call_agency_balance = array(
                    'company_id' => $call_phone['company_id'],
                    'agency_id' => $call_phone['agency_id'],
                    'fee' => -$fee,
                    'type' => 4,
                    'balance' => $balance,
                    'create_time' => time()
                );
                $insert_agency_balance_num = $this->insert_agency_balance($call_agency_balance);
                if ($insert_agency_balance_num > 0) {
                    //插入话费变动
                    $call_fee = array(
                        'company_id' => $call_phone['company_id'],
                        'agency_id' => $call_phone['agency_id'],
                        'broker_id' => $call_phone['broker_id'],
                        'virtual_phone' => $virtual_phone,
                        'broker_phone' => $call_phone['broker_phone'],
                        'house_phone' => $ticket['callee'],
                        'type' => 2,
                        'phone_duration' => $ticket['duration_time'],
                        'fee' => $fee,
                        'phone_start' => strtotime($ticket['atime']),
                        'ticket' => json_encode($ticket),
                        'create_time' => time()
                    );
                    $insert_fee_num = $this->insert_call_fee($call_fee);
                    if ($insert_fee_num > 0) {
                        //更改虚拟号状态
                        $update_data = array(
                            'status' => 0,//0.未使用1.绑定2.通话中
                            'bind_time' => "",
                            'broker_id' => "",
                            'agency_id' => "",
                            'company_id' => "",
                            'bindid' => ""
                        );
                        if (!empty($call_phone['bindid'])) {
                            $update_bind_phone_num = $this->update_bind_phone("bindid = '{$call_phone['bindid']}'", $update_data);
                        }
                        //调用三方接口接触绑定
                        $unbind_result = $this->vpost_unbindnumber(array('bindid' => $call_phone['bindid']));
                        $result['status'] = "success";
                    } else {
                        $result['msg'] = "Call cost record insertion failed ";
                        $result['status'] = "fail";
                    }
                } else {
                    $result['msg'] = "Insertion failure of store balance change record ";
                    $result['status'] = "fail";
                }

            } else {
                $result['msg'] = "Store balance change failed ";
                $result['status'] = "fail";
            }


        } else {
            $result['msg'] = "未获绑定虚拟号";
            $result['status'] = "fail";
        }
        return $result;
    }

    public function call_start($ticket)
    {
        $result = array();
        $virtual_phone = $ticket['dh'];
        $bindid = $ticket['requestid'];
        $update_bind_data = array(
            'start_ticket' => json_encode($ticket),
        );
        $unbind_result = $this->update_call_phone_bind("bindid = '{$bindid}'", $update_bind_data);//更新绑定关系表
        if ($virtual_phone) {
            //更改虚拟号状态
            $update_data = array(
                'status' => 2,//0.未使用1.绑定2.通话中
                'bind_time' => "",
                'broker_id' => "",
                'agency_id' => "",
                'company_id' => "",
                'bindid' => ""
            );
            $update_bind_phone_num = $this->update_bind_phone("virtual_phone = '{$virtual_phone}'", $update_data);
            $result['status'] = "success";
        } else {
            $result['msg'] = "virtual phone not exist";
            $result['status'] = "fail";
        }
        return $result;
    }
}

/* End of file cooperate_base_model.php */
/* Location: ./application/models/cooperate_base_model.php */
