<?php

namespace DevUtils\DependencyInjection;

use DateTime;
use DateTimeInterface;
use Exception;
use IntlDateFormatter;
use IntlGregorianCalendar;
use InvalidArgumentException;

class StrfTime
{
    private const INTL_FORMATS = [
        '%a' => 'EEE',
        '%A' => 'EEEE',
        '%b' => 'MMM',
        '%B' => 'MMMM',
        '%h' => 'MMM',
    ];
    private const SIMPLE_FORMATS = [
        '%d' => 'd',
        '%u' => 'N',
        '%w' => 'w',
        '%V' => 'W',
        '%m' => 'm',
        '%G' => 'o',
        '%y' => 'y',
        '%Y' => 'Y',
        '%H' => 'H',
        '%I' => 'h',
        '%M' => 'i',
        '%p' => 'A',
        '%P' => 'a',
        '%r' => 'h:i:s A',
        '%R' => 'H:i',
        '%S' => 's',
        '%T' => 'H:i:s',
        '%z' => 'O',
        '%Z' => 'T',
        '%D' => 'm/d/Y',
        '%F' => 'Y-m-d',
        '%s' => 'U',
    ];
    private const INTL_FORMAT_KEYS = ['%a', '%A', '%b', '%B', '%h', '%X', '%c', '%x'];

    private static function parseTimestamp(DateTimeInterface|int|string|null $timestamp): DateTimeInterface
    {
        if ($timestamp instanceof DateTimeInterface) {
            return $timestamp;
        }
        $timestamp = is_int($timestamp) ? '@' . $timestamp : (string) $timestamp;
        try {
            return new DateTime($timestamp);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                '$timestamp argument is neither a valid UNIX timestamp, ' .
                    'a valid date-time string or a DateTime object.',
                0,
                $e,
            );
        }
    }

    private static function parseLocale(?string $locale): string
    {
        if (empty($locale)) {
            $locale = setlocale(LC_TIME, '0');
        }
        return (string) preg_replace('/[^\w-].*$/', '', (string) $locale);
    }

    private static function formatIntl(DateTimeInterface $timestamp, string $format, string $locale): string
    {
        $tz = $timestamp->getTimezone();
        $dateType = IntlDateFormatter::FULL;
        $timeType = IntlDateFormatter::FULL;
        $pattern = '';
        if ($format === '%c') {
            $dateType = IntlDateFormatter::LONG;
            $timeType = IntlDateFormatter::SHORT;
        } elseif ($format === '%x') {
            $dateType = IntlDateFormatter::SHORT;
            $timeType = IntlDateFormatter::NONE;
        } elseif ($format === '%X') {
            $dateType = IntlDateFormatter::NONE;
            $timeType = IntlDateFormatter::MEDIUM;
        } else {
            $pattern = self::INTL_FORMATS[$format];
        }
        $calendar = IntlGregorianCalendar::createInstance();
        $calendar->setGregorianChange(PHP_INT_MIN);
        $formatter = new IntlDateFormatter($locale, $dateType, $timeType, $tz, $calendar, $pattern);
        return (string) $formatter->format($timestamp);
    }

    private static function formatPadded(DateTimeInterface $timestamp, string $dateFormat): string
    {
        return sprintf('% 2u', $timestamp->format($dateFormat));
    }

    private static function formatDayOfYear(DateTimeInterface $timestamp): string
    {
        return sprintf('%03d', ((int) $timestamp->format('z')) + 1);
    }

    private static function formatWeekNumber(DateTimeInterface $timestamp, string $dayName): string
    {
        $day = new DateTime(sprintf('%d-01 %s', $timestamp->format('Y'), $dayName));
        $diff = ((int) $timestamp->format('z')) - ((int) $day->format('z'));
        return sprintf('%02u', 1 + ($diff / 7));
    }

    private static function formatCentury(DateTimeInterface $timestamp): string
    {
        return (string) floor(((int) $timestamp->format('Y')) / 100);
    }

    private static function formatIsoYearShort(DateTimeInterface $timestamp): string
    {
        return substr($timestamp->format('o'), -2);
    }

    private static function getTranslateValue(
        string $pattern,
        DateTimeInterface $timestamp,
        string $locale
    ): string {
        if (in_array($pattern, self::INTL_FORMAT_KEYS, true)) {
            return self::formatIntl($timestamp, $pattern, $locale);
        }
        if (isset(self::SIMPLE_FORMATS[$pattern])) {
            return $timestamp->format(self::SIMPLE_FORMATS[$pattern]);
        }
        return match ($pattern) {
            '%e' => self::formatPadded($timestamp, 'j'),
            '%j' => self::formatDayOfYear($timestamp),
            '%U' => self::formatWeekNumber($timestamp, 'Sunday'),
            '%W' => self::formatWeekNumber($timestamp, 'Monday'),
            '%C' => self::formatCentury($timestamp),
            '%g' => self::formatIsoYearShort($timestamp),
            '%k' => self::formatPadded($timestamp, 'G'),
            '%l' => self::formatPadded($timestamp, 'g'),
            '%n' => "\n",
            '%t' => "\t",
            default => throw new InvalidArgumentException(
                sprintf('Format "%s" is unknown in time format', $pattern),
            ),
        };
    }

    private static function applyPrefix(string $prefix, string $result): string
    {
        return match ($prefix) {
            '_' => (string) preg_replace('/\G0(?=.)/', ' ', $result),
            '#', '-' => (string) preg_replace('/^0+(?=.)/', '', $result),
            default => $result,
        };
    }

    public static function strftime(
        string $format,
        DateTimeInterface|int|string|null $timestamp = null,
        ?string $locale = null
    ): string {
        $timestamp = self::parseTimestamp($timestamp);
        $locale = self::parseLocale($locale);
        $out = preg_replace_callback(
            '/(?<!%)%([_#-]?)([a-zA-Z])/',
            function (array $match) use ($timestamp, $locale): string {
                $prefix = $match[1];
                $pattern = '%' . $match[2];
                $result = self::getTranslateValue($pattern, $timestamp, $locale);
                return self::applyPrefix($prefix, $result);
            },
            $format,
        );
        return str_replace('%%', '%', (string) $out);
    }
}
