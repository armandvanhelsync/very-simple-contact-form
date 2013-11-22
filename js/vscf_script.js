jQuery(document).ready(function(){
	
	var form = jQuery("#vscf");
	var name = jQuery("#vscf_name");
	var nameInfo = jQuery("#nameInfo");
	var email = jQuery("#vscf_email");
	var emailInfo = jQuery("#emailInfo");
	var subject = jQuery("#vscf_subject");
	var subjectInfo = jQuery("#subjectInfo");
	
	var message = jQuery("#vscf_message");
	var messageInfo = jQuery("#messageInfo");
	
	//On blur
	name.blur(validateName);
	email.blur(validateEmail);
	subject.blur(validateSubject);
	//On key press
	name.keyup(validateName);
	message.keyup(validateMessage);
	//On Submitting
	form.submit(function(){
		if(validateName() & validateEmail() & validateSubject() & validateMessage())
			return true
		else
			return false;
	});
	
	//validation functions
	function validateEmail(){
		//testing regular expression
		var a = jQuery("#vscf_email").val();
		var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
		//if it's valid email
		if(filter.test(a)){
			email.removeClass("error");
			emailInfo.addClass("hide");
			return true;
		}
		//if it's NOT valid
		else{
			email.addClass("error");
			emailInfo.removeClass("hide");
			return false;
		}
	}
	function validateName(){
		//if it's NOT valid
		if(name.val().length < 3){
			name.addClass("error");
			nameInfo.removeClass("hide");
			return false;
		}
		//if it's valid
		else{
			name.removeClass("error");
			nameInfo.addClass("hide");
			return true;
		}
	}
	function validateSubject(){
		//if it's NOT valid
		if(subject.val().length < 3){
			subject.addClass("error");
			subjectInfo.removeClass("hide");
			return false;
		}
		//if it's valid
		else{
			subject.removeClass("error");
			subjectInfo.addClass("hide");
			return true;
		}
	}
	
	function validateMessage(){
		//it's NOT valid
		if(message.val().length < 10){
			message.addClass("error");	
			messageInfo.removeClass("hide");
			return false;
		}
		//it's valid
		else{			
			message.removeClass("error");
			messageInfo.addClass("hide");
			return true;
		}
	}
});