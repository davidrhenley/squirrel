<?php
/*
 * Register Admin Menu
 */
add_action('admin_menu', 'register_drh_menu_page');

function register_drh_menu_page() {
  add_menu_page('Taxi Ordering Settings', 'Order A Taxi', 'add_users', 'drh_adminpage', 'drh_admin_page', plugins_url('taxi-ordering-online/images/taxi-icon.jpg'), 99);
}

function table_install() {
	global $wpdb;
	$table_name = "drh_appointments";	
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	id int(11) NOT NULL AUTO_INCREMENT,
	first_name varchar(100) NOT NULL,
	phone varchar(30) NOT NULL,
	email varchar(100) NOT NULL,
	start_location varchar(255) NOT NULL,
    end_location varchar(255) NOT NULL,
    departure_time time NOT NULL,
	departure_date date NOT NULL,
	status varchar(30),
	appointment_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );

}

/*
 * Register settings for the plugin
 */
add_action( 'admin_init', 'register_settings' );
add_action( 'admin_init', 'table_install' );

function register_settings() {
	register_setting('mkgd-settings-group', 'drh_rate');
	register_setting('mkgd-settings-group', 'drh_currency');
	register_setting('mkgd-settings-group', 'mkgd_units');
	register_setting('mkgd-settings-group', 'mkgd_latitude');
	register_setting('mkgd-settings-group', 'mkgd_longitude');
	register_setting('mkgd-settings-group', 'mkgd_language');
	register_setting('mkgd-settings-group', 'mkgd_width');
	register_setting('mkgd-settings-group', 'mkgd_height');
	register_setting('mkgd-settings-group', 'display_map');
	register_activation_hook( __FILE__, 'table_install' );
}

/*
 * Define Options Page
 */

