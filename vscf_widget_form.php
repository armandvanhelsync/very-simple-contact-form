<?php

// Start session for captcha validation
session_start();
$_SESSION['vscf-widget-rand'] = isset($_SESSION['vscf-widget-rand']) ? $_SESSION['vscf-widget-rand'] : rand(100, 999);

// The shortcode
function vscf_widget_shortcode($atts) {
	extract(shortcode_atts(array(
		"email_to" 			=> get_bloginfo('admin_email'),
		"label_name" 			=> __('Name', 'verysimple') ,
		"label_email" 			=> __('Email', 'verysimple') ,
		"label_subject" 		=> __('Subject', 'verysimple') ,
		"label_message" 		=> __('Message', 'verysimple') ,
		"label_sum"	 		=> __('Fill in number', 'verysimple') ,
		"label_submit" 			=> __('Submit', 'verysimple') ,
		"error_empty" 			=> __("Please fill in all the required fields", "verysimple"),
		"error_form_name" 		=> __('Please enter at least 3 characters', 'verysimple') ,
		"error_form_subject" 		=> __('Please enter at least 3 characters', 'verysimple') ,
		"error_form_message" 		=> __('Please enter at least 10 characters', 'verysimple') ,
		"error_form_sum" 		=> __("Please fill in the correct number", "verysimple"),
		"error_email" 			=> __("Please enter a valid email", "verysimple"),
		"success" 				=> __("Thanks for your message! I will contact you as soon as I can.", "verysimple"),
	), $atts));

	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['widget_form_send']) ) {
	
	// Get posted data and sanitize them
		$post_data = array(
			'form_name' 		=> sanitize_text_field($_POST['form_name']),
			'email' 			=> sanitize_email($_POST['email']),
			'form_subject' 		=> sanitize_text_field($_POST['form_subject']),
			'form_message' 		=> vscf_sanitize_text_field($_POST['form_message']),
			'form_sum'		 	=> sanitize_text_field($_POST['form_sum']),
			'form_firstname' 	=> sanitize_text_field($_POST['form_firstname']),
			'form_lastname' 	=> sanitize_text_field($_POST['form_lastname'])
		);
			
		$error = false;
		$required_fields = array("form_name", "email", "form_subject", "form_message");
		$security_fields = array("form_firstname", "form_lastname");
		$sum_fields = array("form_sum");

		foreach ($required_fields as $required_field) {
			$value = stripslashes(trim($post_data[$required_field]));
		
		// Displaying error message if validation failed for each input field
			if(((($required_field == "form_name") || ($required_field == "form_subject")) && strlen($value)<3) || 
			 	(($required_field == "form_message") && strlen($value)<10) || empty($value)) {
				$error_class[$required_field] = "error";
				$error_msg[$required_field] = ${"error_".$required_field};
				$error = true;
				$result = $error_empty;
			}
			$form_data[$required_field] = $value;
		}

		foreach ($sum_fields as $sum_field) {
			$value = stripslashes(trim($post_data[$sum_field]));

		// Displaying error message if validation failed for each input field
			if ($_POST['form_sum'] != $_SESSION['vscf-widget-rand']) { 
				$error_class[$sum_field] = "error";
				$error_msg[$sum_field] = ${"error_".$sum_field};
				$error = true;
				$result = $error_empty;
			}
			$form_data[$sum_field] = $value;
		}

		foreach ($security_fields as $security_field) {
			$value = stripslashes(trim($post_data[$security_field]));

		// Not sending message if validation failed for each input field
			if ((($security_field == "form_firstname") || ($security_field == "form_lastname")) && strlen($value)>0) {
				$error_class[$security_field] = "error";
				$error = true;
			}
			$form_data[$security_field] = $value;
		}

		// Sending message to admin
		if ($error == false) {
			$email_subject = "[" . get_bloginfo('name') . "] " . $form_data['form_subject'];
			$email_message = $form_data['form_name'] . "\n\n" . $form_data['email'] . "\n\n" . $form_data['form_message'] . "\n\nIP: " . vscf_get_the_ip();
			$headers  = "From: ".$form_data['form_name']." <".$form_data['email'].">\n";
			$headers .= "Content-Type: text/plain; charset=UTF-8\n";
			$headers .= "Content-Transfer-Encoding: 8bit\n";
			wp_mail($email_to, $email_subject, $email_message, $headers);
			$result = $success;
			$sent = true;
		}
	}

	// Message 
	if($result != "") {
		$info .= '<p class="vscf_info">'.$result.'</p>';
	}

	// The contact form with error messages
	$email_form = '<form class="vscf" id="vscf" method="post" action="">
		
		<p><label for="vscf_name">'.$label_name.': <span class="error '.((isset($error_class['form_name']))?"":" hide").'" >'.$error_form_name.'</span></label></p>
		<p><input type="text" name="form_name" id="vscf_name" class="'.$error_class['form_name'].'" maxlength="50" value="'.$form_data['form_name'].'" /></p>
		
		<p><label for="vscf_email">'.$label_email.': <span class="error '.((isset($error_class['email']))?"":" hide").'" >'.$error_email.'</span></label></p>
		<p><input type="text" name="email" id="vscf_email" class="'.$error_class['email'].'" maxlength="50" value="'.$form_data['email'].'" /></p>
		
		<p><label for="vscf_subject">'.$label_subject.': <span class="error '.((isset($error_class['form_subject']))?"":" hide").'" >'.$error_form_subject.'</span></label></p>
		<p><input type="text" name="form_subject" id="vscf_subject" class="'.$error_class['form_subject'].'" maxlength="50" value="'.$form_data['form_subject'].'" /></p>
		
		<p><label for="vscf_sum">'.$label_sum.' '. $_SESSION['vscf-widget-rand'].': <span class="error '.((isset($error_class['form_sum']))?"":" hide").'" >'.$error_form_sum.'</span></label></p>
		<p><input type="text" name="form_sum" id="vscf_sum" class="'.$error_class['form_sum'].'" maxlength="50" value="'.$form_data['form_sum'].'" /></p>
		
		<p><input type="text" name="form_firstname" id="vscf_firstname" class="'.$error_class['form_firstname'].'" maxlength="50" value="'.$form_data['form_firstname'].'" /></p>
		
		<p><input type="text" name="form_lastname" id="vscf_lastname" class="'.$error_class['form_lastname'].'" maxlength="50" value="'.$form_data['form_lastname'].'" /></p>
		
		<p><label for="vscf_message">'.$label_message.': <span class="error '.((isset($error_class['form_message']))?"":" hide").'" >'.$error_form_message.'</span></label></p>
		<p><textarea name="form_message" id="vscf_message" rows="10" class="'.$error_class['form_message'].'" >'.$form_data['form_message'].'</textarea></p>
		
		<p><input type="submit" value="'.$label_submit.'" name="widget_form_send" class="vscf_send" id="vscf_send" /></p>
	
	</form>';
	
	// Send message and erase captcha session or display form with error messages
	if($sent == true) {
		unset($_SESSION['vscf-widget-rand']);
		return $info;
	} else {
		return $info.$email_form;
	}
} 

add_shortcode('contact-widget', 'vscf_widget_shortcode');

?>