jQuery(window).load(function(){

	

//===================================================================================================	
	// pay for a cab now
//===================================================================================================	
	jQuery("input#btnPayNow").click(function() {        
		date = jQuery("#datepicker").datepicker('getDate');
		day  = date.getDate();
		month = date.getMonth() + 1;
		year =  date.getFullYear();		
		todaysDate = year+"-"+month+"-"+day;			
		hourDesired = document.getElementById('hourdropdown').value;
		mornOrnoon = document.getElementById('amorpm').value;
		theminutes = document.getElementById('mindropdown').value;			
		hours = currentTime.getHours();
		minutes = currentTime.getMinutes();
		isIttoday = currentTime.getDate();
		themonth = (currentTime.getMonth() + 1);	
		currentyear = currentTime.getFullYear();			
		whatshouldido = 'saveiguess';
		if (jQuery('#btnNow').is(':checked')){
			timeYouWantIt = hours+':'+minutes;
			todaysDate = currentyear+'-'+themonth+"-"+isIttoday;				
		} 
		else
		{	
			if (mornOrnoon == 'PM') {
				hourDesired	= parseInt(hourDesired)+parseInt(12);
				timeYouWantIt = hourDesired.toString()+":"+theminutes;
			} 
			else
			{
				timeYouWantIt = hourDesired+":"+theminutes;
			}
		}			
		var data = { 	whatshouldido	:	whatshouldido,
						your_first_name :	first_name,
						startLocation	:	start,
						endLocation		:	end,
						phoneNumber		:	phone,
						youremail		:	email,
						departureTime	:	timeYouWantIt,
						departureDate	:	todaysDate
					};
		jQuery.ajax({
			type	:	"POST",
			url		:	myurl,
			data	:	data,
			dataType:	'json',
			success	:	function(response,	status)	{
				if (response.success === true) {
					jQuery('#database_test').html('')
					.html("Appointment Saved Successfully, "+first_name+".<br> Please write down the following confirmation number to check the status.<br>"+response.last_id)
					.show();
				} else {
					jQuery('#database_test').html('').html("There was an error saving your appointment, "+first_name+". Please try and submit it again.").show();
				}									
			}
		});				
		jQuery("input#btnPayNow").hide();		
		return false;
	});	// end pay now

	
//===================================================================================================
	// cancel an appointment 
//===================================================================================================	
	jQuery("#btnCancel").click(function(){
		var phone_status	=	document.getElementById('phone_status').value;
		var conf_num		=	document.getElementById('conf_num').value;
		whatshouldido		=	'canceltheappt';
		var data			=	{ 	whatshouldido		:	whatshouldido,
									the_phone_status 	:	phone_status,
									the_conf_num		:	conf_num	};
		if (confirm('Are you sure you want to cancel this appointment?')) {
			jQuery.ajax({
						type	:	"POST",
						url		:	myurl,
						data	:	data,
						dataType:	'json',
						success	:	function(response,	status)	{
							if (response.success === true) {
								jQuery('#appt_details').html('')
								.html("Current Status: "+response.status)					
								.show();
							} else {
								jQuery('#appt_details').html('')
								.html("Sorry, but the appointment you entered was not found.")
								.show();					
							}
							jQuery("#btnCancel").hide();
						}
			});		
		}									

		jQuery("input#btnStartOver2").show();		
		return false;
	});	// end cancel appointment

	
//===================================================================================================	
	// check on the status of a previous appointment
//===================================================================================================	
	jQuery("#btnCheckStatus").click(function(){
		var phone_status	=	document.getElementById('phone_status').value;
		var conf_num		=	document.getElementById('conf_num').value;	
		whatshouldido		=	'lemmesee';
		var data			=	{ 	whatshouldido		:	whatshouldido,
									the_phone_status 	:	phone_status,
									the_conf_num		:	conf_num	};		
		if(phone_status	== ""){ 
			alert("Please enter your phone number."); 
			jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);	
			jQuery("input#phone_status").focus();
			return false;			
		}  			
		if(conf_num == ""){ 
			alert("Please enter your confirmation number."); 
			jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);	
			jQuery("input#conf_num").focus();
			return false;			
		}  	
		jQuery("#btnCheckStatus").hide();
		jQuery("#btnCancel").hide();
		jQuery.ajax({
			type	:	"POST",
			url		:	myurl,
			data	:	data,
			dataType:	'json',
			success	:	function(response,	status)	{
				if (response.success === true) {
					jQuery('#appt_details').html('')
					.html("Current Status: "+response.status+"<br>Name: "+response.first_name+"Phone: "+response.phone+"Email: "+response.email+"Origin: "+response.start_location+"Destination: "+response.end_location+"Departure Date: "+response.departure_date+"Departure Time: "+response.departure_time)
					.show();
					if (response.status === "Booked") { 
						jQuery("#btnCancel").show();	
					}
				} else {
					jQuery('#appt_details').html('')
					.html("Sorry, but the appointment you entered was not found.")
					.show();	
					jQuery("#btnCheckStatus").show();					
					
				}									
			}
		});			
		jQuery("input#btnStartOver2").show();	
		return false;
	});	// end check status


