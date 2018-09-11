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
 * Im_sync_message_base_model CLASS
 *
 * IM
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Im_sync_message_base_model extends MY_Model
{

  /**
   * �н��
   * @var string
   */
  private $_fkt_tbl = 'im_fkt_message';

  /**
   * ��ѯ�ֶ�
   * @var string
   */
  private $_select_fields = '';

  //��Ϣ����
  private $_object_name_id = array(
    'RC:TxtMsg' => 1, /**�ı���Ϣ**/
    'RC:ImgMsg' => 2, /**ͼƬ��Ϣ**/
    'RC:VcMsg' => 3, /**������Ϣ**/
    'RC:ImgTextMsg' => 4, /**ͼ����Ϣ**/
    'RC:LBSMsg' => 5, /**λ����Ϣ**/
    'RC:ContactNtf' => 6, /**�����ϵ����Ϣ**/
    'RC:InfoNtf' => 7, /**��ʾ��֪ͨ��Ϣ**/
    'RC:ProfileNtf' => 8, /**����֪ͨ��Ϣ**/
    'RC:CmdNtf' => 9, /**ͨ������֪ͨ��Ϣ**/
    'RC:HsMsg' => 10, /**�ͷ�������Ϣ**/
    'RC:SpMsg' => 10,/**�ͷ��Ҷ���Ϣ**/
  );

  //�Ự����
  private $_channel_type_id = array(
    'PERSON' => 1, /**���˻Ự**/
    'PERSONS' => 2, /**������Ự**/
    'GROUP' => 3, /**Ⱥ��Ự**/
    'TEMPGROUP' => 4, /**�����һỰ**/
    'CUSTOMERSERVICE' => 5, /**�ͷ��Ự**/
    'NOTIFY' => 6, /**ϵͳ֪ͨ**/
    'MC' => 7, /**Ӧ�ù��ڷ���**/
    'MP' => 8,/**���ڷ���**/
  );

  /**
   * ���ʼ��
   */
  public function __construct()
  {
    parent::__construct();
  }

  //����ͨ�����¼
  public function insert_fkt_data($insert_data)
  {

    //ת��
    $insert_data['object_name_id'] = $this->_object_name_id[$insert_data['object_name']];
    unset($insert_data['object_name']);
    $insert_data['channel_type_id'] = $this->_channel_type_id[$insert_data['channel_type']];
    unset($insert_data['channel_type']);
    //��������
    if ($this->db_city->insert($this->_fkt_tbl, $insert_data)) {
      return $this->db_city->insert_id();
    }
    return false;
  }
}

/* End of file Im_sync_message_base_model.php */
/* Location: ./applications/models/agency_base_model.php */
