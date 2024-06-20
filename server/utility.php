<?php
function mergeKeys(array $from, array $to, string... $singleKeys)
{
	foreach($from as $key => $value)
	{
		if(in_array($key, $singleKeys))
		{
			$to[$key] = $value;
			continue;
		}

		if(!isset($to[$key]))
				$to[$key] = [];

		if(!in_array($value, $to[$key]) && $value != null)
			$to[$key][] = $value;
	}

	return $to;
}