<?php
require_once 'DB.php';

$dsn = 'mysql://aaijrob:7sdhJHG8@localhost/aaijrob_app';
$options = array(
    'debug' => 2,
    'result_buffering' => false,
);

$db =& DB::connect($dsn, $options);
if (PEAR::isError($db)) {
    die($db->getMessage());
}
$db->setFetchMode(DB_FETCHMODE_ASSOC);


/** HTML_Quickform **/ 
require_once('HTML/QuickForm.php'); 

/** HTML_QuickForm_Renderer_Tableless **/ 
require_once '_l/classes/cssrenderer.class.php'; 

/*
if (!isset($type))
    $type = 'wrestling';
 */

$f  = new HTML_Quickform('contact', 'post'); 
$fr = new HTML_QuickForm_CSSRenderer(); 

function array_val_to_key($arr) {
    $new_arr = array();
    foreach ($arr as $v) 
        $new_arr[$v] = $v;
    return $new_arr;
}

$f->removeAttribute('name'); 

$f->addElement('html', '<br /><h4>How did you hear about us?</h4>');

$referrals = array(
    '',
    "Coach Referral",
    "Teammate Referral",
    "Google",
    "Facebook",
	"Internet Search",
	"Wrestling Magazine",
	"Wrestling Website",
	"TV Ad",
	"Radio Ad",
	"Mailing",
	"Other",
);

$f->addElement('select', 'referral', 'Referral:', array_val_to_key($referrals));

$f->addElement('html', '<hr/>');
$f->addElement('html', '<h4 >Are you a(n):</h4>');
$f->addElement('checkbox', 'athlete_flag', 'Athlete');
$f->addElement('checkbox', 'coach_flag', 'Coach');
$f->addElement('checkbox', 'parent_flag', 'Parent of Athelete');
$f->addElement('html', '<hr/>');

$f->addElement('text', 'first_name','First name:', array('maxlength'=>100));
$f->addElement('text', 'last_name', 'Last name:', array('maxlength'=>100));
$f->addElement('text', 'address_1', 'Address:', array('maxlength'=>255));
$f->addElement('text', 'address_2', 'Address cont\'d:', array('maxlength'=>255));
$f->addElement('text', 'city', 'City:', array('maxlength'=>60));
$f->addElement('text', 'state', 'State:', array('maxlength'=>20));
$f->addElement('text', 'postal_code', 'Postal Code:', array('maxlength'=>20));
$f->addElement('text', 'country',   'Country:', array('maxlength'=>60));
$f->addElement('text', 'email', 'Email:', array('maxlength'=>60));
$f->addElement('text', 'phone', 'Phone:', array('maxlength'=>20));


$f->addElement('textarea', 'comments', 'Questions/comments:', array('rows'=> 6, 'cols' => 30));

$f->addElement('submit', 'submit', 'Submit Request');


$f->addRule('first_name', 'Please enter your first name', 'required'); 
$f->addRule('first_name', 'Your first name cannot exceed 100 characters', 'maxlength', 100); 

$f->addRule('last_name', 'Please enter your last name', 'required', false); 
$f->addRule('last_name', 'Your last name cannot exceed 100 characters', 'maxlength', 100); 

$f->addRule('address_1', 'Your address cannot exceed 255 characters', 'maxlength', 255); 
$f->addRule('address_1', 'Please enter an address', 'required', false); 

$f->addRule('city', 'Please enter a city', 'required', false); 
$f->addRule('state', 'Please enter a state', 'required', false); 
$f->addRule('postal_code', 'Please enter a postal code', 'required', false); 

$f->addRule('email', 'Please enter an e-mail address', 'required', '', 'client'); 
$f->addRule('email', 'Enter a valid e-mail address', 'email', '', 'client'); 

$f->addRule('phone', 'Please enter a phone number', 'required', false); 
$f->addRule('phone', 'Phone cannot exceed 20 characters', 'maxlength', 20); 

$f->addRule('country', 'Please enter a country', 'required', false); 
$f->addRule('country', 'Country cannot exceed 20 characters', 'maxlength', 60); 

$valid = $f->validate();

if ($valid && empty($_POST['referral'])) {
    if (!empty($referrals)) $f->setElementError('referral', 'Please select one of these options for how you heard about us.', 'required', false); 
    else $f->setElementError('website', 'Please select one of these options for how you heard about us.', 'required', false); 
    $valid = false;
}

$invalid_post = isset($_POST['first_name']) && !$valid;

if ($valid && ($clean = $f->exportValues())) { 
    
    $rs = $db->autoExecute(
        'brochure_request',
        array(
            'downloaded_flag'   => 0,
            'timestamp'         => date('Y-m-d H:i:s'),
            'ip_address'        => $_SERVER['REMOTE_ADDR'],
            'sport'             => $type,
            'first_name'        => $_POST['first_name'],
            'last_name'         => $_POST['last_name'],
            'address_1'         => $_POST['address_1'],
            'address_2'         => $_POST['address_2'],
            'city'              => $_POST['city'],
            'state'             => $_POST['state'],
            'postal_code'       => $_POST['postal_code'],
            'country'           => $_POST['country'],
            'email'             => $_POST['email'],
            'phone'             => $_POST['phone'],
            'athlete_flag'      => isset($_POST['athlete_flag']) ? 1 : 0,
            'coach_flag'        => isset($_POST['coach_flag'])   ? 1 : 0,
            'parent_flag'       => isset($_POST['parent_flag'])  ? 1 : 0,
            'referral'          => $_POST['referral'],
        	'comments'          => $_POST['comments'],
        ),
        DB_AUTOQUERY_INSERT
    );

    if (PEAR::isError($rs)) {
        print "There was an error processing your request, please try again later";
    } else {
        $mail = 'info@jrobinsoncamps.com';
       // mail($mail, 'Brochure Request Form', 'A new brochure request has been received');
        echo '<strong>Thank you for your request.</strong>';
    }

} else { 
    print '<link type="text/css" rel="stylesheet" media="screen" href="forms.css" />';
    if ($invalid_post) {
        print 'Please fill in all the required fields';

    }
    $f->accept($fr); 
    echo $fr->toHtml();
}
