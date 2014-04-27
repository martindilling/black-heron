<?php namespace Daytime;

use DateTimeZone;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Martindilling\Sunny\Sunny;

/**
 * 
 * @property       integer     $year
 * @property       integer     $week
 * @property-read  integer     $nextWeek
 * @property-read  Week        $nextWeekCopy
 * @property       Collection  $days
 * @property-read  Sunny       $monday
 * @property-read  Sunny       $tuesday
 * @property-read  Sunny       $wednesday
 * @property-read  Sunny       $thursday
 * @property-read  Sunny       $friday
 * @property-read  Sunny       $saturday
 * @property-read  Sunny       $sunday
 *
 */
class Week {

    /**
     * Latitude used for finding sunrise and sunset
     *
     * @var float
     */
    protected $latitude;

    /**
     * Longitude used for finding sunrise and sunset
     *
     * @var float
     */
    protected $longitude;

    /**
     * @var string
     */
    protected $timezone = 'Europe/Copenhagen';

    /**
     * @var integer
     */
    protected $year;

    /**
     * @var integer
     */
    protected $weekNo;

    /**
     * @var Collection
     */
    protected $days;


    ///////////////////////////////////////////////////////////////////
    //////////////////////////// CONSTRUCTOR //////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * @param integer $weekNo
     * @param integer $year
     * @param string  $timezone
     * @param float   $latitude
     * @param float   $longitude
     */
    function __construct($weekNo = null, $year = null, $timezone = null, $latitude = null, $longitude = null)
    {
        if (is_null($year)) {
            $year = Sunny::now()->year;
        }
        $this->setYear($year, false);

        if (is_null($weekNo)) {
            $weekNo = Sunny::now()->weekOfYear;
        }
        $this->setWeek($weekNo, false);

        if (!is_null($timezone)) {
            $this->setTimezone($timezone, false);
        }

        $this->latitude = $latitude;
        $this->longitude = $longitude;

        $this->updateDays();
    }

    /**
     * Update the days collection from this objects information.
     * 
     * @return Week
     */
    public function updateDays()
    {
        $paddedWeekNo = sprintf('%02d', $this->weekNo);
        $day = Sunny::parse($this->year . '-W' . $paddedWeekNo . '-1', $this->timezone);

        $this->days = new Collection;
        $this->days->put(Sunny::MONDAY,    $day);
        $this->days->put(Sunny::TUESDAY,   $day->copy()->next(Sunny::TUESDAY));
        $this->days->put(Sunny::WEDNESDAY, $day->copy()->next(Sunny::WEDNESDAY));
        $this->days->put(Sunny::THURSDAY,  $day->copy()->next(Sunny::THURSDAY));
        $this->days->put(Sunny::FRIDAY,    $day->copy()->next(Sunny::FRIDAY));
        $this->days->put(Sunny::SATURDAY,  $day->copy()->next(Sunny::SATURDAY));
        $this->days->put(Sunny::SUNDAY,    $day->copy()->next(Sunny::SUNDAY));

        $this->setLocation($this->latitude, $this->longitude);

        return $this;
    }


    ///////////////////////////////////////////////////////////////////
    ///////////////////////////// GETTERS /////////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Get a part of the Week object
     *
     * @param  string  $name
     *
     * @throws InvalidArgumentException
     *
     * @return integer|Collection|Sunny
     */
    public function __get($name)
    {
        switch ($name) {
            case 'latitude':
                return (float) $this->latitude;

            case 'longitude':
                return (float) $this->longitude;

            case 'year':
                return (int) $this->year;

            case 'week':
                return (int) $this->weekNo;

            case 'nextWeek':
                return (int) $this->getNextWeek();

            case 'nextWeekCopy':
                return $this->getNextWeekCopy();

            case 'days':
                return $this->days;

            case 'monday':
                return $this->getDay(Sunny::MONDAY);

            case 'tuesday':
                return $this->getDay(Sunny::TUESDAY);

            case 'wednesday':
                return $this->getDay(Sunny::WEDNESDAY);

            case 'thursday':
                return $this->getDay(Sunny::THURSDAY);

            case 'friday':
                return $this->getDay(Sunny::FRIDAY);

            case 'saturday':
                return $this->getDay(Sunny::SATURDAY);

            case 'sunday':
                return $this->getDay(Sunny::SUNDAY);

            default:
                throw new InvalidArgumentException(sprintf("Unknown getter '%s'!", $name));
        }
    }

