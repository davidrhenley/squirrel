<?php

$response	=	array('success' => false); 

if ($_POST['origin'] != "" && $_POST['destination'] != "") {
	$origin = str_replace(' ', '+', $_POST['origin']);
	$destination = str_replace(' ', '+', $_POST['destination']);
	$language = $_POST['language'];
	$units = $_POST['units'];
	$rate = $_POST['rate'];
	$currency = $_POST['currency'];
	$thetotal = 1;	
	$displaymap = $_POST['displaymap'];
	$url = "http://maps.googleapis.com/maps/api/directions/json?origin=" . $origin . "&destination=" . $destination . "&sensor=false&language=" . $language . '&units=' . $units;

// sendRequest
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $body = curl_exec($ch);
  curl_close($ch);

  $json = json_decode($body);

  if ($json->status != 'ZERO_RESULTS') 
  {
	$response['success'] = true;	  
    $legs = $json->routes[0]->legs[0];
    $drivingSteps = $json->routes[0]->legs[0]->steps;
?>
	<h3>The estimated total cost is
	<span class="target">
<?php
	
	$thetotal = number_format((float)($legs->distance->text)*$rate, 2, '.', '');
	echo $thetotal." ".$currency;
	
	?>
	</span> 
	</h3>
	<h4>Total Distance: <?php echo $legs->distance->text; ?><br>
    Approx. time of journey: <?php echo $legs->duration->text; ?>
	</h4>	
	<p>The rate is	<span class="target"><?php echo $rate." ".$currency;?></span> per
<?php 
	if ($units == "metric") echo "kilometer";
	if ($units == "imperial") echo "mile";	
?>	
	<br>Fuel and additional passenger surcharges may apply</p>
<?php
	if ($displaymap == "yes") {
?>	
    <div id="exact_directions">
		<ul>
      <?php foreach ($drivingSteps as $drivingStep) { ?>
        <li><?php echo $drivingStep->html_instructions; ?></li>
        <?php
      }?>
	</div>
<?php 
	} //end $displaymap == yes
	} //end $json->status != 'ZERO_RESULTS'
else {
    echo "<h4 class=\"mkgd-error\">Google cannot find directions for the Origin and Destination addess entered by you.</h4>";
	 }
  } // end if ($_POST['origin'] != "" && $_POST['destination'] != "")
?>