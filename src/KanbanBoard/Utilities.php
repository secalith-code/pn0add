<?php
namespace App\KanbanBoard;

use Exception;

class Utilities
{
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
                    sprtinf(
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

	public static function dump($data) {
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}
}