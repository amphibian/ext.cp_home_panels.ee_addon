<?php

if(!defined('EXT'))
{
	exit('Invalid file request');
}

class Cp_home_panels
{
	var $settings        = array();
	var $name            = 'Custom CP Home Panels';
	var $version         = '1.0.1';
	var $description     = 'Add up to two custom panels to the control panel home page.';
	var $settings_exist  = 'y';
	var $docs_url        = 'http://github.com/amphibian/ext.cp_home_panels.ee_addon';

	
	// -------------------------------
	//   Constructor - Extensions use this for settings
	// -------------------------------
	
	function Cp_home_panels($settings='')
	{
	    $this->settings = $settings;
	}
	// END
	
	
	// --------------------------------
	//  Settings
	// --------------------------------  
	
	function settings()
	{	    			
		$settings = array();
	    $settings['custom_panel_one_heading'] = '';
	    $settings['custom_panel_one'] = array('t', '', NULL);
	    $settings['custom_panel_two_heading'] = '';
	    $settings['custom_panel_two'] = array('t', '', NULL);
	    return $settings;
	}
	// END
	
	
	function get_user_id()
	{
		global $IN, $SESS;
		return ( ! $IN->GBL('id', 'GP')) ? $SESS->userdata('member_id') : $IN->GBL('id', 'GP');
	}
	// END
	
	
	function check_for_panels()
	{
		$panels = array();
		if(!empty($this->settings['custom_panel_one_heading'])) $panels[] = 'custom_panel_one';
		if(!empty($this->settings['custom_panel_two_heading'])) $panels[] = 'custom_panel_two';
		return $panels;
	}
	
	    
	function myaccount_homepage_builder($i)
	{
		global $DSP, $DB, $EXT;
		$r = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';

		if($panels = $this->check_for_panels())
		{
			$id = $this->get_user_id();
			$prefs = array();

			// This is all basically lifted from the function 'homepage_builder'
			// Located in /system/cp/cp.myaccount.php  
					
			$DB->fetch_fields = TRUE;
			$query = $DB->query("
				SELECT ".implode(',', $panels)." 
				FROM exp_member_homepage 
				WHERE member_id = '".$DB->escape_str($id)."'
			");
			if ($query->num_rows == 0)
	        {        
	            foreach ($query->fields as $f)
	            {
					$prefs[$f] = 'n';
	            }
	        }
	        else
	        {  
	        	unset($query->row['member_id']);
	              
	            foreach ($query->row as $key => $val)
	            {
					$prefs[$key] = $val;
	            }
	        }
	        
		  	foreach ($prefs as $key => $val)
			{
				$style = ($i++ % 2) ? 'tableCellOne' : 'tableCellTwo';
				
				$r .= $DSP->tr();
				$r .= $DSP->table_qcell($style, $DSP->qspan('defaultBold', $this->settings[$key.'_heading']));
				$r .= $DSP->table_qcell($style, $DSP->input_radio($key, 'l', ($val == 'l') ? 1 : ''));
				$r .= $DSP->table_qcell($style, $DSP->input_radio($key, 'r', ($val == 'r') ? 1 : ''));
				$r .= $DSP->table_qcell($style, $DSP->input_radio($key, 'n', ($val != 'l' && $val != 'r') ? 1 : ''));
				$r .= $DSP->tr_c();
	        }
		}	
		return $r;
	}   
	// END 


	function myaccount_set_homepage_order($i)
	{
		global $DB, $DSP, $EXT;
		$r = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';

		if($panels = $this->check_for_panels())
		{			
			$id = $this->get_user_id();
			$prefs = array();		
					
			// This is all basically lifted from the function 'set_homepage_order'
			// Located in /system/cp/cp.myaccount.php
			      
	        $query = $DB->query("
	        	SELECT	* FROM exp_member_homepage 
	        	WHERE member_id = '".$DB->escape_str($id)."'
	        ");
						  
			foreach ($query->row as $key => $val)
			{
				if (in_array($key, $panels))
				{
					if ($val && $val != 'n')
					{
						$prefs[$key] = $val;
					}
				}
			}
			
			foreach ($prefs as $key => $val)
			{
				if (in_array($key, $panels))
				{
					$style = ($i++ % 2) ? 'tableCellOne' : 'tableCellTwo';
					
					$r .= $DSP->tr();
					$r .= $DSP->table_qcell($style, $DSP->qspan('defaultBold', $this->settings[$key.'_heading']));
									
					if ($val == 'l')
					{
						$r .= $DSP->table_qcell($style, $DSP->input_text($key.'_order', $query->row[$key.'_order'], '10', '3', 'input', '50px'));
						$r .= $DSP->table_qcell($style, NBS);
					}
					elseif ($val == 'r')
					{
						$r .= $DSP->table_qcell($style, NBS);
						$r .= $DSP->table_qcell($style, $DSP->input_text($key.'_order', $query->row[$key.'_order'], '10', '3', 'input', '50px'));
					}
					
					$r .= $DSP->tr_c();
				}
	        }
		}
        return $r;		
	}   
	// END
	

	function show_full_control_panel_end($out)
	{
		global $EXT, $IN;
		$out = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';
		
		// Only add our styles on the CP home screen
		if( $IN->GBL('C', 'GET') === FALSE && $IN->GBL('M', 'GET') === FALSE )
		{	
			$find= '</head>';
			$replace = '
			<style type="text/css">
				td.customPanel {
					background: url(../themes/cp_themes/default/images/box_bg.gif) repeat-x left top;
				}
				td.customPanel p {
					font-size: 12px;
					line-height: 16px;
					margin: 10px 0;
				}
				td.customPanel h3,
				td.customPanel strong,
				td.customPanel a:link,
				td.customPanel a:hover,
				td.customPanel a:visited {
					font-size: 12px;
				}
			</style>
			
			</head>
			';
			return str_replace($find, $replace, $out);
		}
		else
		{
			return $out;
		}
	}   
	// END
	
		
	function add_home_panel($method)
	{										
		global $DSP, $EXT;
		$r = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';

		// With this crazy hook we need to make sure that it's our method that's being called,
		// as other methods using this hook will *also* call this function.			
		$our_methods = array('custom_panel_one', 'custom_panel_two');
		if( in_array($method, $our_methods) && !empty($this->settings[$method]) && !empty($this->settings[$method.'_heading']) )
		{
			if ( ! class_exists('Typography') ) {
				require_once PATH_CORE.'core.typography'.EXT;
			}
			$format = new Typography;
			$text = $format->xhtml_typography($this->settings[$method]);
			
			$r .=
			$DSP->table('tableBorder', '0', '0', '100%').
			$DSP->tr().
			$DSP->table_qcell('tableHeading', $this->settings[$method.'_heading']).
			$DSP->tr_c().
			$DSP->table_qrow('tableCellTwo customPanel', $text).    
			$DSP->table_c();
			
			// The'control_panel_home_page_left/right_option' hook doesn't return data,
			// so we have to manually save our output in the last_call variable.
			// Otherwise subsequent calls to this hook with other functions
			// will overwrite what we just created.
			$EXT->last_call = $r;
		}
		return $r;
	}   
	// END 
		
   
	// --------------------------------
	//  Activate Extension
	// --------------------------------
	
	function activate_extension()
	{
	    global $DB;
	    
		$defaults = array();
		$defaults['custom_panel_one_heading'] = '';
		$defaults['custom_panel_one'] = '';
		$defaults['custom_panel_two_heading'] = '';
		$defaults['custom_panel_two'] = '';	    
	    
	    $hooks = array(
	    	'myaccount_homepage_builder' => 'myaccount_homepage_builder',
	    	'myaccount_set_homepage_order' => 'myaccount_set_homepage_order',
	    	'control_panel_home_page_left_option' => 'add_home_panel',
	    	'control_panel_home_page_right_option' => 'add_home_panel',
	    	'show_full_control_panel_end' => 'show_full_control_panel_end'
	    );
	    
	    foreach($hooks as $hook => $method)
	    {
		    $DB->query($DB->insert_string('exp_extensions',
		    	array(
					'extension_id' => '',
			        'class'        => "Cp_home_panels",
			        'method'       => $method,
			        'hook'         => $hook,
			        'settings'     => serialize($defaults),
			        'priority'     => 10,
			        'version'      => $this->version,
			        'enabled'      => "y"
					)
				)
			);	    
	    }
		
	    $DB->query("ALTER TABLE exp_member_homepage ADD `custom_panel_one` char(1), ADD `custom_panel_one_order` int(3) unsigned, ADD `custom_panel_two` char(1), ADD `custom_panel_two_order` int(3) unsigned");
	}
	// END


	// --------------------------------
	//  Update Extension
	// --------------------------------  
	
	function update_extension($current='')
	{
	    global $DB;
	    
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	    
	    if ($current < '1.0.1')
	    {
		}
	    
	    $DB->query("UPDATE exp_extensions 
	                SET version = '".$DB->escape_str($this->version)."' 
	                WHERE class = 'Cp_home_panels'");
	}
	// END
	
	
	// --------------------------------
	//  Disable Extension
	// --------------------------------
	
	function disable_extension()
	{
	    global $DB;
	    
	    $DB->query("DELETE FROM exp_extensions WHERE class = 'Cp_home_panels'");
	    $DB->query("ALTER TABLE exp_member_homepage DROP `custom_panel_one`, DROP `custom_panel_one_order`, DROP `custom_panel_two`, DROP `custom_panel_two_order`");
	    
	}
	// END


}
// END CLASS