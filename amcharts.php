<?php
/**
 * Load Google API library
 */
require_once 'vendor/autoload.php';

/**
 * Start session to store auth data
 */
session_start();

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
  '7daysAgo', // start date
  'today',  // end date
  'ga:sessions', //metric

  array(
    'dimensions'  => 'ga:date',
    'sort'        => '-ga:sessions',
    'max-results' => 20
  ) );
$sessions = $results->getRows();

// var_dump($sessions);

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

echo json_encode( $data );
