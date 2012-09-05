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

class Clicktale_library {

	private $EE;

	public function __construct() {
		$this->EE = &get_instance();
	}

	public function init()
	{
		if (!defined('ClickTale_Root'))
		{
			$pathinfo = pathinfo(__FILE__);
			define ("ClickTale_Root", $pathinfo["dirname"]);
		}

		require_once(ClickTale_Root."/ClickTale.inc.php");
		require_once(ClickTale_Root."/ClickTale.Logger.php");
		require_once(ClickTale_Root."/ClickTale.Settings.php");
	}
}

/* End of file clicktale_library.php */
/* Location: /system/expressionengine/third_party/automin/libraries/clicktale_library.php */