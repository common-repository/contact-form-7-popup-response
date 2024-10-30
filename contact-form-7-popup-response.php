<?php
/*
    Plugin Name: Contact Form 7 Popup Response
    Plugin URI: http://huseyinbabal.net
    Description: Displays Contact Form 7 response in popup
    Version: 1.0
    Author: Hüseyin BABAL
    Author URI: http://huseyinbabal.net
    Tags: contact form 7 popup, contact form 7
*/
class Contact_Form_7_Popup_Response {

    var $version = '1.0';
    var $plugin_url = '';

    // Constructor for initialization required paths and hooks
    function Contact_Form_7_Popup_Response() {
    	$this->plugin_url = plugins_url() . DIRECTORY_SEPARATOR . basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR;
        register_activation_hook( __FILE__, array( &$this, 'on_plugin_init' ) );
        add_action( 'wp_print_styles', array( &$this, 'loadPopup' ) );
        add_action( 'wp_footer', array( &$this, 'cf7Popupify' ) );
    }

    // Check whether contact form already installed/activated or not
    function on_plugin_init() {
        if ( !(is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) ) {
			die($this->showDependencyMessages());
		}
    }

    // We need colorbox for displaying popups
    function loadPopup() {
   		wp_enqueue_style('popupStyle', $this->plugin_url . DIRECTORY_SEPARATOR . 'colorbox' . DIRECTORY_SEPARATOR . 'colorbox.css');
    	wp_enqueue_script('popupScript', $this->plugin_url . DIRECTORY_SEPARATOR . 'colorbox' . DIRECTORY_SEPARATOR . 'jquery.colorbox-min.js', array('jquery'), '', true );
    }

    // Run function when user clicks contact form 7 submit button
    function cf7Popupify() {
    ?>
		<script>
			jQuery(".wpcf7-submit").click(function(event) {
				jQuery( document ).ajaxComplete(function() {
					var responseType = jQuery(".wpcf7-response-output").hasClass("wpcf7-validation-errors") ? "red" : "green";
					var responseHtml = "";
					
					// Iterate all error tips on elements and show that errors in the popup
					jQuery(".wpcf7-not-valid-tip").each(function(index) {
						jQuery(this).hide();
						responseHtml += "<li>*" + jQuery(this).html() + "</li>"; 
					});

					if (responseHtml.length == 0) {
						responseHtml = "<li>" + jQuery(".wpcf7-response-output").html() + "</li>";
					}
					
					jQuery.colorbox({
						html: '<div style="color:' + responseType + '; padding:30px; background:#fff;"><ol>' + responseHtml + '</ol></div>',
						onClosed: function() {
							if (responseType == "green") {
								window.location = "<?php echo get_site_url();?>";
							}
						}
					});
					jQuery(".wpcf7-response-output").css( "display", "none" );
				});
			});
		</script>
	<?php
    }

    // Simple message for warning user to get required plugins
    function showDependencyMessages() {
  		return 'In order to use <strong>Contact Form 7 Popup Response</strong> you need to have <em><strong><a href="plugin-install.php?tab=search&s=contact+form+7">Contact Form 7</a></strong></em> plugins installed and activated.';
	}
}

//create an instance of plugin
if ( class_exists( 'Contact_Form_7_Popup_Response' ) ) {
    if ( !isset( $cf7fr ) ) {
        $cf7fr = new Contact_Form_7_Popup_Response;
    }
}