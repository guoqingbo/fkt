<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLSϵͳ���
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Collocation_contract_base_model CLASS
 *
 * �йܺ�ͬ�³����ͬ��ѯ����ӡ�ɾ�����޸Ĺ�����
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          wll
 */
class Collocation_rent_contract_base_model extends MY_Model
{

  /**
   * �����ͬ��
   * @var string
   */
  private $_tbl = 'collocation_rent';
  /**
   * ����Ӧ�տͻ���
   * @var string
   */
  private $_tbl2 = 'need_pay_customer';
  /**
   * ����ʵ�տͻ���
   * @var string
   */
  private $_tbl3 = 'actual_pay_customer';
  /**
   * ���������
   * @var string
   */
  private $_tbl4 = 'collocation_contract_log';
  /**
   * �йܺ�ͬ��ű��
   *
   * @access private
   * @var integer
   */
  private $_id = 0;

  /**
   * ��ѯ�ֶ�
   * @var string
   */
  private $_select_fields = array();

  /**
   * ���ʼ��
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * ͨ�������ͬid��ȡӦ����ʵ����¼
   * @param int $id ���
   * @return array ��ͬ��¼�б�����
   */
  public function get_list_by_rid($where, $tag, $start = 0, $limit = 15,
                                  $order_key = 'id', $order_by = 'DESC')
  {
    //�ж������ĸ��������
    if ($tag == 1) {//Ӧ
      $this->_tb = $this->_tbl2;
    } elseif ($tag == 2) {//ʵ
      $this->_tb = $this->_tbl3;
    } elseif ($tag == 3) {//����
      $this->_tb = $this->_tbl4;
    }

    //��ѯ����
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->_tb);

    //��������
    $this->dbback_city->order_by($this->_tb . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //���ؽ��
    return $this->dbback_city->get()->result_array();
  }

  /**
   * ɾ�������ͬ��Ӧ�տͻ���¼
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del_need_receive_by_id($id, $tag)
  {
    //�ж������ĸ��������
    if ($tag == 1) {//Ӧ
      $this->_tb = $this->_tbl2;
    } elseif ($tag == 2) {//ʵ
      $this->_tb = $this->_tbl3;
    }
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    $this->db_city->delete($this->_tb);
    return $this->db_city->affected_rows();
  }

  //ɾ�������ͬʱͬʱɾ����Ӧ�����Ӧ�գ�ʵ��
  public function del_need_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('r_id', $ids);
    $this->db_city->delete($this->_tbl2);
    return $this->db_city->affected_rows();
  }

  public function del_actual_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('r_id', $ids);
    $this->db_city->delete($this->_tbl3);
    return $this->db_city->affected_rows();
  }

  /**
   * ��ӳ����ͬ--ʵ��,Ӧ��
   * @return string
   */
  public function add_need_receive_info($data_info, $type)
  {
    //�ж������ĸ��������
    if ($type == 1 || $type == 2) {//Ӧ
      $this->_tb = $this->_tbl2;
    } elseif ($type == 3) {//ʵ
      $this->_tb = $this->_tbl3;
    }
    $this->db_city->insert($this->_tb, $data_info);
    return $this->db_city->affected_rows() >= 1 ? $this->db_city->insert_id() : 0;
  }

