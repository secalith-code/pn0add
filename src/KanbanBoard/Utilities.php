<?php
namespace App\KanbanBoard;

use Exception;
use InvalidArgumentException;
use Michelf\Markdown;

class Utilities
{
    public static string $sortBy;

    /**
     * @param string $name
     * @param bool|string|int|null $default
     * @return mixed
     */
	public static function env(string $name, bool|string|int $default = null): mixed
    {
        try {
            if(array_key_exists($name,$_SERVER)) {
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
        } catch (Exception $e){
            echo $e->getMessage();
        }

        return null;
    }

    public static function sortArrayByKey(array $data, mixed $sortBy, ?int $sortOrder=SORT_ASC): array
    {
        self::$sortBy = $sortBy;

        if($sortOrder===SORT_ASC) {
            // sort ascending
            usort($data, function ($item1, $item2) {
                return $item1[self::$sortBy] <=> $item2[self::$sortBy];
            });
        } else {
            // sort descending
            usort($data, function ($item1, $item2) {
                return $item2[self::$sortBy] <=> $item1[self::$sortBy];
            });
        }

        return $data;
    }

    public static function calcProgress($complete,$remaining): null|array
    {
        $total = $complete + $remaining;
        if($total > 0)
        {
            $percent = ($complete || $remaining) ? round($complete / $total * 100) : 0;

            return array(
                'total' => $total,
                'complete' => $complete,
                'remaining' => $remaining,
                'percent' => $percent
            );
        }
        return null;
    }

}