    /**
     * Get a day from the week
     *
     * @param integer  $weekDay
     * 
     * @throws InvalidArgumentException
     * 
     * @return Sunny
     */
    public function getDay($weekDay)
    {
        if (!in_array($weekDay, range(Sunny::SUNDAY, Sunny::SATURDAY))) {
            throw new InvalidArgumentException(sprintf(
                "Unknown weekday [%s]! Must be between [%s] and [%s]!", $weekDay, Sunny::SUNDAY, Sunny::SATURDAY
            ));
        }
        
        return $this->days->get($weekDay);
    }

    /**
     * Get the number of the next week
     *
     * @return integer
     */
    public function getNextWeek()
    {
        if ($next = $this->week + 1 > Sunny::WEEKS_PER_YEAR) {
            $next = 1;
        }
        return $next;
    }

    /**
     * Get a new Week instance of the next week
     *
     * @return Week
     */
    public function getNextWeekCopy()
    {
        $weekNo = $this->week + 1;
        $year = $this->year;

        if ($weekNo > Sunny::WEEKS_PER_YEAR) {
            $weekNo = 1;
            $year++;
        }
        $clone = clone $this;
        return $clone->setWeek($weekNo, false)->setYear($year);
    }


    ///////////////////////////////////////////////////////////////////
    ///////////////////////////// SETTERS /////////////////////////////
    ///////////////////////////////////////////////////////////////////


    /**
     * Set a part of the Week object
     *
     * @param string          $name
     * @param string|integer  $value
     *
     * @throws InvalidArgumentException
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'latitude':
                $this->setLatitude($value);
                break;

            case 'longitude':
                $this->setLongitude($value);
                break;

            case 'timezone':
                $this->setTimezone($value);
                break;

            case 'year':
                $this->setYear($value);
                break;

            case 'week':
                $this->setWeek($value);
                break;

            default:
                throw new InvalidArgumentException(sprintf("Unknown setter '%s'!", $name));
        }
    }

    /**
     * Set the timezone
     * 
     * @param string  $timezone
     * @param bool    $updateDays
     * 
     * @throws InvalidArgumentException
     * 
     * @return Week
     */
    public function setTimezone($timezone, $updateDays = true)
    {
        if (!in_array($timezone, DateTimeZone::listIdentifiers())) {
            throw new InvalidArgumentException(sprintf("Unknown timezone '%s'!", $timezone));
        }

        $this->timezone = $timezone;

        if ($updateDays) {
            $this->updateDays();
        }

        return $this;
    }

    /**
     * Set the year
     * 
     * @param integer  $year
     * @param bool     $updateDays
     * 
     * @return Week
     */
    public function setYear($year, $updateDays = true)
    {
        $this->year = $year;

        if ($updateDays) {
            $this->updateDays();
        }

        return $this;
    }

    /**
     * Set the week number
     * 
     * @param integer  $week
     * @param bool     $updateDays
     * 
     * @return Week
     */
    public function setWeek($week, $updateDays = true)
    {
        $this->weekNo = $week;

        if ($updateDays) {
            $this->updateDays();
        }

        return $this;
    }

    /**
     * Set the instance's latitude
     *
     * @param  float $value
     *
     * @return Sunny
     */
    public function setLatitude($value)
    {
        $this->latitude = $value;

        return $this;
    }

    /**
     * Set the instance's longitude
     *
     * @param  float $value
     *
     * @return Sunny
     */
    public function setLongitude($value)
    {
        $this->longitude = $value;

        return $this;
    }

    /**
     * Set the location to all days
     *
     * @param  float $latitude
     * @param  float $longitude
     *
     * @return Week
     */
    public function setLocation($latitude, $longitude)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);

        $this->days->each(function($day) use ($latitude, $longitude)
        {
            $day->setLocation($latitude, $longitude);
        });

        return $this;
    }

}
