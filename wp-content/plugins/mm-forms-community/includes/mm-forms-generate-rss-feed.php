<?php
/* 
 * This file will be used in mm-forms.php file
 */
$form_title = $cf['title'];
    $subject = __('RSS Feed of form submission', 'mm-forms');
    $copyright = __('This is copyrighted', 'mm-forms');
    $description = __('This is the MM Form', 'mm-forms');
    $creator =  __('Motionmill', 'mm-forms');
    $language =  __('en-us', 'mm-forms');
    $myfeed = new RSSFeed();
    $myfeed->SetChannel($x,
              $form_title,
              $description,
              $language,
              $copyright,
              $creator,
              $subject);
    global $wpdb;
	$contactform_submit = $wpdb->prefix . 'contactform_submit';
	$contactform_submit_data = $wpdb->prefix . 'contactform_submit_data';

    $sql = "SELECT *
            FROM ".$contactform_submit." cs, ".$contactform_submit_data." csd
            WHERE cs.fk_form_id = ".$id." and
            cs.id =  csd.fk_form_joiner_id order by submit_date desc";
    $data = $wpdb->get_results($sql);

    $i = 0;
    $rss = array();
    foreach ($data as $item) {
        $rss['Item'][] = array($item->form_key, $item->value);
        if ($item -> fk_form_joiner_id != $prev) {
            if($i != 0) {
                $des = "<table>";
                $fields = array();
                foreach($rss['Item'] as $entry){
                    $des   .= "<tr> <td>". $entry[0]." </td><td>   ".$entry[1] ."</td> </tr>" ;
                }
                $des .= "</table>";

                $myfeed->SetItem('',
                    "Submit Date: ".$submit_date,
                    $des);
            }
            $rss = array();
        }
        $submit_date = $item->submit_date;
        $prev = $item->fk_form_joiner_id;
        $i++;
    }

    $des = "<table>";
     foreach($rss['Item'] as $entry){
                    $des   .= "<tr> <td>". $entry[0]." </td><td>   ".$entry[1] ."</td> </tr>" ;
     }
    $des .= "</table>";
    $myfeed->SetItem('',
                    "Submit Date: ".$submit_date,
                    $des);
   echo $myfeed->output();
   exit;
?>
