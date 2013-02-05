<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package     ExpressionEngine
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license     http://expressionengine.com/user_guide/license.html
 * @link        http://expressionengine.com
 * @since       Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ClickTale Extension
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    Extension
 * @author      Ben Wilkins
 * @link        http://paramoredigital.com/
 */

class Clicktale_ext
{

    public $settings = array();
    public $description = 'Enables the PHP Integration Module for ClickTale';
    public $docs_url = '';
    public $name = 'ClickTale';
    public $settings_exist = 'y';
    public $version = '1.1';

    private $EE;

    /**
     * @param mixed
     */
    public function __construct($settings = '')
    {
        $this->EE =& get_instance();
        $this->settings = $settings;
    }

    /**
     * @see http://codeigniter.com/user_guide/database/index.html for
     * more information on the db class.
     * @return void
     */
    public function activate_extension()
    {
        $this->settings = array();

        $data = array(
            'class'    => __CLASS__,
            'method'   => 'template_post_parse',
            'hook'     => 'template_post_parse',
            'settings' => serialize($this->settings),
            'version'  => $this->version,
            'enabled'  => 'y'
        );

        $this->EE->db->insert('extensions', $data);
    }

    /**
     * @param string $template
     * @param bool $is_embed
     * @param int $site_id
     * @return bool|null|string
     */
    public function template_post_parse($template, $is_embed, $site_id)
    {
        if (! $this->setup_directory('Cache')
          OR ! $this->setup_directory('Logs')
          OR ! $this->write_config_file()
          OR ! $this->write_scripts_file()
        )
            return $template;

        $this->init_clicktale();

        if ($this->is_last_extension_call())
            $template = $this->EE->extensions->last_call;

        if (! $is_embed)
            $template = $this->inject_clicktale_scripts($template);

        return $template;
    }

    /**
     * @param $dir
     * @param null $path
     * @return bool
     */
    private function setup_directory($dir, $path = NULL)
    {
        if (is_null($path))
            $path = '../system/expressionengine/third_party/clicktale/libraries/';

        if (! is_dir($path . $dir))
            return mkdir($path . $dir, 0775);
        else
            return TRUE;
    }

    /**
     * @return int
     */
    private function write_scripts_file()
    {
        $path = realpath('../system/expressionengine/third_party/clicktale/libraries');
        $template = file_get_contents($path . '/ClickTaleScripts_TEMPLATE.txt');

        return file_put_contents(
            $path . '/ClickTaleScripts.xml',
            sprintf(
                $template,
                $this->settings['top_part'],
                $this->settings['bottom_part']
            )
        );
    }

    /**
     * @return int
     */
    private function write_config_file()
    {
        $path = realpath('../system/expressionengine/third_party/clicktale/libraries');

        $data = file_get_contents($path . '/config_TEMPLATE.txt');
        $data .= '$config["CacheFetchingUrl"] = "'
          . $this->settings['path_to_cache']
          . '/ClickTaleCache.php?t=%CacheToken%'
          . '";'
          . PHP_EOL;
        $data .= '$config["AllowedAddresses"] = "'
          . $this->settings['allowed_ips']
          . '";'
          . PHP_EOL;

        return file_put_contents($path . '/config.php', $data);
    }

    /**
     * @return bool
     */
    private function is_last_extension_call()
    {
        return (
          isset($this->EE->extensions->last_call)
            && $this->EE->extensions->last_call
        );
    }

    /**
     * @return void
     */
    private function init_clicktale()
    {
        if (! defined('ClickTale_Root')) {
            $pathinfo = pathinfo(__FILE__);
            define ("ClickTale_Root", $pathinfo["dirname"] . '/libraries');
        }

        require_once(ClickTale_Root . "/ClickTale.inc.php");
        require_once(ClickTale_Root . "/ClickTale.Logger.php");
        require_once(ClickTale_Root . "/ClickTale.Settings.php");
    }

    /**
     * @param string $template
     * @return bool|null|string
     */
    private function inject_clicktale_scripts($template = '')
    {
        $parsed_template = $this->EE->TMPL->parse_globals($template);
        $parsed_template = ClickTale_ProcessOutput($parsed_template);

        return $parsed_template;
    }

    /**
     * @return array
     */
    function settings()
    {
        $settings = array();

        $settings['path_to_cache'] = array('i', '', '');
        $settings['allowed_ips'] = array('i', '', '75.125.82.64/26,50.97.162.64/26');
        $settings['top_part'] = array(
            't',
            array('rows' => '5'),
            '<!-- ClickTale Top part -->' . PHP_EOL
              . '<script type="text/javascript">' . PHP_EOL
              . 'var WRInitTime=(new Date()).getTime();' . PHP_EOL
              . '</script>' . PHP_EOL
              . '<!-- ClickTale end of Top part -->'
        );

        $settings['bottom_part'] = array('t', array('rows' => '20'), '');

        return $settings;
    }

    /**
     * @return void
     */
    function disable_extension()
    {
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('extensions');
    }

    /**
     * @param string $current
     * @return bool
     */
    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version) {
            return FALSE;
        }

        return FALSE;
    }
}

/* End of file ext.clicktale.php */
/* Location: /system/expressionengine/third_party/clicktale/ext.clicktale.php */