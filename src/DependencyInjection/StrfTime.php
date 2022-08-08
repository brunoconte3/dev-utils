<?php

namespace DevUtils\DependencyInjection;

use DateTime;
use DateTimeZone;
use DateTimeInterface;
use Exception;
use IntlDateFormatter;
use IntlGregorianCalendar;
use InvalidArgumentException;

class StrfTime
{
    public static function strftime(string $format, $timestamp = null, ?string $locale = null): string
    {
        if (!($timestamp instanceof DateTimeInterface)) {
            $timestamp = is_int($timestamp) ? '@' . $timestamp : strval($timestamp);

            try {
                $timestamp = new DateTime($timestamp);
            } catch (Exception $e) {
                throw new InvalidArgumentException('$timestamp argument is neither a valid UNIX timestamp,
            a valid date-time string or a DateTime object.', 0, $e);
            }
        }
        if (empty($locale)) {
            $locale = setlocale(LC_TIME, '0');
        }
        $locale = preg_replace('/[^\w-].*$/', '', $locale);

        $intlFormats = [
            '%a' => 'EEE',
            '%A' => 'EEEE',
            '%b' => 'MMM',
            '%B' => 'MMMM',
            '%h' => 'MMM',
        ];

        $intlFormatter = function (DateTimeInterface $timestamp, string $format) use ($intlFormats, $locale) {
            $tz = $timestamp->getTimezone();
            $dateType = IntlDateFormatter::FULL;
            $timeType = IntlDateFormatter::FULL;
            $pattern = '';

            switch ($format) {
                    // %c = Preferred date and time stamp based on locale
                    // Example: Tue Feb 5 00:45:10 2009 for February 5, 2009 at 12:45:10 AM
                case '%c':
                    $dateType = IntlDateFormatter::LONG;
                    $timeType = IntlDateFormatter::SHORT;
                    break;
                case '%x':
                    $dateType = IntlDateFormatter::SHORT;
                    $timeType = IntlDateFormatter::NONE;
                    break;
                case '%X':
                    $dateType = IntlDateFormatter::NONE;
                    $timeType = IntlDateFormatter::MEDIUM;
                    break;

                default:
                    $pattern = $intlFormats[$format];
            }
            $calendar = IntlGregorianCalendar::createInstance();
            $calendar->setGregorianChange(PHP_INT_MIN);

            return (new IntlDateFormatter($locale, $dateType, $timeType, $tz, $calendar, $pattern))->format($timestamp);
        };

        $translateTable = [
            // Day
            '%a' => $intlFormatter,
            '%A' => $intlFormatter,
            '%d' => 'd',
            '%e' => function ($timestamp) {
                return sprintf('% 2u', $timestamp->format('j'));
            },
            '%j' => function ($timestamp) {
                // Day number in year, 001 to 366
                return sprintf('%03d', $timestamp->format('z') + 1);
            },
            '%u' => 'N',
            '%w' => 'w',

            // Week
            '%U' => function ($timestamp) {
                // Number of weeks between date and first Sunday of year
                $day = new DateTime(sprintf('%d-01 Sunday', $timestamp->format('Y')));
                return sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
            },
            '%V' => 'W',
            '%W' => function ($timestamp) {
                // Number of weeks between date and first Monday of year
                $day = new DateTime(sprintf('%d-01 Monday', $timestamp->format('Y')));
                return sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
            },

            // Month
            '%b' => $intlFormatter,
            '%B' => $intlFormatter,
            '%h' => $intlFormatter,
            '%m' => 'm',

            // Year
            '%C' => function ($timestamp) {
                // Century (-1): 19 for 20th century
                return floor($timestamp->format('Y') / 100);
            },
            '%g' => function ($timestamp) {
                return substr($timestamp->format('o'), -2);
            },
            '%G' => 'o',
            '%y' => 'y',
            '%Y' => 'Y',

            // Time
            '%H' => 'H',
            '%k' => function ($timestamp) {
                return sprintf('% 2u', $timestamp->format('G'));
            },
            '%I' => 'h',
            '%l' => function ($timestamp) {
                return sprintf('% 2u', $timestamp->format('g'));
            },
            '%M' => 'i',
            '%p' => 'A', // AM PM (this is reversed on purpose!)
            '%P' => 'a', // am pm
            '%r' => 'h:i:s A', // %I:%M:%S %p
            '%R' => 'H:i', // %H:%M
            '%S' => 's',
            '%T' => 'H:i:s', // %H:%M:%S
            '%X' => $intlFormatter, // Preferred time representation based on locale, without the date

            // Timezone
            '%z' => 'O',
            '%Z' => 'T',

            // Time and Date Stamps
            '%c' => $intlFormatter,
            '%D' => 'm/d/Y',
            '%F' => 'Y-m-d',
            '%s' => 'U',
            '%x' => $intlFormatter,
        ];

        $out = preg_replace_callback(
            '/(?<!%)%([_#-]?)([a-zA-Z])/',
            function ($match) use ($translateTable, $timestamp) {
                $prefix = $match[1];
                $char = $match[2];
                $pattern = '%' . $char;
                if ($pattern == '%n') {
                    return "\n";
                } elseif ($pattern == '%t') {
                    return "\t";
                }

                if (!isset($translateTable[$pattern])) {
                    throw new InvalidArgumentException(sprintf('Format "%s" is unknown in time format', $pattern));
                }

                $replace = $translateTable[$pattern];

                if (is_string($replace)) {
                    $result = $timestamp->format($replace);
                } else {
                    $result = $replace($timestamp, $pattern);
                }

                switch ($prefix) {
                    case '_':
                        // replace leading zeros with spaces but keep last char if also zero
                        return preg_replace('/\G0(?=.)/', ' ', $result);
                    case '#':
                    case '-':
                        // remove leading zeros but keep last char if also zero
                        return preg_replace('/^0+(?=.)/', '', $result);
                }

                return $result;
            },
            $format
        );

        $out = str_replace('%%', '%', $out);
        return $out;
    }
}
