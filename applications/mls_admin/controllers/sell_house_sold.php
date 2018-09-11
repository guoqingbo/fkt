<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 出售成交房源
 *
 * @package     mls_admin
 * @subpackage  Controllers
 * @category    Controllers
 * @author      angel_in_us
 */
class Sell_house_sold extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('cooperate_model');
    $this->load->model('cooperation_model');
    $this->load->helper('user_helper');
  }

  /**
   * 出售成交房源列表
   * date   : 2015-01-26
   * author : angel_in_us
   */
  public function index()
  {
    $data['title'] = '二手房成交列表';
    $data['conf_where'] = 'index';

    //筛选条件
    $where = " tbl = 'sell' ";

    if (@$_POST['angela_wen'] == 'angel_in_us') {
      $house_id = intval($this->input->post('house_id'));
      if ($house_id != "") {
        $where .= " and rowid = " . $house_id;
      }
      $order_sn = $this->input->post('order_sn');
      if ($order_sn != "") {
        $where .= " and order_sn = '" . $order_sn . "'";
      }
      $agent_name = $this->input->post('agent_name');
      if ($agent_name != "") {
        $where .= " and broker_name_a = '" . $agent_name . "'";
      }
      $agent_id = intval($this->input->post('agent_id'));
      if ($agent_id != "") {
        $where .= " and brokerid_a=" . $agent_id;
      }
    }
    //分页开始
    $data['sold_num'] = $this->cooperate_model->get_transaction_records_num_by_cond($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['sold_list'] = $this->cooperate_model->get_transaction_records_by_cond($where, $data['offset'], $data['pagesize']);

    //如果查询到成交数据
    if (count($data['sold_list']) >= 1) {
      //把合作 id 放在一个数组里，去查询 cooperate_attached 表里关联的房源信息
      $cooperation_ids = array();
      foreach ($data['sold_list'] as $key => $val) {
        $cooperation_ids[] = $val['id'];
      }
      $where_in = array('id', $cooperation_ids);
      $house_info = $this->cooperation_model->get_house_info_byids($where_in);
      foreach ($house_info as $k => $v) {
        foreach ($data['sold_list'] as $k => $val) {
          if ($val['id'] == $v['id']) {
            $data['sold_list'][$k]['house'] = unserialize($v['house']);
          }
        }
      }
    } else {
      $data['sold_list'] = array();
    }
    //加载列表页面
    $this->load->view('sold_list/sold_sell', $data);
  }


  /**
   * 改变 cooperate_attached 表里的真实成交价格
   * date   : 2015-01-27
   * author : angel_in_us
   */
  public function change_real_price()
  {
    $id = $this->input->get('id');
    $real_price = $this->input->get('real_price');
    $data = array(
      'id' => $id,
      'real_price' => $real_price
    );
    $result = $this->cooperate_model->sub_transaction_real_price($id, $real_price);
    if ($result == "") {
      echo 123;
    } else {
      echo json_encode($data);
    }
    die;
  }


  /**
   * 根据合作 id 查看房源详情
   * date   : 2015-01-27
   * author : angel_in_us
   */
  public function house_detail($id, $type)
  {
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $house_config = $this->house_config_model->get_config();

    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';

    //控制标题输出
    if ($type == 'sell') {
      $data['type'] = '二手房';
    } else if ($type == 'rent') {
      $data['type'] = '租房';
    }

    $arr = array('id' => $id);
    $where_in = array('id', $arr);
    $house_info = $this->cooperation_model->get_house_info_byids($where_in);

    $house_detail = unserialize($house_info[0]['house']);
    $house_detail['fitment'] = $house_config['fitment'][$house_detail['fitment']];
    $house_detail['forward'] = $house_config['forward'][$house_detail['forward']];

    $data['house_detail'] = $house_detail;
    //控制view层输出
    if ($type == 'sell') {
      $this->load->view('sold_list/sell_house_detail', $data);
    } else if ($type == 'rent') {
      $this->load->view('sold_list/rent_house_detail', $data);
    }
  }


  /**
   * 根据合作 id 查询合作信息
   * date   : 2015-01-27
   * author : angel_in_us
   */
  public function cooperation_detail($id, $type)
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';

    $arr = array('id' => $id);
    $where_in = array('id', $arr);
    $cooperation_info = $this->cooperation_model->get_cooperation_byids($where_in);
    $cooperation_detail = $cooperation_info[0];

    $data['detail'] = $cooperation_detail;

    //控制view层输出
    if ($type == 'sell') {
      $this->load->view('sold_list/cooperation_sell_detail', $data);
    } else if ($type == 'rent') {
      $this->load->view('sold_list/cooperation_rent_detail', $data);
    }
  }


  /**
   * 根据合作 id 查询合作信息
   * date   : 2015-01-27
   * author : angel_in_us
   */
  public function reward_real_price($id)
  {
    echo "<script>alert('页面尚未完成，请稍后重试~!');history.go(-1);</script>";
    die;
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';

    $arr = array('id' => $id);
    $where_in = array('id', $arr);
    $cooperation_info = $this->cooperation_model->get_cooperation_byids($where_in);
    $cooperation_detail = $cooperation_info[0];

    $data['detail'] = $cooperation_detail;

    $this->load->view('sold_list/cooperation_detail', $data);
  }
}

/* End of file sell_house_sold.php */
/* Location: ./application/controllers/sell_house_sold.php */