  //ͨ��id��ȡ��ر�ļ�¼
  public function get_need_receive_by_id($id, $tag)
  {
    //�ж������ĸ��������
    if ($tag == 1) {//Ӧ
      $this->_tb = $this->_tbl2;
    } elseif ($tag == 2) {//ʵ
      $this->_tb = $this->_tbl3;
    }
    //��ѯ�ֶ�
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //��ѯ����
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tb)->row_array();
  }

  /**
   * ����Ӧ��ҵ��id����
   * @param array $update_data ���µ�����Դ����
   * @param array $id ���
   * @return int �ɹ��󷵻���Ӱ�������
   */
  public function update_need_receive_by_id($update_data, $id, $tag)
  {
    //�ж������ĸ��������
    if ($tag == 1) {//Ӧ��
      $this->_tb = $this->_tbl2;
    } elseif ($tag == 2) {//ʵ��
      $this->_tb = $this->_tbl3;
    }
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tb, $update_data);
    } else {
      $this->db_city->update($this->_tb, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  //����,�ܼҷ���,��ͬ�б�����
  public function get_list_by_tag($where, $start = 0, $limit = 20, $tag,
                                  $order_key = 'signing_time', $order_by = 'DESC')
  {
    //�ж������ĸ��������
    if ($tag == 1) {//Ӧ��
      $this->_tb = $this->_tbl2;
    } elseif ($tag == 2) {//ʵ��
      $this->_tb = $this->_tbl3;
    }
    if ($where) {
      //��ѯ����
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tb);

    //��������
    $this->dbback_city->order_by($this->_tb . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //���ؽ��
    return $this->dbback_city->get()->result_array();
  }

  /**
   * �������������ͬ������
   * @param string $where ��ѯ����
   * @return int
   */
  public function count_by_tag($where = '', $tag)
  {
    //�ж������ĸ��������
    if ($tag == 1) {//Ӧ��
      $this->_tb = $this->_tbl2;
    } elseif ($tag == 2) {//ʵ��
      $this->_tb = $this->_tbl3;
    }
    if ($where) {
      //��ѯ����
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tb);
  }

  //���ݳ����ͬid�ҳ�Ӧ�գ�ʵ�գ������µ�����
  public function count_by_rent_tag($where, $tag)
  {
    //�ж������ĸ��������
    if ($tag == 1) {//Ӧ��
      $this->_tb = $this->_tbl2;
    } elseif ($tag == 2) {//ʵ��
      $this->_tb = $this->_tbl3;
    } elseif ($tag == 3) {//����
      $this->_tb = $this->_tbl4;
    }
    if ($r_id) {
      //��ѯ����
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tb);
  }


  /**
   * ��ʼ����ͬ���
   *
   * @access  public
   * @param  int $id
   * @return  void
   */
  public function set_id($id)
  {
    $this->_id = intval($id);
  }

  /**
   * ��ȡid
   *
   * @access  public
   * @param  void
   * @return  int ���۳�����Ϣ���
   */
  public function get_id()
  {
    return $this->_id;
  }

  /**
   * ���õ��йܺ�ͬ����Ҫ��ѯ���ֶ�����
   *
   * @access  public
   * @param  array $arr_fields
   * @return  void
   */
  public function set_search_fields($arr_fields)
  {
    $this->_search_fields = $arr_fields;
  }

  public function get_search_fields()
  {
    return $this->_search_fields;
  }

  /**
   * ����йܺ�ͬ
   * @return string
   */
  public function add_info($data_info)
  {
    $this->db_city->insert($this->_tbl, $data_info);
    return $this->db_city->affected_rows() >= 1 ? $this->db_city->insert_id() : 0;
  }

  /**
   * ����йܳ����ͬ
   * @return string
   */
  public function add_rent_info($data_info)
  {
    $this->db_city->insert($this->_tbl5, $data_info);
    return $this->db_city->affected_rows() >= 1 ? $this->db_city->insert_id() : 0;
  }


  /**
   * ������Ҫ��ѯ���ֶ�
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
   * ��ȡ��Ҫ��ѯ���ֶ�
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
  }

  /**
   * ��������������
   * @param string $where ��ѯ����
   * @return int
   */
  public function count_by($where = '')
  {
    if ($where) {
      //��ѯ����
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  /**
   * ��ȡ��ͬ�б�ҳ
   * @param string $where ��ѯ����
   * @param int $start ��ѯ��ʼ��
   * @param int $limit ����ƫ����
   * @param int $order_key �����ֶ�
   * @param string $order_by ���򡢽���Ĭ�Ͻ�������
   * @return array ���ض�����¼��ɵĶ�ά����
   */
  public function get_all_by($where, $start = 0, $limit = 20,
                             $order_key = 'id', $order_by = 'DESC')
  {
    //��ѯ�ֶ�
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($where) {
      //��ѯ����
      $this->dbback_city->where($where);
    }
    //��������
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //���ؽ��
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * ���ݲ�ѯ��������һ����ͬ��ļ�¼
   * @param string $where ��ѯ����
   * @return array ����һ��һά����ı��¼
   */
  public function get_one_by($where = '')
  {
    //��ѯ�ֶ�
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //��ѯ����
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * ͨ���йܺ�ͬ��Ż�ȡ��¼
   * @param int $id ���
   * @return array ��ͬ��¼��ɵ�һά����
   */
  public function get_by_id($id)
  {
    //��ѯ�ֶ�
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //��ѯ����
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  //���ݳ����ͬid
  public function get_by_rent_id($id)
  {
    //��ѯ�ֶ�
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //��ѯ����
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl5)->row_array();
  }


  /**
   * ͨ����ͬ��Ż�ȡ��¼
   * @param int $number ��ͬ���
   * @return array ��ͬ��¼��ɵ�һά����
   */
  public function get_by_contract_number($contract_no)
  {
    //��ѯ�ֶ�
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //��ѯ����
    $this->dbback_city->where('number', $contract_no);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * ���ݱ�Ų�ѯ��ͬ�Ƿ���
   * @param $id ��ͬID
   * @return int ����״̬
   */
  public function is_completed_by_id($id)
  {
    $this->db_city->select('is_completed');
    $this->db_city->where('id', $id);
    $result = $this->db_city->get($this->_tbl)->row_array();
    return $result['is_completed'];
  }

  /**
   * �����йܺ�ͬ��Ÿ��º�ͬ����ϸ��Ϣ����
   * @param array $update_data ���µ�����Դ����
   * @param array $id ���
   * @return int �ɹ��󷵻���Ӱ�������
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  //���ݳ����ͬid����
  public function update_by_rent_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl5, $update_data);
    } else {
      $this->db_city->update($this->_tbl5, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * ����ĳ����Դ������Ϣ
   *
   * @access  protected
   * @param  array $update_arr ��Ҫ�����ֶεļ�ֵ��
   * @param  string $cond_where ��������
   * @param  boolean $escape �Ƿ�ת������ֶε�ֵ
   * @return  boolean �Ǹ��³ɹ���TRUE-�ɹ���FAlSEʧ�ܡ�
   */
  public function update_info_by_cond($update_arr, $cond_where, $tbl = '', $escape = TRUE)
  {

    if ($tbl == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //��������
    $this->db_city->where($cond_where);

    //��������
    $this->db_city->update($tbl);

    return $this->db_city->affected_rows();
  }


  /**
   * ɾ���йܺ�ͬ��¼
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows();
  }

  /**
   * ����id
   *
   * @access  protected
   * @return  array
   */
  public function get_collocationinfo_by_id()
  {
    $demandinfo = array();
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      //��ѯ�ֶ�
      $select_fields_str = implode(',', $select_fields);
      $this->db_city->select($select_fields_str);
    }
    //��ȡ���ۡ�������Ϣ���
    $id = $this->get_id();

    if ($id <= 0) {
      return $demandinfo;
    }

    $cond_where = "id = " . $id;
    $demandinfo = $this->get_info_by_cond($cond_where);

    return $demandinfo;
  }

  /**
   * ����������ȡ��ͬ��Ϣ
   *
   * @access  protected
   * @param  string $cond_where ��ѯ����
   * @return  array ���ۡ�������Ϣ
   */
  protected function get_info_by_cond($cond_where)
  {
    $arr_data = array();

    //��ȡ������
    $tbl_demand = $this->_tbl;

    //�����Ҫ��ѯ�ĳ��ۡ�������Ϣ�ֶ�
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
      $this->db_city->select($select_fields_str);
    }

    //��ѯ����
    if ($cond_where != '') {
      $this->db_city->where($cond_where);
    }

    //��ѯ
    $arr_data = $this->db_city->get($tbl_demand)->row_array();
    return $arr_data;
  }
}

/* End of file contract_base_model.php */
/* Location: ./applications/models/contract_base_model.php */
