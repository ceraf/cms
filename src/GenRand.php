<?php

namespace App;

class GenRand
{
	private function __construct() { }

    public static function getRandGen($p)
	{
	//	array_walk($p, function(&$item){$item = round($item*10000);});
        foreach ($p as &$item)
            $item *= 10000;
        
		$max = array_sum($p);

		$num = mt_rand(1,$max);
		$start = 0;
		foreach ($p as $key => $item) {
			if (($num > $start) && ($num <= ($item + $start)))
				return $key;
			else
				$start += $item;
		}
		
		throw new Exception('Error during generate event');
	}
}