//===================================================================================================	
	// preview an appointment
//===================================================================================================	
	jQuery("#btnPreviewOrder").click(function() { 
		currentTime			=	new Date();
		start				=	document.getElementById('origin').value;
		end					=	document.getElementById('destination').value;
		phone				=	document.getElementById('phone').value;
		email				=	document.getElementById('email').value;
		first_name			=	document.getElementById('first_name').value;
		hourDesired			=	document.getElementById('hourdropdown').value;
		mornOrnoon			=	document.getElementById('amorpm').value;
		theminutes			=	document.getElementById('mindropdown').value;
		hours				=	currentTime.getHours();
		minutes				=	currentTime.getMinutes();
		isIttoday			=	currentTime.getDate();
		themonth			=	(currentTime.getMonth() + 1);
		date				=	jQuery("#datepicker").datepicker('getDate');
		day					=	date.getDate();
		month				=	date.getMonth() + 1;
		year				=	date.getFullYear();		
		var reg				=	/^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
		if(first_name == ""){ 
			jQuery("input#first_name").focus();				
			alert("Please enter your name."); 
			jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);	
			return false;			
		} 		
		if(phone == ""){ 
			jQuery("input#phone").focus();
			alert("Please enter your phone number."); 
			jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);	
			return false;
		}
		if (!reg.test(email)){
			jQuery("input#email").focus();		
			alert("Please enter your email address."); 
			jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);	
			return false; 
		}		
		if(start == ""){
			jQuery("input#origin").focus();	 
			alert("Please enter your origin.");
			jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);	
			return false;
		}
		if(end == ""){
			jQuery("input#destination").focus();	 
			alert("Please enter your destination.");
			jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);	
			return false;
		}
		// check to see if get a cab now is selected
		if ((!jQuery('#btnLater').is(':checked'))&&(!jQuery('#btnNow').is(':checked'))){
			alert("Please select Get a Cab Now or Get a Cab Later");
			return false;
		}
		if (jQuery('#btnLater').is(':checked')){
			// compare today with the date selected in the datepicker
			if (month == themonth)
			{	if (day == isIttoday)
				{	if(hours<13)
					{ 	if(mornOrnoon == 'AM')
						{	if (hours > hourDesired) 
							{	alert("Please select a time in the future");
								return false;						
							} 
							if (hours == hourDesired) 
							{	
								if (minutes > theminutes) 
								{
									alert("Please select a time in the future");
									return false;							
								}
							}
						} 				
					}
					if(hours>=12)
					{ // if it is after noon currently
						if(mornOrnoon == 'AM')
						{
							alert("Please select a time in the future");
							return false;						
						}
						if(mornOrnoon == 'PM')
						{	hours = hours - 12;
							if (hours > hourDesired) {
								alert("Please select a time in the future");
								return false;						
							} 						
							if (hours == hourDesired) 
							{	
								if (minutes > theminutes) 
								{
									alert("Please select a time in the future");
									return false;							
								}
							}		
						}					
					}				
				}
			}
		} // end If (jQuery('#btnLater').is(':checked')){
		jQuery("#btnPreviewOrder").hide();
		
		
/*		
		jQuery.post('<?php echo plugins_url('/drh-ajax-handler.php', __FILE__); ?>', 
			{origin		:	start, 
			destination	:	end, 
			language	:	'<?php echo get_option('mkgd_language', 'en'); ?>',
			units		:	'<?php echo get_option('mkgd_units', 'imperial'); ?>',
			rate		:	'<?php echo get_option('drh_rate','2'); ?>',
			currency	:	'<?php echo get_option('drh_currency','USD'); ?>',
			displaymap	:	'<?php echo get_option('display_map','no'); ?>'
			}, 
			function(data) {
				jQuery('#directions').html(data).show();
				displaymap = "<?php echo get_option('display_map','no'); ?>";
				*/
				
				
				
		jQuery.post(drh_getlocation, 
			{origin		:	start, 
			destination	:	end, 
			language	:	drh_getlanguage,
			units		:	drh_getunits,
			rate		:	drh_getrate,
			currency	:	drh_getcurrency,
			displaymap	:	drh_getdisplay
			}, 
			function(data) {
				jQuery('#directions').html(data).show();
				displaymap = drh_getdisplay;				
				if (displaymap === 'no') { jQuery("#mkgd-map-canvas").hide(); } 
				jQuery("#next_step").show();
				if (jQuery(".mkgd-error").is(':visible')) 
				{
					jQuery("input#btnPayNow").hide();
				} else 
				{
					jQuery("input#btnPayNow").show();
				}
				jQuery('html, body').animate({scrollTop: jQuery("#directions").offset().top},500);		
			}
		);		
		return false;
	});		// end preview an appointment

