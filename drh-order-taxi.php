<?php
/**
 * Plugin Name: Order a Taxi Online
 * Plugin URI: http://davidreedhenley.com
 * Description: Order a taxi now or schedule one later!
 * Version: 1.0
 * Author: David Henley
 * Author URI: http://davidreedhenley.com
 * License: GPL2
 *
 *  Copyright 2014  David Henley  (email : davidrhenley@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

	Thanks to Manoj Kumar for his MK Google Directions plugin
	Plugin URI: https://manojranawpblog.wordpress.com/
 */

global $wp_version;

// Wordppress Version Check
if (version_compare($wp_version, '3.5', '<')) {
  exit($exit_msg . " Please upgrade your wordpress.");
}


/*
 * Add Stylesheet & Scripts for the plugin
 */

add_action('wp_enqueue_scripts', 'drh_scripts');

function drh_scripts() {
	$google_api_js = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places&language=' . get_option('mkgd_language', 'en');
	get_option('mkgd_units', 'imperial');

	wp_register_style('drh-css', plugins_url('/css/drh-styles.css', __FILE__));
	wp_enqueue_style('drh-css');	
	wp_enqueue_style('jquery-css');
	wp_enqueue_style( 'jquery-ui-datepicker','//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css' );

	wp_enqueue_script('mkgd-google-map-places', $google_api_js, array('jquery'));
	wp_enqueue_script('mask_js', plugins_url('/js/jquery.maskedinput.min.js', __FILE__), array('jquery'));
	wp_enqueue_script('ui', plugins_url('/js/jquery-ui.js', __FILE__), array('jquery'));
	wp_enqueue_script('drh-appointments', plugins_url('/js/drh-appointments.js', __FILE__), array('jquery'));
}

/*
 * Add Footer Content
 */

add_action('wp_footer', 'mkgd_footer');

