<?php
/**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2022 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace ReduxWeb\AdvancedEmailGuard;

class Utils
{
    /**
     * Extract parts of an email address.
     *
     * @param string $email The email address to parse
     * @return array The parts of the email. First the local part, then the domain.
     */
    public static function parseEmailAddress($email)
    {
        static $regexLocal;
        static $regexDomain;
        if ($regexLocal === null) {
            $regexLocal = '(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:'
                . '\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\'
                . 'x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\'
                . 'x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\'
                . 'x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\'
                . 'x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*';
            $regexDomain = '(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:'
                . '[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::'
                . '[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:'
                . '[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|'
                . '(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]'
                . '{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])'
                . '|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))';
        }

        $pattern = sprintf('/^(?<local>%s)@(?<domain>%s)$/iD', $regexLocal, $regexDomain);

        if (!preg_match($pattern, $email, $parts)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid email', $email));
        }
        return array_map(array('\\Tools', 'strtolower'), array($parts['local'], $parts['domain']));
    }

    /**
     * Parse content and extract lines.
     *
     * @param string $content The content to parse
     * @return array Array of cleaned string
     */
    public static function parseLines($content)
    {
        // Split by line
        $lines = explode("\n", $content);

        // Trim and convert to lowercase
        $lines = array_map('trim', $lines);
        $lines = array_map(array('\\Tools', 'strtolower'), $lines);

        // Remove empty lines and comments
        $lines = array_filter($lines, function ($line) {
            return (0 === \Tools::strlen($line) || '#' === $line[0]) ? false : $line;
        });
        return $lines;
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function strContains($haystack, $needles)
    {
        if (\Tools::version_compare(_PS_VERSION_, '1.6', '>=')) {
            foreach ((array) $needles as $needle) {
                if ($needle !== '' && \Tools::strpos($haystack, $needle) !== false) {
                    return true;
                }
            }
            return false;
        }

        foreach ((array) $needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|array $pattern
     * @param string $value
     * @return bool
     */
    public static function strMatches($pattern, $value)
    {
        $patterns = (array) $pattern;
        if (empty($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            if ($pattern === $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');
            $pattern = str_replace('\*', '.*', $pattern);
            if (preg_match('#^' . $pattern . '\z#u', $value) === 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    public static function toStudly($value)
    {
        $value = \Tools::ucwords(str_replace(array('-', '_'), ' ', $value));
        return str_replace(' ', '', $value);
    }
}