//===================================================================================================	
	// start over -- reset inputs
//===================================================================================================		
	jQuery("input#btnStartOver").click(function() {  
		jQuery("input#first_name").val('').focus();
		jQuery('input#phone').val('').mask("(999) 999-9999");
		jQuery('input#email').val('');
		jQuery("input#origin").val('');
		jQuery("input#destination").val('');
		var todaysDate = new Date();
		var thisMonth = (todaysDate.getMonth() + 1);
		if (thisMonth<10){ thisMonth = "0"+thisMonth; }
		jQuery("#database_test").html('').hide();
		//format the date in the right format (add 1 to month because JavaScript counts from 0)
		formatDate = thisMonth + '/' + todaysDate.getDate() + '/' + todaysDate.getFullYear();
		jQuery('#datepicker').val(formatDate);	
		jQuery('#comments').hide();
		jQuery('.search-toggle').hide();
		jQuery('#next_step').hide();
		jQuery("#blk-btnLater").hide();
		jQuery("#next_step").slideUp(10,function() {
			jQuery("#directions").html('').slideUp(1000,function(){
				jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);			
			});
		});
		jQuery("#btnPreviewOrder").show();
		return false;
	});	
	jQuery("input#btnStartOver2").click(function() {  
		jQuery("input#btnStartOver2").hide();
		jQuery("#btnCancel").hide();
		jQuery("#appt_details").html('').hide();
		jQuery("input#conf_num").val('');
		jQuery('input#phone_status').val('').mask("(999) 999-9999").focus();
		jQuery("#database_test").html('').hide();
		//format the date in the right format (add 1 to month because JavaScript counts from 0)
		jQuery('#comments').hide();
		jQuery('.search-toggle').hide();
		jQuery('#next_step').hide();
		jQuery("#blk-btnLater").hide();
		jQuery("#next_step").slideUp(10,function() {
			jQuery("#directions").html('').slideUp(1000,function(){
				jQuery('html, body').animate({scrollTop: jQuery("#mkgd-wrap").offset().top}, 1000);			
			});
		});
		jQuery("#btnCheckStatus").show();
		return false;
	});	
	
	jQuery("#btnNow").click(function(){	jQuery("#blk-btnLater").hide();	});
	jQuery("#btnLater").click(function(){ jQuery("#blk-btnLater").show(); });	
	
	jQuery("#drh-check_status").click(function(){
		jQuery("#check_status_now").slideDown();
		return false;
	});
	jQuery("#drh-hide_status").click(function(){
		jQuery("#check_status_now").slideUp();		
		return false;
	});	
	jQuery("div#back_to_top").click(function(){	window.scrollTo(0,0);	});			

	jQuery('.alphaonly').bind('keyup blur',function(){ 
		jQuery(this).val( jQuery(this).val().replace(/[^a-zA-Z ]/g,'') ); }
	);

	jQuery('.alphaonly').bind('keyup onblur',function(){ 
		jQuery(this).val( jQuery(this).val().replace(/[^a-zA-Z ]/g,'') ); }
	);

	jQuery('.numbersonly').bind('keyup blur',function(){ 
		jQuery(this).val( jQuery(this).val().replace(/[^0-9]/g,'') ); }
	);

	jQuery('.numbersonly').bind('keyup onblur',function(){ 
		jQuery(this).val( jQuery(this).val().replace(/[^0-9]/g,'') ); }
	);	
	
	jQuery("input#first_name").bind('keypress', function(e) {
		if(jQuery("input#first_name").val().length == 0){
			var k = e.which;
			var ok = k >= 65 && k <= 90 || // A-Z
				k >= 97 && k <= 122 || // a-z
				k >= 48 && k <= 57; // 0-9
			if (!ok){
				e.preventDefault();
			}
		}
	}); 	
});
