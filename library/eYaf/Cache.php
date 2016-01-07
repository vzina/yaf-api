<?php
/**
 *
 */
namespace eYaf;

class Cache
{
	public static function factory($config)
	{
		$adapter = $params = null;
	    extract($config);
        $class = __NAMESPACE__ . '\Cache\\' . ucfirst($adapter);
        return new $class($params);
	}
}