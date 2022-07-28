<?php
namespace App\KanbanBoard;

use Exception;
use Michelf\Markdown;

class Utilities
{
    public static $sortBy;

	public static function env($name, $default = NULL)
    {
        try {
            $value = getenv($name);
            if( ! $value && isset($_SERVER[$name])) {
                return $_SERVER[$name];
            } elseif($default !== NULL) {
                return $default;
            } else {
                throw new Exception(
                    sprintf(
                        "Environment variable %s not found or has no value",
                        $name
                    )
                );
            }
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

	public static function hasValue($array, $key) {
		return is_array($array) && array_key_exists($key, $array) && !empty($array[$key]);
	}

    public static function sortArrayByKey($data, $sortBy, ?int $sortOrder=SORT_ASC)
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