<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('email_testinvite'))
{
	function email_testinvite($participant, $testinvite, $auto = FALSE)
	{
		$CI =& get_instance();
		$test = $CI->testInviteModel->get_test_by_testinvite($testinvite);
		$template = $CI->testTemplateModel->get_testtemplate_by_test($test->id, L::Dutch); // TODO: set to current language?

		$message = email_replace($template->template, $participant, NULL, NULL, $testinvite, $auto);

		$CI->email->clear();
		$CI->email->from(FROM_EMAIL, FROM_EMAIL_NAME);
		$CI->email->to(EMAIL_DEV_MODE ? TO_EMAIL_OVERRIDE : $participant->email);
		$CI->email->subject('Babylab Utrecht: Uitnoding voor vragenlijst');
		$CI->email->message($message);
		$CI->email->send();
		
		return sprintf(lang('testinvite_added'), name($participant), $test->name);
	}
}

if (!function_exists('email_replace'))
{
	function email_replace($view, $participant = NULL, $participation = NULL, $experiment = NULL, $testinvite = NULL, $auto = FALSE)
	{
		$CI =& get_instance();
		
		$message_data = array();
		$message_data['auto'] 				= $auto;
		
		if (!empty($participant)) 
		{
			$message_data['name']			= name($participant);
			$message_data['name_first']		= $participant->firstname;
			$message_data['name_parent']	= parent_name($participant);
			$message_data['gender']			= gender_child($participant->gender);
			$message_data['gender_pos']		= gender_pos($participant->gender);
			$message_data['gender_plural']	= gender_sex($participant->gender) . 's';
			$message_data['phone']			= $participant->phone;
			
			$participations = $CI->participationModel->get_participations_by_participant($participant->id, TRUE);
			if (count($participations) <= 1) 
			{
				$message_data['first_visit'] = TRUE;
			}
		}

		if (!empty($participation)) 
		{
			$message_data['appointment']	= format_datetime($participation->appointment);
		}
		
		if (!empty($experiment)) 
		{
			$message_data['type'] 			= $experiment->type;
			$message_data['duration'] 		= $experiment->duration;
			$message_data['duration_total'] = $experiment->duration + INSTRUCTION_DURATION;
			$message_data['description'] 	= $experiment->description;
		}
		
		if (!empty($participant) && !empty($experiment)) 
		{
			$data = get_min_max_days($participant, $experiment);
			
			$message_data['min_date'] 		= format_date($data['min_date_js']);
			$message_data['max_date'] 		= format_date($data['max_date_js']);
		}
		
		if (!empty($testinvite)) 
		{
			$testsurvey = $CI->testInviteModel->get_testsurvey_by_testinvite($testinvite);
			
			$message_data['survey_link'] 	= survey_link($testsurvey->limesurvey_id, $testinvite->token);
			$message_data['whennr'] 		= $testsurvey->whennr;
		}
		
		return $CI->load->view($view, $message_data, TRUE);
	}
}