function mkgd_footer() {
  wp_enqueue_script('mkgd-google-map', plugins_url('/js/mkgd-google-map.js', __FILE__), array('jquery'));
  ?>
<script type="text/javascript">

jQuery(window).load(function(){
	var url = window.location.href;
	var currentTime = new Date();
	var start = document.getElementById('origin').value;
	var end = document.getElementById('destination').value;
	var phone = document.getElementById('phone').value;
	var email = document.getElementById('email').value;
	var first_name = document.getElementById('first_name').value;
	var hourDesired = document.getElementById('hourdropdown').value;
	var mornOrnoon = document.getElementById('amorpm').value;
	var theminutes = document.getElementById('mindropdown').value;
	var hours = currentTime.getHours();
	var minutes = currentTime.getMinutes();
	var isIttoday = currentTime.getDate();
	var themonth = (currentTime.getMonth() + 1);
	var date = jQuery("#datepicker").datepicker('getDate');
	var day  = date.getDate();
	var month = date.getMonth() + 1;
	var year =  date.getFullYear();		
	var timeYouWantIt;
	var currentyear;	
	var whatshouldido;
	jQuery("#blk-btnLater").hide();
	
	var displaymap = "<?php echo get_option('display_map','no'); ?>";
	if (displaymap === 'no') { jQuery("#mkgd-map-canvas").hide(); } 
	else {jQuery("#mkgd-map-canvas").show();}
	jQuery('#directions').hide();
	var background = "<?php echo plugins_url('/images/noisy-texture-200x200.png', __FILE__); ?>";
	var background2 = "<?php echo plugins_url('/images/301.gif', __FILE__); ?>";
	background = "url("+background+")";
	background2 = "url("+background2+")";
	jQuery("#mkgd-wrap").css({"background-image": background});	
	jQuery('#my_loader').css({"background-image": background2});		
	var myurl			= "<?php echo plugins_url('/drh-email-order.php', __FILE__ ); ?>";
	var drh_getlocation	= "<?php echo plugins_url('/drh-ajax-handler.php', __FILE__); ?>";
	var drh_getlanguage = "<?php echo get_option('mkgd_language', 'en'); ?>";
	var drh_getunits	= "<?php echo get_option('mkgd_units', 'imperial'); ?>";
	var drh_getrate		= "<?php echo get_option('drh_rate','2'); ?>";
	var drh_getcurrency	= "<?php echo get_option('drh_currency','USD'); ?>";
	var drh_getdisplay	= "<?php echo get_option('display_map','no'); ?>";

});
  
jQuery(document).ready(function(){

	myurl 			=	"<?php echo plugins_url('/drh-email-order.php', __FILE__ ); ?>";
	drh_getlocation =	"<?php echo plugins_url('/drh-ajax-handler.php', __FILE__); ?>";
	drh_getlanguage =	"<?php echo get_option('mkgd_language', 'en'); ?>";
	drh_getunits 	=	"<?php echo get_option('mkgd_units', 'imperial'); ?>";
	drh_getrate		=	"<?php echo get_option('drh_rate','2'); ?>";
	drh_getcurrency =	"<?php echo get_option('drh_currency','USD'); ?>";
	drh_getdisplay	=	"<?php echo get_option('display_map','no'); ?>";	
	
	jQuery("#mkgd-map-canvas").hide();
	jQuery("#check_status_now").hide();
	jQuery("#btnCancel").hide();
	jQuery("input#btnStartOver2").hide();
	jQuery("#datepicker").datepicker({
		minDate:0,
		setDate:+0,
		defaultDate: new Date(),
		autoSize: true,
		showOn:"button",
		buttonImage:"<?php echo plugins_url('/images/calendar.gif', __FILE__); ?>",
		buttonImageOnly: true	
	});
	var todaysDate = new Date();
	var thisMonth = (todaysDate.getMonth() + 1);
	if (thisMonth<10){ thisMonth = "0"+thisMonth; }
    //format the date in the right format (add 1 to month because JavaScript counts from 0)
    formatDate = thisMonth + '/' + todaysDate.getDate() + '/' + todaysDate.getFullYear();
	jQuery('#datepicker').val(formatDate);		
	jQuery('input#phone').mask("(999) 999-9999");	
	jQuery('input#phone_status').mask("(999) 999-9999");		
	jQuery('#comments').hide();
	jQuery('.search-toggle').hide();
	jQuery('#next_step').hide();
	jQuery('.toHide').hide();
	jQuery('#my_loader').hide();
	jQuery('#wpmem_login').hide();
	jQuery('#hide_login').hide();
	jQuery('#vehicletype').hide();
	jQuery("#database_test").hide();	
	jQuery('#directions').hide();
	jQuery( "#radio" ).buttonset();
});	
	
    function initialize() {
      directionsDisplay = new google.maps.DirectionsRenderer();
      var charleston = new google.maps.LatLng(<?php echo get_option('mkgd_latitude', '32.7833'); ?>, 
										<?php echo get_option('mkgd_longitude', '-79.9333'); ?>);
      var mapOptions = {
        zoom:8,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: charleston,
		scrollwheel: false   
      }
      map = new google.maps.Map(document.getElementById('mkgd-map-canvas'), mapOptions);
      directionsDisplay.setMap(map);
    }

</script>
  <?php
}

/*
 * Initialize the map
 */

function mkgd_initialize() {
	$output 	= "";
	
	$output .= '<style>
		#mkgd-map-canvas{
		  width: '. get_option("mkgd_width", "300"). 'px;
		  height: '. get_option("mkgd_height", "300") .'px;
		}
	  </style>';	

	$output .= '
	
	<div id="mkgd-wrap">
		<div id="drh-choices">
			<div id="check_status">
			Already placed an order?&nbsp;<span id="drh-check_status" class="drh-chk_status">Check Status</span>
			</div>
			<br>
			<div id="check_status_now">
				<span id="drh-hide_status" class="drh-chk_status">Hide Status</span>
				<br><br>
				<form id="drhForm2">	
					<ul class="mkgd-form">
					<li>
					<label for="phone_status">'. __("Phone number") .'</label><br>
					<input id="phone_status" name="phone_status" type="text" size="15"/>					  		
					</li>					
					<li>
					<label for="conf_num">'. __("Confirmation Number") .'</label><br>
					<input id="conf_num" name="conf_num" type="number" size="15" class="numbersonly" /><br>			
					</li>
					<li>
					<input type="button" name="btnCheckStatus" id="btnCheckStatus" value="'. __("Check Status").'"/>
					</li>									
					<li>
					<div id="appt_details"></div>
					</li>
					<li>
					<input type="button" name="btnCancel" id="btnCancel" value="'. __("Cancel Appointment").'"/>					
					</li>
					<li>
					<input type="button" name="btnStartOver2" id="btnStartOver2" value="'. __("Start Over").'"/>
					</li>
					</ul>
				</form>
			</div>			
		</div>
		<div id="drh-hr"></div>
		<br>	
		<div id="order_form">
		<span id="drh-cab-title"><h4>Order a Cab</h4></span>	
