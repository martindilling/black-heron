<?php namespace Daytime;

use Martindilling\Sunny\Sunny;

class WeekPresenter {

    /**
     * Names of days of the week.
     *
     * @var array
     */
    protected static $days = array(
        Sunny::SUNDAY    => 'Sunday',
        Sunny::MONDAY    => 'Monday',
        Sunny::TUESDAY   => 'Tuesday',
        Sunny::WEDNESDAY => 'Wednesday',
        Sunny::THURSDAY  => 'Thursday',
        Sunny::FRIDAY    => 'Friday',
        Sunny::SATURDAY  => 'Saturday'
    );

    /**
     * Create an array with information for a single day
     * 
     * @param Week     $week
     * @param integer  $dayId
     * @return array
     */
    public static function prepareDay(Week $week, $dayId)
    {
        $day = $week->getDay($dayId);
        
        // Set variables for the calculations
        $min = $day->setTime(0,0,0)->getTimestamp();
        $max = $day->setTime(23,59,59)->getTimestamp();
        $total = $max - $min;
        $sunriseFloat = $day->getSunrise(SUNFUNCS_RET_TIMESTAMP) - $min;
        $sunsetFloat = $day->getSunset(SUNFUNCS_RET_TIMESTAMP) - $min;
        
        // Calculate percent numbers to be used for graphical presentation
        $sunrisePct = ($sunriseFloat / $total) * 100;
        $sunnyPct = (($sunsetFloat - $sunriseFloat) / $total) * 100;
        $sunsetPct = 100 - $sunrisePct - $sunnyPct;

        // Return the array
        return array(
            'weekday' => self::$days[$day->dayOfWeek],
            'date' => (string) $day,
            'dst' => $day->dst,
            'sunrise' => array(
                'pct' => $sunrisePct,
                'float' => $sunriseFloat,
                'string' => $day->sunrise,
            ),
            'sunset' => array(
                'pct' => $sunsetPct,
                'float' => $sunsetFloat,
                'string' => $day->sunset,
            ),
            'sunny' => array(
                'pct' => $sunnyPct,
                'float' => $sunsetFloat - $sunriseFloat,
                'string' => $day->sunnyTime,
            ),
        );
    }

    /**
     * Create an array with information for a week
     * 
     * @param Week $week
     * @return array
     */
    public static function getArray(Week $week)
    {
        return array(
            $week->week => array(
                'week' => $week->week,
                'days' => array(
                    'monday'    => self::prepareDay($week, Sunny::MONDAY),
                    'tuesday'   => self::prepareDay($week, Sunny::TUESDAY),
                    'wednesday' => self::prepareDay($week, Sunny::WEDNESDAY),
                    'thursday'  => self::prepareDay($week, Sunny::THURSDAY),
                    'friday'    => self::prepareDay($week, Sunny::FRIDAY),
                    'saturday'  => self::prepareDay($week, Sunny::SATURDAY),
                    'sunday'    => self::prepareDay($week, Sunny::SUNDAY),
                ),
            ),
        );
    }

} 
