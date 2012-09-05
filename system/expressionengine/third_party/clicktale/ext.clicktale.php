<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ClickTale Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Ben Wilkins
 * @link		http://paramore.is/
 */

class Clicktale_ext {
	
	public $settings 		= array();
	public $description		= 'Enables the PHP Integration Module for ClickTale';
	public $docs_url		= '';
	public $name			= 'ClickTale';
	public $settings_exist	= 'n';
	public $version			= '1.0';
	
	private $EE;
	
	/**
	 * Constructor
	 * @param mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '') {
		$this->EE =& get_instance();
		$this->settings = $settings;
	}
	
	/**
	 * Activate Extension
	 * This function enters the extension into the exp_extensions table
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 * @return void
	 */
	public function activate_extension()
	{
		// Setup custom settings in this array.
		$this->settings = array();
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'template_post_parse',
			'hook'		=> 'template_post_parse',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		$this->EE->db->insert('extensions', $data);			
	}	
	
	/**
	 * Hook for processing template output.
	 * @param string $template_string The template markup
	 * @param bool $is_embed Is the template an embed?
	 * @param int $site_id Site ID
	 * @return string The final template string
	 */
	public function template_post_parse($template, $is_embed, $site_id)
	{
		$this->EE->load->library('clicktale_library');
		$this->EE->clicktale_library->init();

		if ($this->is_last_extension_call())
			$template = $this->EE->extensions->last_call;

		if ( ! $is_embed)
			$template = $this->_inject_clicktale_scripts($template);
		
		return $template;
	}

	/**
	 * Disable Extension
	 * This method removes information from the exp_extensions table
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	/**
	 * Update Extension
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 * @return mixed void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}

	private function is_last_extension_call()
	{
		return (
			isset($this->EE->extensions->last_call) 
			&& $this->EE->extensions->last_call
		);
	}

	private function _inject_clicktale_scripts($template = '')
	{
			$parsed_template = $this->EE->TMPL->parse_globals($template);
			$parsed_template = ClickTale_ProcessOutput($parsed_template);
			return $parsed_template;
	}
}

/* End of file ext.clicktale.php */
/* Location: /system/expressionengine/third_party/clicktale/ext.clicktale.php */