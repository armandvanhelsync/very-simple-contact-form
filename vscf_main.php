<?php
// the shortcode
function vscf_shortcode($atts) {
	extract(shortcode_atts(array(
		"email" => get_bloginfo('admin_email'),
		"subject" => '',
		"label_name" => __('Your name', 'verysimple') ,
		"label_email" => __('Your email', 'verysimple') ,
		"label_subject" => __('Subject', 'verysimple') ,
		"label_message" => __('Message', 'verysimple') ,
		"label_submit" => __('Submit', 'verysimple') ,
		"error_empty" => __("Please fill in all the required fields", "verysimple"),
		"error_your_name" => __('Please enter at least 3 characters', 'verysimple') ,
		"error_subject" => __('Please enter at least 3 characters', 'verysimple') ,
		"error_message" => __('Please enter at least 10 characters', 'verysimple') ,
		"error_email" => __("Please enter a valid email", "verysimple"),
		"success" => __("Thanks for your message! I will contact you as soon as I can.", "verysimple"),
	), $atts));

	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['send']) ) {
	
	// get posted data and sanitize them
		$post_data = array(
			'your_name'	=> vscf_clean_input (sanitize_text_field($_POST['your_name'])),
			'email'		=> sanitize_email($_POST['email']),
			'subject'		=> vscf_clean_input (sanitize_text_field($_POST['subject'])),
			'message'		=> vscf_clean_input (sanitize_text_field($_POST['message']))
			);
			
		$error = false;
		$required_fields = array("your_name", "email", "subject", "message");
		
		foreach ($required_fields as $required_field) {
			$value = stripslashes(trim($post_data[$required_field]));
		
		// displaying error message if validation failed for each input field
			if(((($required_field == "your_name") || ($required_field == "subject")) && strlen($value)<3) || 
			 	(($required_field == "message") && strlen($value)<10) || empty($value)) {
				$error_class[$required_field] = "error";
				$error_msg[$required_field] = ${"error_".$required_field};
				$error = true;
				$result = $error_empty;
			}
			$form_data[$required_field] = $value;
		}
		

		// sending email to admin
		if ($error == false) {
			$email_subject = "[" . get_bloginfo('name') . "] " . $form_data['subject'];
			$email_message = $form_data['message'] . "\n\nIP: " . vscf_get_the_ip();
			$headers  = "From: ".$form_data['your_name']." <".$form_data['email'].">\n";
			$headers .= "Content-Type: text/plain; charset=UTF-8\n";
			$headers .= "Content-Transfer-Encoding: 8bit\n";
			wp_mail($email, $email_subject, $email_message, $headers);
			$result = $success;
			$sent = true;
		}
	}

	// message 
	if($result != "") {
		$info .= '<div class="info">'.$result.'</div>';
	}

	// The Contact Form with error messages
	$email_form = '<form class="vscf" id="vscf" method="post" action="">
		<div>
			<label for="vscf_name">'.$label_name.': <span id="nameInfo" class="error '.((isset($error_class['your_name']) && ($error_class['your_name']))?"":" hide").'" >'.$error_your_name.'</span></label>
			<input type="text" name="your_name" id="vscf_name" class="'.$error_class['your_name'].'" maxlength="50" value="'.$form_data['your_name'].'" />
		</div>
		<div>
			<label for="vscf_email">'.$label_email.': <span id="emailInfo" class="error '.((isset($error_class['email']) && ($error_class['email']))?"":" hide").'" >'.$error_email.'</span></label>
			<input type="text" name="email" id="vscf_email" class="'.$error_class['email'].'"  maxlength="50" value="'.$form_data['email'].'" />
		</div>
		<div>
			<label for="vscf_subject">'.$label_subject.': <span id="subjectInfo" class="error '.((isset($error_class['subject']) && ($error_class['subject']))?"":" hide").'" >'.$error_subject.'</span></label>
			<input type="text" name="subject" id="vscf_subject" maxlength="50"  class="'.$error_class['subject'].'"  value="'.$subject.$form_data['subject'].'" />
		</div>
		<div>
			<label for="vscf_message">'.$label_message.': <span id="messageInfo"  class="error '.((isset($error_class['message']) && ($error_class['message']))?"":" hide").'" >'.$error_message.'</span></label>
			<textarea name="message" id="vscf_message" rows="10" class="'.$error_class['message'].'" >'.$form_data['message'].'</textarea>
			
		</div>
		<div>
			<input type="submit" value="'.$label_submit.'" name="send" id="vscf_send" />
		</div>
	</form>';
	
	if($sent == true) {
		return $info;
	} else {
		return $info.$email_form;
	}
} 
add_shortcode('contact', 'vscf_shortcode');
?>