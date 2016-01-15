<?php
/**
 * @package Subscribe_Page_Acquisition_Form
 * @version 1.0
 */

/*
Plugin Name: Subscribe Page Acquisition Form
Description: Use the shortcode [subscribepageacquisitionform] to output a subscribe form on a page.
Author: Joe Gilbert
Version: 1.0
*/

/*
 * Simply returns the pdf request input form when called.
 */
function display_page_subscriber_acquisition_form($pre_form_content = '')
{

	/* Build the form and set it to the form_string variable. */
	$form_output_string = '
		<section class="page-subscriber-acquisition">
			<div class="inner-container">
				'. $pre_form_content .'
				<form action="'. $_SERVER['REQUEST_URI'] .'" method="post" name="pageSubscriberAcquisitionForm" class="single-input-form" id="page-subscriber-acquisition-form">
					<input name="pageSubscriberAcquisitionEmail" type="text" placeholder="Enter your email here">
					<input name="pageSubscriberAcquisitionSubmit" type="submit" value="Sign me up!">
				</form>
			</div>
		</section>';

	return $form_output_string;

} /* END function display_page_subscriber_acquisition_form */


/*
 * Either calls the display form function, or the process form function.
 */
function page_subscriber_acquisition_control()
{
  if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || !isset ($_POST['pageSubscriberAcquisitionSubmit']) )
  {
		return display_page_subscriber_acquisition_form();
  }
  else
  {
		return process_page_subscriber_acquisition_form();
  }
} /* END function page_subscriber_acquisition_control */


/*
 * Processes the form after user submission. It will ultimately either display any errors, or control emailing the pdf.
 */
function process_page_subscriber_acquisition_form()
{
	/* Initialize variables */
	$error = array();
	$subscriber_acquisition_email = isset($_POST["pageSubscriberAcquisitionEmail"]) ? $_POST["pageSubscriberAcquisitionEmail"] : '';

	/* Clean email address */
	if(strlen($subscriber_acquisition_email) <= 0){
		$error[] = "Please enter your email.";
	}else{
		if(!preg_match("/^([a-z0-9_]\.?)*[a-z0-9_]+@([a-z0-9-_]+\.)+[a-z]{2,3}$/i", stripslashes(trim($subscriber_acquisition_email)))) {$error[] = "Please enter a valid e-mail address.";}
	}

	/* Return errors found or writes the subscriber specific info to the master capture file. */
	if(sizeof($error) > 0)
	{
		$size = sizeof($error);
		$error_message = '<div class="form-errors-container">';

		for ($i=0; $i < $size; $i++)
		{
			if($i == 0)
				$error_message .= '<h3 class="form-error-title">Form Errors</h3>';

			$error_message .= '<p class="form-error">- '.$error[$i].'</p>';
		}

		$error_message .= '</div>';

		return display_page_subscriber_acquisition_form($error_message);
	}
	else
	{

		/* process_capture arguments: $captured_email, $captured_name, $capture_type, $capture_id */
		/* process_capture is in global functions file */
		process_capture($subscriber_acquisition_email, null, 'page-subscriber-acquisition', null);

		$confirmation_output_string = '
			<section class="page-subscriber-acquisition">
				<div class="inner-container">
					<h3 class=\"title\">Thanks for signing up!</h3>
					<p class=\"subtitle\">Our emails will come from <a href="mailto:Hello@InspireYourPeople.com">Hello@InspireYourPeople.com</a>.</p>
				</div>
			</section>
		';

		return $confirmation_output_string;

	}
} /* END function process_page_subscriber_acquisition_form */


/* Wordpress function call to bind the shortcode '[postcontentcapture]' to the functions above. */
add_shortcode( 'subscribepageacquisitionform', 'page_subscriber_acquisition_control' );

?>