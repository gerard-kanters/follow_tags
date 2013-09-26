<?php

elgg_register_event_handler('init', 'system', 'follow_tags_init');

function follow_tags_init() {
	//Register Libary File
	elgg_register_library('follow_tags', dirname(__FILE__) . '/lib/follow_tags_lib.php');
	elgg_load_library('follow_tags');

	//Register Save Action for saving and changing FollowTags
	elgg_register_action("follow_tags/save", dirname(__FILE__) . '/action/save.php');

	//Register a River Tab
	if (elgg_is_logged_in()) {
		$user = elgg_get_logged_in_user_entity();
		elgg_register_menu_item('filter', array(
			'name' => 'tags',
			'href' => "/activity/tags",
			'text' => elgg_echo("follow_tags:tab:title"),
			'priority' => 500,
			'contexts' => array('activity'),
			
		));

		//Register a Sidebar Item for Usersettings
		elgg_register_menu_item('page', array(
			'name' => "follow_tags",
			'text' => elgg_echo("follow_tags:sidebar:title"),
			'href' => "follow_tags/settings/" . $user->username,
			'context' => "settings",
		));
	}

	elgg_register_plugin_hook_handler("route", "activity", "follow_tags_route_activity_hook");
	
	//Register Pagehandlers
	elgg_register_page_handler('follow_tags', 'follow_tags_page_handler');
	elgg_register_page_handler('follow_tags_data', 'follow_tags_data_page_handler');

	//Register JS and CSS for custom taginput field
	$js_url = 'mod/follow_tags/vendors/jquery.tagsinput.min.js';
	elgg_register_js('jquery.tagsinput', $js_url, 'footer');
	elgg_load_js('jquery.tagsinput');
	
	// Register CSS for TagInput
	$css_url = 'mod/follow_tags/vendors/jquery.tagsinput.css';
	elgg_register_css('jquery.tagsinput', $css_url);
	elgg_load_css('jquery.tagsinput');
	
	// Add a JavaScript Initialization
	elgg_extend_view('js/elgg','follow_tags/js');
	
	//Trigger all Create Events for the Notification
	elgg_trigger_event('create', 'object', $object);
	 
	// Run the followtags_notofy function in event is triggerd
	elgg_register_event_handler('create', 'object', 'followtags_notify', 501);
}

function follow_tags_data_page_handler() {
	echo getAllTags();
	return true;
}

function follow_tags_route_activity_hook($hook, $type, $return_value, $params) {
	$result = $return_value;
	
	if ($page = elgg_extract("segments", $return_value)){
		if (elgg_extract(0, $page) == "tags") {
			include(dirname(__FILE__) . '/pages/activity/follow_tags.php');
			
			$result = false; // block other page handlers
		}
	}
	
	return $result;
}

function follow_tags_page_handler($page){
	require_once dirname(__FILE__) . '/pages/follow_tags/settings.php';
	return true;
}
