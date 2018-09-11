<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 客源模块公用函数文件
 *
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Helpers
 * @author      HOUSE365 ESF Dev Team
 * @link        http://nj.sell.house365.com/
 */

/**
* 根据客源编号和客源类型获取客源展示编号
* @param int $id 客源编号
* @param string $type 客源类型buy/rent
* @return string 客源展示编号
*/
function get_custom_id( $id , $type = 'buy')
{
    $customer_id_str = '';
    
    if( $id > 0 )
    {
        switch ($type)
        {
            case 'buy':
                $customer_id_str = 'QG'.$id;
            break;
        
            case 'rent':
                $customer_id_str = 'QZ'.$id;
            break;
        }
    }
    
    return $customer_id_str;
}
