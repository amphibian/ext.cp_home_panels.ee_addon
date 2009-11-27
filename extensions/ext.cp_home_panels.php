<?php

if(!defined('EXT'))
{
	exit('Invalid file request');
}

class Cp_home_panels
{
	var $settings        = array();
	var $name            = 'Custom CP Home Panels';
	var $version         = '1.1';
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
	
	function settings_form($current)
	{	    			
	    global $DB, $DSP, $IN, $LANG, $PREFS;

		$site = $PREFS->ini('site_id');

		// Only grab settings for the current site
		if(isset($current[$site]))
		{
			$current = $current[$site];
		}
		
		// Get a list of Member Groups for this site
		$groups = $DB->query("SELECT group_id, group_title 
			FROM exp_member_groups 
			WHERE site_id = '".$DB->escape_str($site)."' 
			ORDER BY group_id ASC");
								
		// Start building the page
		$DSP->crumbline = TRUE;
		
		$DSP->title  = $LANG->line('extension_settings');
		$DSP->crumb  = $DSP->anchor(BASE.AMP.'C=admin'.AMP.'area=utilities', $LANG->line('utilities')).
		$DSP->crumb_item($DSP->anchor(BASE.AMP.'C=admin'.AMP.'M=utilities'.AMP.'P=extensions_manager', $LANG->line('extensions_manager')));
		$DSP->crumb .= $DSP->crumb_item($this->name);
		
		$DSP->right_crumb($LANG->line('disable_extension'), BASE.AMP.'C=admin'.AMP.'M=utilities'.AMP.'P=toggle_extension_confirm'.AMP.'which=disable'.AMP.'name='.$IN->GBL('name'));
		
		$DSP->body = $DSP->form_open(
			array(
				'action' => 'C=admin'.AMP.'M=utilities'.AMP.'P=save_extension_settings',
				'name'   => 'cp_home_panels',
				'id'     => 'cp_home_panels'
			),
			array('name' => get_class($this))
		);
		
		// $DSP->body .=	'<pre>'.print_r($current, TRUE).'</pre>';
		
		// Open the table
		$DSP->body .=   $DSP->table('tableBorder', '0', '', '100%');
		$DSP->body .=   $DSP->tr();
		$DSP->body .=   $DSP->td('tableHeading', '', '2');
		$DSP->body .=   $this->name;
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();
				
		// Give some brief instructions
		$DSP->body .=   $DSP->tr();
		$DSP->body .=   $DSP->td('default', '', '2');
		$DSP->body .=   '<div class="box" style="border-width: 0; margin: 0; padding: 10px 6px;">';
		$DSP->body .=   $LANG->line('instructions').' ';
		$DSP->body .=	$DSP->anchor(BASE.AMP.'C=myaccount'.AMP.'M=homepage'.AMP.'id='.$this->get_user_id(), $LANG->line('configure_homepage'), 'class="defaultBold"');
		$DSP->body .=   $DSP->div_c();
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();
		
		// Heading for first panel
		$DSP->body .=   $DSP->tr();
		$DSP->body .=   $DSP->td('tableHeadingAlt', '', '2');
		$DSP->body .=   'First Custom Panel';
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();		
		
		// First panel title
		$DSP->body .=   $DSP->tr();		
		$DSP->body .=   $DSP->td('tableCellTwo', '25%');
		$DSP->body .=   $LANG->line('custom_panel_heading');
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->td('tableCellTwo', '70%');
		$DSP->body .=   $DSP->input_text('custom_panel_one_heading', 
			(isset($current['custom_panel_one_heading'])) ? $current['custom_panel_one_heading'] : '', '', '', '', '500px');
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();
		
		// First panel content
		$DSP->body .=   $DSP->tr();	
		$DSP->body .=   $DSP->td('tableCellOne', '25%', '', '', 'top');
		$DSP->body .=   $LANG->line('custom_panel_content');
		$DSP->body .=   $DSP->qdiv('defaultLight', $LANG->line('formatting_info'));
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->td('tableCellOne', '70%');
		$DSP->body .=   $DSP->input_textarea('custom_panel_one', 
			(isset($current['custom_panel_one'])) ? $current['custom_panel_one'] : '');
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();	

		// First panel member groups
		$DSP->body .=   $DSP->tr();		
		$DSP->body .=   $DSP->td('tableCellTwo', '25%', '', '', 'top');
		$DSP->body .=   $LANG->line('member_groups');
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->td('tableCellTwo', '70%');
		foreach($groups->result as $group)
		{
			$DSP->body .= '<label style="margin-right: 8px;">';
			$DSP->body .= $DSP->input_checkbox('custom_panel_one_groups[]', $group['group_id'], 
				(isset($current['custom_panel_one_groups']) && !empty($current['custom_panel_one_groups']) 
				&& in_array($group['group_id'], $current['custom_panel_one_groups'])) ? 1 : '');
			$DSP->body .= ' '.$group['group_title'].'</label>';
		}
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();

		// Heading for second panel
		$DSP->body .=   $DSP->tr();
		$DSP->body .=   $DSP->td('tableHeadingAlt', '', '2');
		$DSP->body .=   'Second Custom Panel';
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();	
		
		// Second panel title
		$DSP->body .=   $DSP->tr();		
		$DSP->body .=   $DSP->td('tableCellTwo', '25%');
		$DSP->body .=   $LANG->line('custom_panel_heading');
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->td('tableCellTwo', '70%');
		$DSP->body .=   $DSP->input_text('custom_panel_two_heading', 
			(isset($current['custom_panel_two_heading'])) ? $current['custom_panel_two_heading'] : '', '', '', '', '500px');
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();
		
		// Second panel content
		$DSP->body .=   $DSP->tr();		
		$DSP->body .=   $DSP->td('tableCellOne', '25%', '', '', 'top');
		$DSP->body .=   $LANG->line('custom_panel_content');
		$DSP->body .=   $DSP->td_c();
				
		$DSP->body .=   $DSP->td('tableCellOne', '70%');
		$DSP->body .=   $DSP->input_textarea('custom_panel_two', 
			(isset($current['custom_panel_two'])) ? $current['custom_panel_two'] : '');
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();
		
		// Second panel member groups
		$DSP->body .=   $DSP->tr();		
		$DSP->body .=   $DSP->td('tableCellTwo', '25%', '', '', 'top');
		$DSP->body .=   $LANG->line('member_groups');
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->td('tableCellTwo', '70%');
		foreach($groups->result as $group)
		{
			$DSP->body .= '<label style="margin-right: 8px;">';
			$DSP->body .= $DSP->input_checkbox('custom_panel_two_groups[]', $group['group_id'], 
				(isset($current['custom_panel_two_groups']) && !empty($current['custom_panel_two_groups']) 
				&& in_array($group['group_id'], $current['custom_panel_two_groups'])) ? 1 : '');
			$DSP->body .= ' '.$group['group_title'].'</label>';
		}
		$DSP->body .=   $DSP->td_c();
		$DSP->body .=   $DSP->tr_c();			
			    
		// Wrap it up
		$DSP->body .=   $DSP->table_c();
		$DSP->body .=   $DSP->qdiv('itemWrapperTop', $DSP->input_submit());
		$DSP->body .=   $DSP->form_c();	  

	}

	
	function save_settings()
	{
		global $DB, $PREFS;

		$site = $PREFS->ini('site_id');		
		
		$settings = $this->get_settings(TRUE);
		
		unset($_POST['name']);
		
		// Remove dummy setting
		if(array_key_exists(0, $settings)) unset($settings[0]);
		
		$settings[$site] = $_POST;
		
		// Make sure there's at least one member group (super admins)
		$settings[$site]['custom_panel_one_groups'] = 
			(isset($_POST['custom_panel_one_groups'])) ? $_POST['custom_panel_one_groups'] : array('1');
		$settings[$site]['custom_panel_two_groups'] = 
			(isset($_POST['custom_panel_two_groups'])) ? $_POST['custom_panel_two_groups'] : array('1');		
			
		$data = array('settings' => addslashes(serialize($settings)));
		$update = $DB->update_string('exp_extensions', $data, "class = 'Cp_home_panels'");
		$DB->query($update);
	}

	
	function get_settings($all_sites = FALSE)
	{
		global $DB, $PREFS, $REGX;
		$site = $PREFS->ini('site_id');

		$get_settings = $DB->query("SELECT settings FROM exp_extensions WHERE class = 'Cp_home_panels' LIMIT 1");
		if ($get_settings->num_rows > 0 && $get_settings->row['settings'] != '')
        {
        	$settings = $REGX->array_stripslashes(unserialize($get_settings->row['settings']));
        	$settings = ($all_sites == TRUE) ? $settings : $settings[$site];
        }
        else
        {
        	$settings = array();
        }
        return $settings;		
	}	
	
		
	function get_user_id()
	{
		global $IN, $SESS;
		return ( ! $IN->GBL('id', 'GP')) ? $SESS->userdata('member_id') : $IN->GBL('id', 'GP');
	}
	
	
	function check_for_panels()
	{
		global $PREFS, $SESS;
		$site = $PREFS->ini('site_id');
		$group = $SESS->userdata['group_id'];
		
		$panels = array();
		
		if(!empty($this->settings[$site]['custom_panel_one_heading'])
			&& in_array($group, $this->settings[$site]['custom_panel_one_groups']))
		{
			$panels[] = 'custom_panel_one';
		}

		if(!empty($this->settings[$site]['custom_panel_two_heading']) 
			&& in_array($group, $this->settings[$site]['custom_panel_two_groups']))
		{
			$panels[] = 'custom_panel_two';
		}

		return $panels;
	}
	
	    
	function myaccount_homepage_builder($i)
	{
		global $DSP, $DB, $EXT, $PREFS;
		$r = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';

		if($panels = $this->check_for_panels())
		{
			$id = $this->get_user_id();
			$site = $PREFS->ini('site_id');
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
				$r .= $DSP->table_qcell($style, $DSP->qspan('defaultBold', $this->settings[$site][$key.'_heading']));
				$r .= $DSP->table_qcell($style, $DSP->input_radio($key, 'l', ($val == 'l') ? 1 : ''));
				$r .= $DSP->table_qcell($style, $DSP->input_radio($key, 'r', ($val == 'r') ? 1 : ''));
				$r .= $DSP->table_qcell($style, $DSP->input_radio($key, 'n', ($val != 'l' && $val != 'r') ? 1 : ''));
				$r .= $DSP->tr_c();
	        }
		}	
		return $r;
	}   


