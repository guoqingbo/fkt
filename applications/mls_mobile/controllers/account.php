<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 统计控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Account extends MY_Controller
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

  public function bubble()
  {
    //采集数据
    $this->load->model('collections_model');//采集模型类
    $collect_sell = $this->collections_model->get_new_sell_num();
    $collect_rent = $this->collections_model->get_new_rent_num();
    $collect_total = $collect_sell + $collect_rent;
    $collect_remain = '不限';
    //新房分销
    //$this->load->model('newhouse_request_model');
    //$project = json_decode($this->newhouse_request_model->project(), true);
    //$newhouse_distri_total = count($project['data']);
    $newhouse_distri_remain = '不限';
    //合作中心
    $cond_where = "isshare = 1 AND status = 1";
    //加载客户MODEL
    $this->load->model('sell_house_model');
    //出售
    $coop_sell = $this->sell_house_model->get_count_by_cond("isshare = 1 AND status = 1");
    //出租
    $this->load->model('rent_house_model');
    $coop_rent = $this->rent_house_model->get_count_by_cond("isshare = 1 AND status = 1");
    //求购
    $this->load->model('buy_customer_model');
    $coop_buy_customer = $this->buy_customer_model->get_buynum_by_cond("is_share = 1 AND status = 1");
    //求租
    $this->load->model('rent_customer_model');
    $coop_rent_customer = $this->rent_customer_model->get_rentnum_by_cond("is_share = 1 AND status = 1");
    $coop_total = $coop_sell + $coop_rent + $coop_buy_customer + $coop_rent_customer;
    $coop_remain = '不限';
    //消息
    $cond_where = array('from' => 1, 'broker_id' => $this->user_arr['broker_id'], 'is_read' => 0);
    $this->load->model('message_model');//消息、公告模型类
    $unread = $this->message_model->get_count_by_cond($cond_where);
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $return_account = array(
      'collect' => array('total' => $collect_total, 'remain' => $collect_remain),
      'newhouse_distri' => array('total' => $newhouse_distri_total, 'remain' => $newhouse_distri_remain),
      'coop' => array('total' => $coop_total, 'remain' => $coop_remain),
      'message' => array('unread' => $unread),
      'open_cooperate' => $company_basic_data['open_cooperate']
    );
    $this->result(1, '查询成功', $return_account);
  }
}
/* End of file account.php */
/* Location: ./application/mls/controllers/account.php */