<form id="drhForm">		
		<ul class="mkgd-form">
		  <li>
			<label for="first_name">'. __("Name"). '</label>
			<input id="first_name" name="first_name" type="text" size="50" class="alphaonly" />
		  </li>
		  <li>
			<label for="phone">'. __("Phone number") .'</label><br>
			<input id="phone" name="phone" type="text" size="14" />
		  </li>	
		  <li>
			<label for="email">'. __("Email") .'</label><br>
			<input id="email" type="email" name="email" />
		  </li>		  
		  <li>
			<label for="origin">'. __("Origin"). '</label>
			<input id="origin" name="origin" type="text" size="50" />
		  </li>  	
		  <li>
			<label for="origin">'. __("Destination"). '</label>
			<input id="destination" name="destination" type="text" size="50" />
		  </li>
		<li>
			<label>'. __("Departure Time"). '</label><br>		
		</li>
		 <li>
			<div id="radio">
			
			
				<input type="radio" id="btnNow" name="radio" checked="checked">
				<label for="btnNow">Now&nbsp;</label>
			   
			  <br> 	<br> 
			 				
				<input type="radio" id="btnLater" name="radio">
				<label for="btnLater">Later</label>
	
			</div>			
		</li>
		<li>
			<br>
			<br>
			<div id="blk-btnLater">
					<label for="departuredate">'. __("Departure Date&nbsp;") .'</label>
					<br>
					<input type="text" id="datepicker" readonly="true" />
					<br>	
					<label for="departuretime">'. __("Departure Time&nbsp;") .'</label>
					<br>
					<select name="hourdropdown" id="hourdropdown">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>		
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>		
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>		
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>					
					</select>
					<select name="mindropdown" id="mindropdown">
						<option value="00">00</option>
						<option value="05">05</option>
						<option value="10">10</option>
						<option value="15">15</option>		
						<option value="20">20</option>		
						<option value="25">25</option>		
						<option value="30">30</option>		
						<option value="35">35</option>		
						<option value="40">40</option>		
						<option value="45">45</option>		
						<option value="50">50</option>				
						<option value="55">55</option>	
					</select>
					<select name="amorpm" id="amorpm">
						<option value="AM">AM</option>
						<option value="PM">PM</option>		
					</select>	
			
			</div>	
		</li>
		<li><br>
			<input type="button" name="btnPreviewOrder" id="btnPreviewOrder" value="'. __("Preview Trip").'"/>
		</li>	
		<div id="my_loader"></div>
		</ul><!-- End .mkgd-form -->
</form>		
		<div id="mkgd-map-canvas"></div><!-- End #mkgd-map-canvas -->
		<div id="directions"></div><!-- End #directions -->
		<div id="next_step">
			<input type="button" name="btnPayNow" id="btnPayNow" value="'. __("Schedule Trip").'"/>
			<input type="button" name="btnStartOver" id="btnStartOver" value="'. __("Start Over").'"/>
		</div>	
			<div id="database_test"></div><br><br>
			<div id="back_to_top">Back to the top</div>
		</div><!-- End #order_form -->
	  </div><!-- End #mkgd-wrap -->';
  return $output;
}

/*
 * Add Shortcode Support
 */

function mkgd_shortcode($atts) {
  return mkgd_initialize();
}

add_shortcode('Order_Taxi', 'mkgd_shortcode'); // Add shortcode [MKGD]


/*
 * Include Admin
 */
require_once 'drh-admin.php';