	function myaccount_set_homepage_order($i)
	{
		global $DB, $DSP, $EXT, $PREFS;
		$r = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';

		if($panels = $this->check_for_panels())
		{			
			$id = $this->get_user_id();
			$site = $PREFS->ini('site_id');
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
					$r .= $DSP->table_qcell($style, $DSP->qspan('defaultBold', $this->settings[$site][$key.'_heading']));
									
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
					padding: 10px;
				}
				td.customPanel p {
					font-size: 12px;
					line-height: 16px;
					margin: 0 0 10px;
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
	
		
	function add_home_panel($method)
	{										
		global $DSP, $EXT, $PREFS, $SESS;
		$r = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';

		$site = $PREFS->ini('site_id');
		$group = $SESS->userdata['group_id'];	
		
		// With this crazy hook we need to make sure that it's our method that's being called,
		// as other methods using this hook will *also* call this function.			
		$our_methods = array('custom_panel_one', 'custom_panel_two');
		if( in_array($method, $our_methods) 
			&& !empty($this->settings[$site][$method]) 
			&& !empty($this->settings[$site][$method.'_heading'])
			&& in_array($group, $this->settings[$site][$method.'_groups']) )
		{
			if ( ! class_exists('Typography') ) {
				require_once PATH_CORE.'core.typography'.EXT;
			}
			$format = new Typography;
			$text = $format->xhtml_typography($this->settings[$site][$method]);
			
			$r .=
			$DSP->table('tableBorder', '0', '0', '100%').
			$DSP->tr().
			$DSP->table_qcell('tableHeading', $this->settings[$site][$method.'_heading']).
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
		
   
	// --------------------------------
	//  Activate Extension
	// --------------------------------
	
	function activate_extension()
	{
	    global $DB;   
	    
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
			        'settings'     => serialize(array(0)),
			        'priority'     => 10,
			        'version'      => $this->version,
			        'enabled'      => "y"
					)
				)
			);	    
	    }
		
	    $DB->query("ALTER TABLE exp_member_homepage 
	    	ADD `custom_panel_one` char(1), 
	    	ADD `custom_panel_one_order` int(3) unsigned, 
	    	ADD `custom_panel_two` char(1), 
	    	ADD `custom_panel_two_order` int(3) unsigned");
	}


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
	    
	    if ($current < '1.1')
	    {
			// Migrate all current settings into site_id 1
			// and assign them to super admins
			$settings = $this->get_settings(TRUE);
			$new_settings = array();
			
			$new_settings[1]['custom_panel_one_heading'] = 
				(isset($settings['custom_panel_one_heading'])) ? $settings['custom_panel_one_heading'] : '';
			$new_settings[1]['custom_panel_one'] = 
				(isset($settings['custom_panel_one'])) ? $settings['custom_panel_one'] : '';		
			$new_settings[1]['custom_panel_one_groups'] = array(1);

			$new_settings[1]['custom_panel_two_heading'] = 
				(isset($settings['custom_panel_two_heading'])) ? $settings['custom_panel_two_heading'] : '';
			$new_settings[1]['custom_panel_two'] = 
				(isset($settings['custom_panel_two'])) ? $settings['custom_panel_two'] : '';		
			$new_settings[1]['custom_panel_two_groups'] = array(1);
			
			$data = array('settings' => addslashes(serialize($new_settings)));
			$update = $DB->update_string('exp_extensions', $data, "class = 'Cp_home_panels'");
			$DB->query($update);
		}
	    
	    $DB->query("UPDATE exp_extensions 
	                SET version = '".$DB->escape_str($this->version)."' 
	                WHERE class = 'Cp_home_panels'");
	}
	
	
	// --------------------------------
	//  Disable Extension
	// --------------------------------
	
	function disable_extension()
	{
	    global $DB;
	    
	    $DB->query("DELETE FROM exp_extensions WHERE class = 'Cp_home_panels'");
	    $DB->query("ALTER TABLE exp_member_homepage 
	    	DROP `custom_panel_one`, 
	    	DROP `custom_panel_one_order`, 
	    	DROP `custom_panel_two`, 
	    	DROP `custom_panel_two_order`");
	    
	}


}
// END CLASS