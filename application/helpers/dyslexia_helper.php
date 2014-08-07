<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/////////////////////////
// Table-related
/////////////////////////

if (!function_exists('create_dyslexia_table'))
{
	/** Creates the table with dyslexia data */
	function create_dyslexia_table($id = NULL)
	{
		$CI =& get_instance();
		base_table($id);
		$CI->table->set_heading(lang('participant'), lang('parent'), lang('statement'), lang('emt_score'), lang('klepel_score'), lang('vc_score'), lang('date'), lang('actions'));
	}
}

if (!function_exists('dyslexia_actions'))
{
	/** Possible actions for a dyslexia: prioritize and delete */
	function dyslexia_actions($dyslexia_id)
	{
		$edit_link = anchor('dyslexia/edit/' . $dyslexia_id, img_edit());
		$delete_link = anchor('dyslexia/delete/' . $dyslexia_id, img_delete(), warning(lang('sure_delete_dyslexia')));

		return implode(' ', array($edit_link, $delete_link));
	}
}
