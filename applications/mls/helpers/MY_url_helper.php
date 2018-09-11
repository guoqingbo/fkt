<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package	CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license	http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MLS URL Helpers
 *
 * @package     MLS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		esf Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/url_helper.html
 */

// ------------------------------------------------------------------------

/**
 * 返回客源修改页面 URL
 *
 * MLS系统内部客源修改页面地址
 *
 * @access	public
 * @param	int $customer_id 客源编号
 * @param	string $type 客源类型
 * @return	string
 */
if ( ! function_exists('customer_modify_url'))
{
	function customer_modify_url($customer_id , $type = 'buy')
	{
        $page_url = '';

        $customer_id = intval($customer_id);
        $type = strip_tags($type);

        if($customer_id > 0 && $type == 'buy')
        {
            $page_url = MLS_URL.'/customer/modify/'.$customer_id;
        }
        else if($customer_id > 0 && $type == 'rent')
        {
            $page_url = MLS_URL.'/rent_customer/modify/'.$customer_id;
        }

        return $page_url;
	}
}


/**
 * 客源发布页面 URL
 *
 * MLS系统内部客源修改页面地址
 *
 * @access	public
 * @param	string $type 客源类型
 * @return	string
 */
if ( ! function_exists('customer_publish_url'))
{
	function customer_publish_url($type = 'buy')
	{
        $page_url = '';

        $type = strip_tags($type);

        if( $type == 'buy')
        {
            $page_url = MLS_URL.'/customer/publish';
        }
        else if($type == 'rent')
        {
            $page_url = MLS_URL.'/rent_customer/publish';
        }

        return $page_url;
	}
}


/**
 * 客源发布页面 URL
 *
 * MLS系统内部客源修改页面地址
 *
 * @access	public
 * @param	string $type 客源类型
 * @return	string
 */
if ( ! function_exists('customer_details_url'))
{
	function customer_details_url($customer_id , $type = 'buy')
	{
        $page_url = '';

        $customer_id = intval($customer_id);
        $type = strip_tags($type);

        if($customer_id > 0 && $type == 'buy')
        {
            $page_url = MLS_URL.'/customer/customer_detail/'.$customer_id;
        }
        else if($customer_id > 0 && $type == 'rent')
        {
            $page_url = MLS_URL.'/rent_customer/customer_detail/'.$customer_id;
        }

        return $page_url;
	}
}