function drh_admin_page() {
	$languages = array(
	'ARABIC' => 'ar',
	'BASQUE' => 'eu',
	'BULGARIAN' => 'bg',
	'BENGALI' => 'bn',
	'CATALAN' => 'ca',
	'CZECH' => 'cs',
	'DANISH' => 'da',
	'GERMAN' => 'de',
	'GREEK' => 'el',
	'ENGLISH' => 'en',
	'ENGLISH (AUSTRALIAN)' => 'en-AU',
	'ENGLISH (GREAT BRITAIN)' => 'en-GB',
	'SPANISH' => 'es',
	'BASQUE' => 'eu',
	'FARSI' => 'fa',
	'FINNISH' => 'fi',
	'FILIPINO' => 'fil',
	'FRENCH' => 'fr',
	'GALICIAN' => 'gl',
	'GUJARATI' => 'gu',
	'HINDI' => 'hi',
	'CROATIAN' => 'hr',
	'HUNGARIAN' => 'hu',
	'INDONESIAN' => 'id',
	'ITALIAN' => 'it',
	'HEBREW' => 'iw',
	'JAPANESE' => 'ja',
	'KANNADA' => 'kn',
	'KOREAN' => 'ko',
	'LITHUANIAN' => 'lt',
	'LATVIAN' => 'lv',
	'MALAYALAM' => 'ml',
	'MARATHI' => 'mr',
	'DUTCH' => 'nl',
	'NORWEGIAN' => 'no',
	'POLISH' => 'pl',
	'PORTUGUESE' => 'pt',
	'PORTUGUESE (BRAZIL)' => 'pt-BR',
	'PORTUGUESE (PORTUGAL)' => 'pt-PT',
	'ROMANIAN' => 'ro',
	'RUSSIAN' => 'ru',
	'SLOVAK' => 'sk',
	'SLOVENIAN' => 'sl',
	'SERBIAN' => 'sr',
	'SWEDISH' => 'sv',
	'TAGALOG' => 'tl',
	'TAMIL' => 'ta',
	'TELUGU' => 'te',
	'THAI' => 'th',
	'TURKISH' => 'tr',
	'UKRAINIAN' => 'uk',
	'VIETNAMESE' => 'vi',
	'CHINESE (SIMPLIFIED)' => 'zh-CN',
	'CHINESE (TRADITIONAL)' => 'zh-TW'
	);
	$currencies = array(
	'USD - US Dollar' => 'USD',  
	'GBP - British Pound' => 'GBP',  
	'EUR - Euro' => 'EUR',  
	'INR - Indian Rupee' => 'INR',  
	'AUD - Australian Dollar' => 'AUD',  
	'CAD - Canadian Dollar' => 'CAD',  
	'AED - Emirati Dirham' => 'AED',  
	'MYR - Malaysian Ringgit' => 'MYR',
	'CHF - Swiss Franc' => 'CHF',
	'CNY - Chinese Yuan Renminbi' => 'CNY',
	'THB - Thai Baht' => 'THB'
	);
	$units = array(
    'Metric (Kilometers & Meters)' => 'metric',
    'Imperial (Miles & Feet)' => 'imperial',
	);	
	$maps = array(
    'No' => 'no',
    'Yes' => 'yes',
	);	
  ?>
  <div class="wrap">
    <h2><img src="<?php echo plugins_url('taxi-ordering-online/images/taxi-icon-small.png');?>"/>Order A Taxi</h2>    
    <form method="post" action="options.php">      
    <?php settings_fields('mkgd-settings-group'); ?>
    <?php do_settings_fields( 'drh_adminpage', 'mkgd-settings-group'); ?>
      <table class="widefat">
        <thead>
          <tr>
            <th>Settings</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <table>
                <tr>
                  <td><label for="rate">Rate: </label></td>
                  <td><input type="text" value="<?php echo get_option('drh_rate', '2'); ?>" size="3" name="drh_rate"></td>
                  <td><small>Default Rate is $2.</small></td>
                </tr>
                <tr>
                  <td><label for="currency">Currency: </label></td>
                  <td>
                    <select name="drh_currency">
                      <option value="">-- Select --</option>
                    <?php foreach($currencies as $currency => $code){ ?>
                      
                      <option <?php echo get_option('drh_currency') === $code ? 'selected="selected"': ''; ?> value="<?php echo $code; ?>"><?php echo $currency; ?></option>
                    <?php } ?>
                    </select>
                  </td>
                  <td><small>Default Currency is the US Dollar</small></td>
                </tr>				
                <tr>
                  <td><label for="latitude">Latitude: </label></td>
                  <td><input type="text" value="<?php echo get_option('mkgd_latitude', '43.6525'); ?>" size="33" name="mkgd_latitude"></td>
                  <td><small>Default latitudes for the Google Map <a href="http://itouchmap.com/latlong.html" title="Help" target="_blank" rel="nofollow"><strong>Help?</strong></a></small></td>
                </tr>
                <tr>
                  <td><label for="longitude">Longitude: </label></td>
                  <td><input type="text" value="<?php echo get_option('mkgd_longitude', '-79.3816667'); ?>" size="33" name="mkgd_longitude"></td>
                  <td><small>Default longitudes for the Google Map <a href="http://itouchmap.com/latlong.html" title="Help" target="_blank" rel="nofollow"><strong>Help?</strong></a></small></td>
                </tr>
                <tr>
                  <td><label for="language">Unit System: </label></td>
                  <td>
                    <select name="mkgd_units">
                      <option value="">-- Select --</option>
                      <?php foreach ($units as $key => $value) { ?>
                        <option <?php echo get_option('mkgd_units') === $value ? 'selected="selected"' : ''; ?> value="<?php echo $value; ?>"><?php echo $key; ?></option>
                      <?php } ?>
                    </select>
                  </td>
                  <td><small>Default unit system for the Google Map</small></td>
                </tr>				
                <tr>
                  <td><label for="language">Language: </label></td>
                  <td>
                    <select name="mkgd_language">
                      <option value="">-- Select --</option>
                    <?php foreach($languages as $language => $code){ ?>
                      
                      <option <?php echo get_option('mkgd_language') === $code ? 'selected="selected"': ''; ?> value="<?php echo $code; ?>"><?php echo $language; ?></option>
                    <?php } ?>
                    </select>
                  </td>
                  <td><small>Default language for the Google Map</small></td>
                </tr>
                <tr>
                  <td><label for="map-width">Map Width: </label></td>
                  <td><input type="text" value="<?php echo get_option('mkgd_width', '300'); ?>" size="33" name="mkgd_width"></td>
                  <td><small>Default width for the Google Map</small></td>
                </tr>				
                <tr>
                  <td><label for="map-width">Map Height: </label></td>
                  <td><input type="text" value="<?php echo get_option('mkgd_height', '300'); ?>" size="33" name="mkgd_height"></td>
                  <td><small>Default height for the Google Map</small></td>
                </tr>
                <tr>
                  <td><label for="display_map">Display Map: </label></td>
                  <td>
                    <select name="display_map">
                      <option value="">-- Select --</option>
                    <?php foreach($maps as $key => $code){ ?>
                      <option <?php echo get_option('display_map') === $code ? 'selected="selected"': ''; ?> value="<?php echo $code; ?>"><?php echo $key; ?></option>
                    <?php } ?>
                    </select>
                  </td>
                </tr>					
              </table>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th>Order a Taxi Settings</th>
          </tr>
        </tfoot>
      </table>

      <?php submit_button(); ?>

    </form>
    <table class="widefat">
        <tbody>
          <tr>
            <td>
              <h2>If you like and use this script, please consider buying me a beer -- a cheap and a simple way to give back! Thanks!</h2>
              <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RVZAWU7BDLRJ2" title="Donate" target="_blank"><img src="https://www.paypalobjects.com/en_GB/i/btn/btn_donateCC_LG.gif" alt="Donate" title="Donate" /></a>			
			</td>
          </tr>
        </tbody>
    </table>
  </div><!-- .wrap -->
  <?php
}