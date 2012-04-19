jQuery(document).ready(function() {	
	jQuery('div.mmf > form').ajaxForm({
		beforeSubmit: mmfBeforeSubmit,
		dataType: 'json',
		success: mmfProcessJson
	});

  jQuery('div.mmf > form').each(function(i, n) {
    mmfToggleSubmit(jQuery(n));
  });
});

// Exclusive checkbox
function mmfExclusiveCheckbox(elem) {
  jQuery(elem.form).find('input:checkbox[@name="' + elem.name + '"]').not(elem).removeAttr('checked');
}

// Toggle submit button
function mmfToggleSubmit(form) {
  var submit = jQuery(form).find('input:submit');
  if (! submit.length) return;
  
  var acceptances = jQuery(form).find('input:checkbox.mmf-acceptance');
  if (! acceptances.length) return;
  
  submit.removeAttr('disabled');
  acceptances.each(function(i, n) {
    n = jQuery(n);
    if (n.hasClass('mmf-invert') && n.is(':checked') || ! n.hasClass('mmf-invert') && ! n.is(':checked'))
      submit.attr('disabled', 'disabled');
  });
}

function mmfBeforeSubmit(formData, jqForm, options) {
	mmfClearResponseOutput();
	jQuery('img.ajax-loader', jqForm[0]).css({ visibility: 'visible' });  
    formData.push({name: '_mmf_is_ajax_call', value: 1});
	return true;
}

function mmfNotValidTip(into, message) {
  jQuery(into).append('<span class="mmf-not-valid-tip">' + message + '</span>');
	jQuery('span.mmf-not-valid-tip').mouseover(function() {
		jQuery(this).fadeOut('fast');
	});
	jQuery(into).find(':input').mouseover(function() {
		jQuery(into).find('.mmf-not-valid-tip').not(':hidden').fadeOut('fast');
	});
	jQuery(into).find(':input').focus(function() {
		jQuery(into).find('.mmf-not-valid-tip').not(':hidden').fadeOut('fast');
	});
}

function mmfProcessJson(data,responseText,jqForm) {
	var errorflag=0;
    var mmfResponseOutput = jQuery(data.into).find('div.mmf-response-output');
	mmfClearResponseOutput();
	if (data.invalids) {
		jQuery.each(data.invalids, function(i, n) {
			mmfNotValidTip(jQuery(data.into).find(n.into), n.message);
		});
		mmfResponseOutput.addClass('mmf-validation-errors');
		errorflag=1;
	}
	if (data.captcha) {
		jQuery.each(data.captcha, function(i, n) {
			jQuery(data.into).find(':input[@name="' + i + '"]').clearFields();
			jQuery(data.into).find('img.mmf-captcha-' + i).attr('src', n);
			var match = /([0-9]+)\.(png|gif|jpeg)$/.exec(n);
			jQuery(data.into).find('input:hidden[@name="_mmf_captcha_challenge_' + i + '"]').attr('value', match[1]);
		});
		//errorflag=1;
	}
	if (1 == data.spam) {
		mmfResponseOutput.addClass('mmf-spam-blocked');
	}
	if (1 == data.mailSent) {
		jQuery(data.into).find('form').resetForm().clearForm();
		mmfResponseOutput.addClass('mmf-mail-sent-ok');
	} else {
		mmfResponseOutput.addClass('mmf-mail-sent-ng');
		errorflag=1;
	}	
	var form = jqForm[0];	
	if(errorflag==0)
	{		
		var success_url=form._mmf_success_url.value;
		if(success_url)
		{
			if(success_url.indexOf('http://')==-1)
			{
				success_url="http://" + success_url;
			}							   
			//alert(success_url);
			window.location=success_url;
		}
	}
	else
	{
		var failure_url=form._mmf_failure_url.value;
		if(failure_url)
		{
			if(failure_url.indexOf('http://')==-1)
			{
				failure_url="http://" + failure_url;
			}							   
			//alert(failure_url);
			window.location=failure_url;
		}
	}
	mmfResponseOutput.append(data.message).fadeIn('fast');
}

function mmfClearResponseOutput() {
	jQuery('div.mmf-response-output').hide().empty().removeClass('mmf-mail-sent-ok mmf-mail-sent-ng mmf-validation-errors mmf-spam-blocked');
	jQuery('span.mmf-not-valid-tip').remove();
	jQuery('img.ajax-loader').css({ visibility: 'hidden' });
}