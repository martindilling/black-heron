<?php namespace Daytime;

class NavPresenter {

    /**
     * Create an array of weeks to generate the navigation from
     * 
     * @param integer  $startWeek
     * @param integer  $year
     * @param integer  $activeWeek
     * @param string   $navFormat
     * @return array
     */
    public static function weekList($startWeek, $year, $activeWeek, $navFormat = '/week/{:week}')
    {
        $activeWeek = (int) $activeWeek;

        $weeksInYear = 52;
        for ($i = 0; $i < $weeksInYear; $i++) {
            $week = $startWeek + $i;

            if ($week > $weeksInYear) {
                $year++;
                $week = $startWeek + $i - $weeksInYear;
            }

            $uri = str_replace('{:year}', $year, $navFormat);
            $uri = str_replace('{:week}', $week, $uri);

            $list[$week] = array(
                'year'   => $year,
                'week'   => $week,
                'text'   => (string) $week,
                'active' => $week === $activeWeek ? true : false,
                'uri'    => $uri,
            );
        }

        return $list;
    }

    /**
     * Create an array of years to generate the navigation from
     *
     * @param        $activeYear
     * @param int    $span
     * @param string $navFormat
     * @return mixed
     */
    public static function yearList($activeYear, $span = 5, $navFormat = '/year/{:year}')
    {
        $activeYear = (int) $activeYear;

        for ($i = $activeYear-$span; $i <= $activeYear+$span; $i++) {
            $uri = str_replace('{:year}', $i, $navFormat);

            $list[$i] = array(
                'year'   => $i,
                'text'   => (string) $i,
                'active' => $i === $activeYear ? true : false,
                'uri'    => $uri,
            );
        }

        return $list;
    }

    /**
     * Create an array to generate the top navigation from
     * 
     * @param string $active
     * @return array
     */
    public static function topNav($active = null)
    {
        return array(
            'base' => array(
                'uri'    => '',
                'text'   => 'Show weeks one year forwards',
                'active' => $active == 'base' ? true : false,
            ),
            'year.week' => array(
                'uri'    => '/year/week',
                'text'   => 'Show weeks in a year',
                'active' => $active == 'year.week' ? true : false,
            ),
            'year' => array(
                'uri'    => '/year',
                'text'   => 'Show all weeks in a year',
                'active' => $active == 'year' ? true : false,
            ),
        );
    }

} 
