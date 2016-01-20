<?php
/**
 * Load Google API library
 */
require_once 'vendor/autoload.php';

/**
 * Start session to store auth data
 */
session_start();

try {
  $server = 'mysql:host=dbinstancetest.czbzihzhfgaj.us-east-1.rds.amazonaws.com;port=3306;dbname=testdb';
  $username = 'sq1';
  $password = 'mypassword';
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
  'email'   => 'test-1-service-account@decisive-force-119018.iam.gserviceaccount.com',
  'key'     => file_get_contents( 'client_secrets.p12' ),
  'profile' => '10158492'

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
  'today', // start date
  'today',  // end date
  'ga:sessions', //metric

  array(
    'dimensions'  => 'ga:date',
    'sort'        => '-ga:sessions',
    'max-results' => 20
  )
);
$sessions = $results->getRows();

// var_dump($sessions);
// print_r($sessions);

/**
 * Format and output data as JSON
 */
$data = array();
foreach( $sessions as $row ) {
  $data[] = array(
    'date'   => $row[0],
    'sessions'  => $row[1]
  );
}

// echo json_encode( $data );
$jsondata = json_encode( $data);

echo $jsondata;

// foreach($data as $item) {
//     $DB->exec("INSERT INTO sessions (date, sessions)");
// }





$sessions_date = $data['date'];
$sessions = $data['sessions'];
try {
  $DB->exec("INSERT INTO sessions (sessions_date, sessions)");

  } catch (Exception $e) {
    echo "Data could not be saved to the database.";
    exit;
  }


//Treehouse example

// try {
//   $results = $DB->query("SELECT date, sessions FROM sessions");
//   echo "Our query ran succesfully.";
// } catch (Exception $e) {
//   echo "Data could not be retrieved from the database.";
//   exit;
// }
