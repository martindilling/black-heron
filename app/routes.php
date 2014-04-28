<?php

use Daytime\NavPresenter;
use Daytime\View;
use Daytime\Week;
use Daytime\WeekPresenter;
use Martindilling\Sunny\Sunny;

// Set default date formatting
Sunny::setToStringFormat('d-m-Y');

// Get an instance of View
$view = new View();

// The location information to get the result from
// TODO: Location is fixed to this for now, should be changeable 
$location = array(
    'timezone'  => 'Europe/Copenhagen',
    'latitude'  => 56.4647961,
    'longitude' => 9.993591,
);

/******************************************************************************
 * Redirect to: Show weeks one year forwards
 * With current week
 *****************************************************************************/
Macaw::get('/', function()
{
    $now = Sunny::now();

    $url = base_url() . '/week/' . $now->weekOfYear;

    header('Location: ' . $url, true, 302);
    exit();
});

/******************************************************************************
 * Show weeks one year forwards
 *****************************************************************************/
Macaw::get('/week/(:num)', function($segmentWeek) use ($view, $location)
{
    // Get a list of weeks for the navigation
    $now = Sunny::now();
    $navList = NavPresenter::weekList($now->weekOfYear, $now->year, $segmentWeek, '/week/{:week}');

    // Get an instance of the Week
    $week = new Week($segmentWeek, $navList[$segmentWeek]['year'], $location['timezone'], $location['latitude'], $location['longitude']);

    // Get the week data prepared for the view
    $weekData = WeekPresenter::getArray($week);

    // Data to be send to the view
    $viewData = array(
        'base_url'      => base_url(),
        'topNav'        => NavPresenter::topNav('base'),
        'navList'       => $navList,
        'weekData'      => $weekData,
    );

    // View the page
    echo $view->render('page/weeks.html', $viewData);
});


/******************************************************************************
 * Redirect to: Show weeks in a year
 * With current year and week
 *****************************************************************************/
Macaw::get('/year/week', function()
{
    $now = Sunny::now();

    $url = base_url() . '/year/' . $now->year . '/week/' . $now->weekOfYear;

    header('Location: ' . $url, true, 302);
    exit();
});

/******************************************************************************
 * Show weeks in a year
 *****************************************************************************/
Macaw::get('/year/(:num)/week/(:num)', function($segmentYear, $segmentWeek) use ($view, $location)
{
    // Get a list of weeks for the navigation
    $weekList = NavPresenter::weekList(1, $segmentYear, $segmentWeek, '/year/{:year}/week/{:week}');

    // Get an instance of the Week
    $week = new Week($segmentWeek, $segmentYear, $location['timezone'], $location['latitude'], $location['longitude']);

    // Get the week data prepared for the view
    $weekData = WeekPresenter::getArray($week);

    // Data to be send to the view
    $viewData = array(
        'base_url'      => base_url(),
        'topNav'        => NavPresenter::topNav('year.week'),
        'navList'       => $weekList,
        'weekData'      => $weekData,
    );

    // View the page
    echo $view->render('page/weeks.html', $viewData);
});


/******************************************************************************
 * Redirect to: Show all weeks in a year
 * With current year
 *****************************************************************************/
Macaw::get('/year', function()
{
    $now = Sunny::now();

    $url = base_url() . '/year/' . $now->year;

    header('Location: ' . $url, true, 302);
    exit();
});

/******************************************************************************
 * Show all weeks in a year
 *****************************************************************************/
Macaw::get('/year/(:num)', function($segmentYear) use ($view, $location)
{
    // Get a list of years for the navigation
    $yearList = NavPresenter::yearList($segmentYear, 4, '/year/{:year}');

    // Get an instance of the Week
    $week = new Week(1, $segmentYear, $location['timezone'], $location['latitude'], $location['longitude']);

    // Fill an array with Week instances for the next year
    $weeks = array($week);
    for ($i = 1; $i < 52; $i++) {
        $weeks[] = $weeks[$i-1]->nextWeekCopy;
    }

    // Get the weeks data prepared for the view
    $weekData = array();
    foreach ($weeks as $w) {
        $weekData = array_merge($weekData, WeekPresenter::getArray($w));
    }

    // Data to be send to the view
    $viewData = array(
        'base_url'      => base_url(),
        'topNav'        => NavPresenter::topNav('year'),
        'navList'       => $yearList,
        'weekData'      => $weekData,
    );

    // View the page
    echo $view->render('page/weeks.html', $viewData);
});

/******************************************************************************
 * Not found
 *****************************************************************************/
Macaw::error(function() use ($view)
{
    // Data to be send to the view
    $viewData = array(
        'base_url'      => base_url(),
        'topNav'        => NavPresenter::topNav(),
    );

    // View the page with a 404 status code
    header("HTTP/1.0 404 Not Found");
    echo $view->render('page/404.html', $viewData);
});

// Run the right route
Macaw::dispatch();
