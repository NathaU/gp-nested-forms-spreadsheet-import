<?php
/**
 * Plugin Name: GP Nested Forms CSV Upload
 * Version: 1.0
 * Author: Nathanael Urmoneit
 */

require 'class-gpnf-session-override.php';

//Parent form => [Parent Form ID, Nested Form Field ID, Nested Form ID, CSV Upload Form ID]
$csv_forms = [
	[2, 77, 11, 16],
	[12, 101, 13, 17],
];

add_action("wp_enqueue_scripts", function() use($csv_forms){
	wp_register_script("csv-upload", plugin_dir_url(__FILE__)."spreadsheet-upload.js");
	foreach($csv_forms as &$arr){
		$arr[4] = gravity_form($arr[3], false, false, true, null, false, 0, false);
	}
	wp_localize_script("csv-upload", "CSVUpload", ["forms"=>$csv_forms]);
	wp_enqueue_script("csv-upload");
});

function csv_button($args){
	$args["add_button"] .= ' <button type="button" class="gpnf-csv-upload"
		        data-bind="attr: { disabled: isMaxed }">
				CSV Upload
			</button>';
	return $args;
}
function csv_upload_101($form){
	$csv_branches = [];
	$session = new GPNF_CSV_Session(12);
	$path = $GLOBALS["csv_path"] . sanitize_file_name($_FILES["input_1"]["name"]);
	
	$lines = file($path);
	if(!empty(rgpost("input_3_1"))) array_shift($lines);
	foreach($lines as $csv_entry){
		$csv_entry = trim($csv_entry);
		if(empty($csv_entry)) continue;
		
		if(preg_match('//u', $csv_entry) === false){ //If there are non-UTF-8 chars, encode them
			$csv_entry = utf8_encode($csv_entry);
		}
		$csv_arr = explode(";", $csv_entry);
		
		$e = [
			"form_id"=>13,
			"created_by"=>get_current_user_id(),
			
			"1"=>"" //Form fields			
		];
		
		//Invoke action
		$post_cache = $_POST;
		do_action('gform_pre_submission_13',GFAPI::get_form(13));
		$new_keys = array_diff_key($_POST, $post_cache);
		foreach(array_keys($_POST) as $inp){
			if(preg_match("/^input_([0-9]+)(_[0-9]+)?/", $inp, $m)){
				$e[$m[1] . (@$m[2] ? '.'.$m[2] : '')] = $_POST[$m[0]];
			}
		}
		
		$entry_id = GFAPI::add_entry($e);
		
		$entry = new GPNF_Entry(GFAPI::get_entry($entry_id));
		$entry->set_parent_form(12);
		$entry->set_nested_form_field(101);
		$entry->set_expiration();
		
		$session->add_child_entry($entry_id);
	}
	
	$session->set_cookie();
	
	unlink($path);
	
	header("Location: ".$_SERVER["REQUEST_URI"]);
}

foreach($csv_forms as $arr){
	add_filter("gpnf_template_args_".$arr[0], "csv_button"); //Echo CSV button
	add_filter('gform_submit_button_'.$arr[3], '__return_false'); //Disable Submit button (use tingle's one instead)
	add_action("gform_post_process_".$arr[3], "csv_upload_".$arr[1], 1); //Func name: csv_upload_(nested form field id)
}

//retrieve file upload path
add_filter('gform_upload_path', function($path_info, $form_id) use(&$csv_path){
	$csv_path = $path_info["path"];
	return $path_info;
}, 1, 2);
