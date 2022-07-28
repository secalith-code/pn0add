<?php

namespace App;

use DOMDocument;
use Exception;
use InvalidArgumentException;
use Michelf\Markdown;
use Symfony\Component\Dotenv\Dotenv;

class Utilities
{
    public static string $sortBy;

    public static function loadEnv(string $envFile): void
    {
        $dotenv = new Dotenv();
        $dotenv->load($envFile);
    }

    /**
     * @param string               $name
     * @param bool|string|int|null $default
     *
     * @return mixed
     */
    public static function env(string $name, bool | string | int $default = null): mixed
    {
        try {
            if (array_key_exists($name, $_SERVER)) {
                return $_SERVER[$name];
            } elseif ($default !== null) {
                return $default;
            } else {
                throw new Exception(
                    sprintf(
                        "Environment variable %s not found or has no value.",
                        $name
                    )
                );
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return null;
    }

    /**
     * @param array    $data
     * @param mixed    $sortBy
     * @param int|null $sortOrder
     *
     * @return array
     */
    public static function sortArrayByKey(array $data, mixed $sortBy, ?int $sortOrder = SORT_ASC): array
    {
        // So it will be accessible in anonymous function.
        self::$sortBy = $sortBy;

        if ($sortOrder === SORT_ASC) {
            // sort ascending
            usort(
                $data,
                function ($item1, $item2) {
                    return $item1[self::$sortBy] <=> $item2[self::$sortBy];
                }
            );
        } else {
            // sort descending
            usort(
                $data,
                function ($item1, $item2) {
                    return $item2[self::$sortBy] <=> $item1[self::$sortBy];
                }
            );
        }

        return $data;
    }

    /**
     * @param int $complete
     * @param int $remaining
     *
     * @return array|null
     */
    public static function calcProgress(int $complete, int $remaining): ?array
    {
        $total = $complete + $remaining;

        if ($total > 0) {
            $percent = ($complete || $remaining) ? round($complete / $total * 100) : 0;

            return [
                'total' => $total,
                'complete' => $complete,
                'remaining' => $remaining,
                'percent' => $percent
            ];
        }

        return null;
    }

    /**
     * Trim the text, convert newlines to <br />, transform markdown to html,
     * and add e target=_blank to html links
     *
     * @param string|null $markdown
     *
     * @return string
     */
    public static function fetchMarkdownToHTML(?string $markdown): ?string
    {
        $markdown = trim($markdown);
        $markdown = nl2br($markdown);
        $markdown = Markdown::defaultTransform($markdown);
        $markdown = self::addBlankTargetToHTMLLinks($markdown);

        return $markdown;
    }

    /**
     * @param string|null $html
     *
     * @return string|null
     */
    public static function addBlankTargetToHTMLLinks(?string $html): ?string
    {
        if( ! empty($html)) {
            $doc = new DOMDocument();
            $dom = $doc->loadHTML($html);
            // only if DOMDocument
            if($dom) {
                if ($doc->getElementsByTagName("a")->length > 0) {
                    foreach ($doc->getElementsByTagName('a') as $link) {
                        $link->setAttribute('target', '_blank');
                    }
                }
            }
        }

        return $doc->saveHTML();
    }
}
