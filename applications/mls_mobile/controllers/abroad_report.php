<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class Abroad_report extends MY_Controller
{

  /**
   * 当前页码
   *
   * @access private
   * @var string
   */
  private $_current_page = 1;

  /**
   * 每页条目数
   *
   * @access private
   * @var int
   */
  private $_limit = 15;
  /**
   * 偏移
   *
   * @access private
   * @var int
   */
  private $_offset = 0;

  /**
   * 条目总数
   *
   * @access private
   * @var int
   */
  private $_total_count = 0;


  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    //加载城市模型类
    $this->load->model('city_model');
    //加载海外报备模型类
    $this->load->model('abroad_model');
    //加载海外、旅游地产报备配置项模型类
    $this->load->model('abroad_config_model');
    //加载基础配置项模型类
    $this->load->model('house_config_model');
    //加载海外报备模型类
    $this->load->model('abroad_report_model');
  }

  private function _get_cond_str($form_param)
  {
    //合同id
    $keyword = isset($form_param['keyword']) ? trim($form_param['keyword']) : '';
    if ($keyword) {
      $cond_where .= " AND (user_phone like '%" . $keyword . "%' or user_name like '%" . $keyword . "%')";
    }
    return $cond_where;
  }

  /**
   * 初始化分页参数
   *
   * @access public
   * @param  int $current_page
   * @param  int $page_size
   * @return void
   */
  private function _init_pagination($current_page = 1, $page_size = 0)
  {
    /** 当前页 */
    $this->_current_page = ($current_page && is_numeric($current_page)) ?
      intval($current_page) : 1;

    /** 每页多少项 */
    $this->_limit = ($page_size && is_numeric($page_size)) ?
      intval($page_size) : $this->_limit;

    /** 偏移量 */
    $this->_offset = ($this->_current_page - 1) * $this->_limit;

    if ($this->_offset < 0) {
      redirect(base_url());
    }
  }


  /**
   * 合同报备
   * @access public
   * @return void
   */
  public function index()
  {

    //模板使用数据
    $data = array();
    //获取报备配置信息
    $config = $this->abroad_config_model->get_config();
    //获取基础配置信息
    $base_config = $this->house_config_model->get_config();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $cond_where = "broker_id = {$this->user_arr['broker_id']}";
    //表单提交参数组成的查询条件
    $cond_where .= $this->_get_cond_str($post_param);
    $cond_where = trim($cond_where);
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //符合条件的总行数
    $this->_total_count = $this->abroad_report_model->count_by($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->abroad_report_model->get_all_by($cond_where, $this->_offset, $this->_limit);

    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        $list[$key]['status'] = $config['status'][$val['status']];
        $city = $this->city_model->get_by_id($val['user_city']);
        $list[$key]['user_city'] = $city['cityname'];
        if ($val['house_id']) {
          $house_info = $this->abroad_model->get_by_id($val['house_id']);
          $list[$key]['house_info']['block_name'] = $house_info['block_name'];
          $country = $this->abroad_model->get_by_country_id($house_info['country_id']);
          $city = $this->abroad_model->get_by_city_id($house_info['city_id']);
          $list[$key]['house_info']['city_info'] = $country['country_name'] . $city['city_name'];
          $list[$key]['house_info']['first_pay'] = $house_info['first_pay'] . $country['money_unit'];
        } else {
          $list[$key]['house_info'] = '无意向楼盘';
        }
      }
      $data['msg'] = '查询报备列表成功';
    } else {
      $data['msg'] = '暂无报备信息';
    }
    $data['list'] = $list;
    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page, //当前页数
      'list_rows' => $this->_limit, //每页显示个数
    );
    $this->result(1, $data['msg'], array('list' => $data['list']));
  }


  public function add()
  {
    $name = $this->input->post('name');
    $phone = $this->input->post('phone');
    $house_id = $this->input->post('house_id');
    if ($this->user_arr['group_id'] == 2) {
      if ($name && $phone) {
        $insert_data = array(
          'user_name' => $name,
          'user_phone' => $phone,
          'user_city' => $this->user_arr['city_spell'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'broker_phone' => $this->user_arr['phone'],
          'house_id' => $house_id,
          'from_type' => 2,
          'from' => 5,
          'status' => 1,
          'createtime' => time()
        );
        $result = $this->abroad_report_model->add_info($insert_data);
        if ($result) {
          $result = 1;
          $msg = '报备成功';
        } else {
          $result = 0;
          $msg = '报备失败';
        }
      } else {
        $result = 0;
        $msg = '客户姓名或电话不能为空';
      }
    } else {
      $result = 0;
      $msg = '未认证用户不能报备';
    }
    $this->result($result, $msg);
  }
}
