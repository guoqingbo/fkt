<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-个人记事本
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_notebook extends MY_Controller
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
  private $_limit = 10;

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

  public function __construct()
  {
    parent::__construct();
    $this->load->model('my_notebook_model');
  }

  public function index()
  {
    $broker_id = $this->user_arr['broker_id'];
    //模板使用数据
    $data = array();

    $data['user_menu'] = $this->user_menu;
    $data['truename'] = $this->user_arr['truename'];
    $data['agency_name'] = $this->user_arr['agency_name'];
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);


    //查询消息的条件
    $cond_where = "broker_id = {$broker_id} ";
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;
    //符合条件的总行数
    $this->_total_count =
      $this->my_notebook_model->count_by($cond_where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->my_notebook_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $key => &$value) {
      if (strlen($value['content']) > 69) {
        $content = $this->left($value['content'], 23);
        $value['content'] = $content . ' &gt;&gt;&gt;';
      }
    }

    $data['list'] = $list;

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

    //页面标题
    $data['page_title'] = '个人记事本';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js');

    $this->view('uncenter/my_notebook/my_notebook', $data);
  }

  /**
   * 字符串截取
   */
  public function left($str, $len, $charset = "utf-8")
  {
    //如果截取长度小于等于0，则返回空
    if (!is_numeric($len) or $len <= 0) {
      return "";
    }

    //如果截取长度大于总字符串长度，则直接返回当前字符串
    $sLen = strlen($str);
    if ($len >= $sLen) {
      return $str;
    }

    //判断使用什么编码，默认为utf-8
    if (strtolower($charset) == "utf-8") {
      $len_step = 3; //如果是utf-8编码，则中文字符长度为3
    } else {
      $len_step = 2; //如果是gb2312或big5编码，则中文字符长度为2
    }

    //执行截取操作
    $len_i = 0;
    //初始化计数当前已截取的字符串个数，此值为字符串的个数值（非字节数）
    $substr_len = 0; //初始化应该要截取的总字节数

    for ($i = 0; $i < $sLen; $i++) {
      if ($len_i >= $len) break; //总截取$len个字符串后，停止循环
      //判断，如果是中文字符串，则当前总字节数加上相应编码的中文字符长度
      if (ord(substr($str, $i, 1)) > 0xa0) {
        $i += $len_step - 1;
        $substr_len += $len_step;
      } else { //否则，为英文字符，加1个字节
        $substr_len++;
      }
      $len_i++;
    }
    $result_str = substr($str, 0, $substr_len);
    return $result_str;
  }

  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //时间条件
    date_default_timezone_set('PRC');
    if (isset($form_param['start_time']) && $form_param['start_time']) {
      $start_time = strtotime($form_param['start_time'] . " 00:00");
      $cond_where .= " AND created >= '" . $start_time . "'";
    }

    if (isset($form_param['end_time']) && $form_param['end_time']) {
      $end_time = strtotime($form_param['end_time'] . " 23:59");
      $cond_where .= " AND created <= '" . $end_time . "'";
    }
    if (isset($start_time) && isset($end_time) && $start_time > $end_time) {
      $this->jump(MLS_URL . '/my_notebook/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($form_param['blur']) {
      $blur = $form_param['blur'];
      $cond_where .= " AND ( title LIKE '%" . $blur . "%' OR content LIKE '%" . $blur . "%' )";
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
   * 记事本详情
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function details($id)
  {
    $isajax = $this->input->get('isajax', TRUE);
    //详情信息
    $data_info = $this->my_notebook_model->get_by_id($id);
    if ($isajax) {
      echo json_encode(array('result' => 'ok', 'data' => $data_info));
    }
  }


  /**
   * 添加记事本信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function add()
  {
    $broker_id = $this->user_arr['broker_id'];

    $datainfo['broker_id'] = $broker_id;
    $title = $this->input->post('title', TRUE);
    $content = $this->input->post('content', TRUE);
    $datainfo['title'] = trim($title);
    $datainfo['content'] = trim($content);
    $datainfo['created'] = time();

    $id = $this->my_notebook_model->add_info($datainfo);
    if ($id) {
      echo json_encode(array('result' => 'ok'));
    } else {
      echo json_encode(array('result' => 'no'));
    }
  }

  /**
   * 保存更改记事本信息
   *
   * @access  public
   * @param   void
   * @return  void
   */
  public function modify()
  {
    $id = $this->input->post('m_id', TRUE);
    $title = $this->input->post('m_title', TRUE);
    $content = $this->input->post('m_content', TRUE);
    $datainfo['title'] = trim($title);
    $datainfo['content'] = trim($content);
    $datainfo['created'] = time();

    $res = $this->my_notebook_model->save_modify($id, $datainfo);
    if ($res) {
      echo json_encode(array('result' => 'ok'));
    } else {
      echo json_encode(array('result' => 'no'));
    }
  }

  /**
   * 删除记事本
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function del($del_id = 0)
  {
    $id = $this->input->get('id');
    $rs = $this->my_notebook_model->del_by_id($id);
    if ($rs) {
      echo json_encode(array('result' => 'ok'));
    } else {
      echo json_encode(array('result' => 'no'));
    }

  }

  /**
   * 删除多条记事本
   *
   * @access public
   * @param   void
   * @return  void
   */
  public function del_more()
  {
    $ids = $this->input->get('notebook_ids');
    $rs = $this->my_notebook_model->del_by_ids($ids);
    $action_result = '';
    if ($rs) {
      $action_result = 'success';
    }
    echo $action_result;
  }
}
