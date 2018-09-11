<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author
 */
class Company_notice extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';


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
    $this->load->helper('page_helper');
    $this->load->model('company_notice_model');//消息、公告模型类
    $this->load->model('company_employee_model');
  }


  /**
   * 公司公告
   * @access public
   * @return void
   */
  public function index($page = 1)
  {
    //遗留 判断是否登录
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $company_id = intval($broker_info['company_id']);

    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_R($post_param);exit;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page, $this->_limit);

    //查询条件
    $cond_where = array('company_id' => $company_id);

    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->company_notice_model->get_count_by_cond($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->company_notice_model->get_company_notice_by($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $k => $vo) {
      $vo['contents'] = trim(strip_tags($vo['contents']));
      $list[$k]['contents'] = mb_substr($vo['contents'], 0, 30, 'utf-8');
      $broker_info = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $list[$k]['broker_name'] = $broker_info['truename'];
      if (mb_strlen($vo['contents']) > 30) {
        $list[$k]['contents'] .= '...';
      }
    }
    $data['list'] = $list;
    //print_r($list);exit;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      //. 'mls/css/v1.0/personal_center.css'
      . 'mls/css/v1.0/notice.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/openWin.js,'
      . 'mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/message.js,'
      . 'mls/js/v1.0/personal_center.js'
    //. 'mls/js/v1.0/broker_common.js'
    );


    //页面标题
    $data['page_title'] = '系统权限---公司公告';
    $data['broker_id'] = $broker_id;//获取经纪人编号
    $this->view('company_notice/company_notice.php', $data);
  }

  /**
   * 公司公告详情company_notice
   * @access  public
   * @return  json
   */
  public function detail()
  {
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    $id = $this->input->post('id', TRUE);
    $detail = $this->company_notice_model->get_detail_by_id($id);

    $detail['createtime'] = date('Y-m-d H:i:s', $detail['createtime']);
    //print_r($detail);die;
    echo json_encode($detail);

  }


  /**
   * 添加
   * @access  public
   * @return  json
   */
  public function add_notice()
  {
    $data = array();
    $get_param = $this->input->post(NULL);
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $agency_id = intval($broker_info['agency_id']);
    $company_id = intval($broker_info['company_id']);
    $title = $get_param['title'];
    $color = $get_param['color'];
    $contents = $get_param['contents']; //print_r($contents);die;
    $is_pop = $get_param['is_pop'];
    if (!$is_pop) {
      $is_pop = 0;
    }
    $data = array('broker_id' => $broker_id, 'title' => $title, 'color' => $color, 'contents' => $contents, 'is_pop' => $is_pop, 'createtime' => time(), 'agency_id' => $agency_id, 'company_id' => $company_id);
    $result = $this->company_notice_model->add_notice($data);
    if ($result > 0) {
      $data['result'] = 'ok';
    }
    echo json_encode($data);
  }

  /**
   * 修改
   * @access  public
   * @return  json
   */
  public function update_notice()
  {
    $data = array();
    $get_param = $this->input->post(NULL);
    $title = $get_param['title'];
    $color = $get_param['color'];
    $contents = $get_param['contents']; //print_r($contents);die;
    $is_pop = $get_param['is_pop'];
    if (!$is_pop) {
      $is_pop = 0;
    }
    $data = array('title' => $title, 'color' => $color, 'contents' => $contents, 'is_pop' => $is_pop, 'createtime' => time());
    $result = $this->company_notice_model->update_notice_broker($get_param['id'], $data);
    if ($result > 0) {
      $data['result'] = 'ok';
    }
    echo json_encode($data);
  }

  /**
   * 删除
   * @access  public
   * @return  json
   */
  public function del()
  {
    $ids = $this->input->post('id', TRUE);
    $result = 0;
    if (is_numeric($ids)) {
      $insert_id = $this->company_notice_model->company_notice_del('id = ' . $ids . '');
      if ($insert_id) {
        $result++;
      }
    } else {
      foreach ($ids as $vo) {
        $insert_id = $this->company_notice_model->company_notice_del('id = ' . $vo . '');
        if ($insert_id) {
          $result++;
        }
      }
    }
    if ($result == count($ids)) {
      $res['result'] = 'ok';
    } else {
      $res['result'] = '';
    }
    echo json_encode($res);
  }


  /**
   * 获取排序参数
   * @access private
   * @param  int $order_val
   * @return void
   */
  private function _get_orderby_arr($order_val)
  {
    $arr_order = array();

    switch ($order_val) {
      case 1:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 2:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'ASC';
        break;
      case 3:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 4:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'ASC';
        break;
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
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


  //截取中英文字符串超过固定长度为省略号
  function substr_for_string($sourcestr, $cutlength)
  {
    $returnstr = "";
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr);    //字符串的字节数
    while (($n < $cutlength) and ($i <= $str_length)) {
      $temp_str = substr($sourcestr, $i, 1);
      $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码
      if ($ascnum >= 224) //如果ASCII位高与224，
      {
        $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
        $i = $i + 3; //实际Byte计为3
        $n++; //字串长度计1
      } elseif ($ascnum >= 192)//如果ASCII位高与192，
      {
        $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
        $i = $i + 2; //实际Byte计为2
        $n++; //字串长度计1
      } elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
      {
        $returnstr = $returnstr . substr($sourcestr, $i, 1);
        $i = $i + 1; //实际的Byte数仍计1个
        $n++; //但考虑整体美观，大写字母计成一个高位字符
      } else //其他情况下，包括小写字母和半角标点符号，
      {
        $returnstr = $returnstr . substr($sourcestr, $i, 1);
        $i = $i + 1;    //实际的Byte数计1个
        $n = $n + 0.5;    //小写字母和半角标点等与半个高位字符宽…
      }
    }
    if ($str_length > $cutlength) {
      $returnstr = $returnstr . "...";    //超过长度时在尾处加上省略号
    }
    return $returnstr;
  }
}
/* End of file message.php */
/* Location: ./application/mls/controllers/message.php */
