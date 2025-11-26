<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * TimeHelper - Centralized time handling for consistent timezone management
 * 
 * This helper ensures all timestamps are accurate and consistent throughout the system.
 * All time operations should use this helper to maintain timezone consistency.
 */
class TimeHelper
{
    /**
     * Get the application timezone.
     * 
     * @return string
     */
    public static function getTimezone(): string
    {
        return config('app.timezone', 'Asia/Manila');
    }

    /**
     * Get current date and time in application timezone.
     * 
     * @return Carbon
     */
    public static function now(): Carbon
    {
        return Carbon::now(self::getTimezone());
    }

    /**
     * Get current date in application timezone.
     * 
     * @return Carbon
     */
    public static function today(): Carbon
    {
        return Carbon::today(self::getTimezone());
    }

    /**
     * Get current date as string (Y-m-d format) in application timezone.
     * 
     * @return string
     */
    public static function todayString(): string
    {
        return self::today()->format('Y-m-d');
    }

    /**
     * Parse a date string in application timezone.
     * 
     * @param string|null $date
     * @return Carbon|null
     */
    public static function parseDate(?string $date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date, self::getTimezone());
        } catch (\Exception $e) {
            \Log::error('TimeHelper::parseDate error', [
                'date' => $date,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Parse a datetime string in application timezone.
     * 
     * @param string|null $datetime
     * @param string|null $format
     * @return Carbon|null
     */
    public static function parseDateTime(?string $datetime, ?string $format = null): ?Carbon
    {
        if (empty($datetime)) {
            return null;
        }

        try {
            if ($format) {
                return Carbon::createFromFormat($format, $datetime, self::getTimezone());
            }
            return Carbon::parse($datetime, self::getTimezone());
        } catch (\Exception $e) {
            \Log::error('TimeHelper::parseDateTime error', [
                'datetime' => $datetime,
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Parse a time string and return Carbon instance with today's date.
     * 
     * @param string|null $time
     * @param string|null $format
     * @return Carbon|null
     */
    public static function parseTime(?string $time, ?string $format = null): ?Carbon
    {
        if (empty($time)) {
            return null;
        }

        try {
            $today = self::today();
            
            if ($format) {
                $parsed = Carbon::createFromFormat($format, $time, self::getTimezone());
            } else {
                // Try common time formats
                $formats = ['H:i:s', 'H:i', 'h:i A', 'h:i:s A'];
                $parsed = null;
                
                foreach ($formats as $fmt) {
                    try {
                        $parsed = Carbon::createFromFormat($fmt, $time, self::getTimezone());
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                if (!$parsed) {
                    $parsed = Carbon::parse($time, self::getTimezone());
                }
            }
            
            // Set to today's date
            return $today->setTime($parsed->hour, $parsed->minute, $parsed->second ?? 0);
        } catch (\Exception $e) {
            \Log::error('TimeHelper::parseTime error', [
                'time' => $time,
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Combine date and time into a single Carbon instance.
     * 
     * @param string|Carbon|null $date
     * @param string|null $time
     * @return Carbon|null
     */
    public static function combineDateTime($date, ?string $time): ?Carbon
    {
        if (empty($date) || empty($time)) {
            return null;
        }

        try {
            $dateCarbon = $date instanceof Carbon ? $date : self::parseDate($date);
            if (!$dateCarbon) {
                return null;
            }

            $timeCarbon = self::parseTime($time);
            if (!$timeCarbon) {
                return null;
            }

            return $dateCarbon->copy()->setTime(
                $timeCarbon->hour,
                $timeCarbon->minute,
                $timeCarbon->second ?? 0
            );
        } catch (\Exception $e) {
            \Log::error('TimeHelper::combineDateTime error', [
                'date' => $date,
                'time' => $time,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Format a Carbon instance for display.
     * 
     * @param Carbon|null $carbon
     * @param string $format
     * @return string
     */
    public static function format(?Carbon $carbon, string $format = 'Y-m-d H:i:s'): string
    {
        if (!$carbon) {
            return '';
        }

        try {
            // Ensure the Carbon instance is in the application timezone
            $carbon = $carbon->setTimezone(self::getTimezone());
            return $carbon->format($format);
        } catch (\Exception $e) {
            \Log::error('TimeHelper::format error', [
                'carbon' => $carbon,
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Format date for display.
     * 
     * @param Carbon|string|null $date
     * @param string $format
     * @return string
     */
    public static function formatDate($date, string $format = 'Y-m-d'): string
    {
        if (empty($date)) {
            return '';
        }

        $carbon = $date instanceof Carbon ? $date : self::parseDate($date);
        if (!$carbon) {
            return '';
        }

        return self::format($carbon, $format);
    }

    /**
     * Format time for display.
     * 
     * @param Carbon|string|null $time
     * @param string $format
     * @return string
     */
    public static function formatTime($time, string $format = 'H:i'): string
    {
        if (empty($time)) {
            return '';
        }

        $carbon = $time instanceof Carbon ? $time : self::parseTime($time);
        if (!$carbon) {
            return '';
        }

        return self::format($carbon, $format);
    }

    /**
     * Check if a date is in the future (after now).
     * 
     * @param Carbon|string|null $date
     * @return bool
     */
    public static function isFuture($date): bool
    {
        if (empty($date)) {
            return false;
        }

        $carbon = $date instanceof Carbon ? $date : self::parseDate($date);
        if (!$carbon) {
            return false;
        }

        return $carbon->isFuture();
    }

    /**
     * Check if a date is in the past (before now).
     * 
     * @param Carbon|string|null $date
     * @return bool
     */
    public static function isPast($date): bool
    {
        if (empty($date)) {
            return false;
        }

        $carbon = $date instanceof Carbon ? $date : self::parseDate($date);
        if (!$carbon) {
            return false;
        }

        return $carbon->isPast();
    }

    /**
     * Check if a date is today.
     * 
     * @param Carbon|string|null $date
     * @return bool
     */
    public static function isToday($date): bool
    {
        if (empty($date)) {
            return false;
        }

        $carbon = $date instanceof Carbon ? $date : self::parseDate($date);
        if (!$carbon) {
            return false;
        }

        return $carbon->isToday();
    }

    /**
     * Get the start of day for a given date.
     * 
     * @param Carbon|string|null $date
     * @return Carbon|null
     */
    public static function startOfDay($date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        $carbon = $date instanceof Carbon ? $date : self::parseDate($date);
        if (!$carbon) {
            return null;
        }

        return $carbon->copy()->startOfDay();
    }

    /**
     * Get the end of day for a given date.
     * 
     * @param Carbon|string|null $date
     * @return Carbon|null
     */
    public static function endOfDay($date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        $carbon = $date instanceof Carbon ? $date : self::parseDate($date);
        if (!$carbon) {
            return null;
        }

        return $carbon->copy()->endOfDay();
    }
}

