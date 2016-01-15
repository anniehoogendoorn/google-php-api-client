<?php

// Set the accessToken and Account-Id
$ga->setAccessToken($accessToken);
$ga->setAccountId('ga:10158492');

// Set the default params. For example the start/end dates and max-results
$defaults = array(
    'start-date' => date('Y-m-d', strtotime('-1 month')),
    'end-date' => date('Y-m-d'),
);
$ga->setDefaultQueryParams($defaults);

// Example1: Get visits by date
$params = array(
    'metrics' => 'ga:visits',
    'dimensions' => 'ga:date',
);
$visits = $ga->query($params);

// Example2: Get visits by country
$params = array(
    'metrics' => 'ga:visits',
    'dimensions' => 'ga:country',
    'sort' => '-ga:visits',
    'max-results' => 30,
    'start-date' => '2013-01-01' //Overwrite this from the defaultQueryParams
);
$visitsByCountry = $ga->query($params);

// Example3: Same data as Example1 but with the built in method:
$visits = $ga->getVisitsByDate();

// Example4: Get visits by Operating Systems and return max. 100 results
$visitsByOs = $ga->getVisitsBySystemOs(array('max-results' => 100));

// Example5: Get referral traffic
$referralTraffic = $ga->getReferralTraffic();

// Example6: Get visits by languages
$visitsByLanguages = $ga->getVisitsByLanguages();

?>
