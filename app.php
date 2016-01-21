<?php
/**
 * Load Google API library
 */
require_once 'vendor/autoload.php';
require_once 'src/analytics.php';
require_once 'src/user_data.php';

/**
 * Start session to store auth data
 */
session_start();

try {
  $server = $server_placeholder;
  $username = $username_placeholder;
  $password = $password_placeholder;
  //setting up connection to our database
  $DB = new PDO($server, $username, $password);
  //Throw an exception when an error is encountered in the query
  $DB->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  $DB->exec("SET NAMES 'utf8'");
  // var_dump($DB);
} catch (Exception $e) {
  echo "Could not connect to the database";
  exit;
}



/**
 * Set Google service account details
 */
$google_account = array(
  'email'   => $google_email_placholder,
  'key'     => file_get_contents( 'client_secrets.p12' ),
  'profile' => $google_profile_placholder

);

/**
 * Get Analytics API object
 */
function getService( $service_account_email, $key ) {
  // Creates and returns the Analytics service object.

  // Load the Google API PHP Client Library.
  require_once 'vendor/autoload.php';

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName( 'Google Analytics Dashboard' );
  $analytics = new Google_Service_Analytics( $client );

  // Read the generated client_secrets.p12 key.
  $cred = new Google_Auth_AssertionCredentials(
      $service_account_email,
      array( Google_Service_Analytics::ANALYTICS_READONLY ),
      $key
  );
  $client->setAssertionCredentials( $cred );
  if( $client->getAuth()->isAccessTokenExpired() ) {
    $client->getAuth()->refreshTokenWithAssertion( $cred );
  }

  return $analytics;
}

/**
 * Get Analytics API instance
 */
$analytics = getService(
  $google_account[ 'email' ],
  $google_account[ 'key' ]
);

/**
 * Query the Analytics data
 */
$results = $analytics->data_ga->get(
  'ga:' . $google_account[ 'profile' ], //profile id
  'yesterday', // start date
  'today',  // end date
  'ga:sessions', //metric

  array(
    'dimensions'  => 'ga:date',
    'sort'        => '-ga:sessions',
    'max-results' => 20
  )
);
$returned_data = $results->getRows();

// print_r($returned_data);

//Get the returned analytics data and save it to the database
Analytics::getAll($returned_data);



//Treehouse example

// try {
//   $results = $DB->query("SELECT date, sessions FROM sessions");
//   echo "Our query ran succesfully.";
// } catch (Exception $e) {
//   echo "Data could not be retrieved from the database.";
//   exit;
// }
