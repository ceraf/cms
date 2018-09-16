<?php

namespace App\Models\Parser;

use App\Models\Parser\Page;
use App\Core;

class Parser
{
	const PARSE_LEVEL = '2';
    
    private $url;
	private $badlinks;
	private $pages;
    
    public function __construct($url)
    {
	//	if ($url[strlen($url) - 1] == '/')
	//		$url = substr($url, 0, strlen($url) - 1);
        $this->url = $url;
    }
    
	public function analysis()
	{
		$page = new Page($this->url);
		$page->findLinks();
		$this->badlinks = $page->getBadLinks();
		$this->pages = $page->getPages();
		if ($this->badlinks && $this->pages)
			$this->pages = array_diff ($this->pages, array_keys($this->badlinks));
		return $this;
	}
	
	public function clearCache()
	{
		(new Page($this->url))->clearCache();
		return $this;
	}
	
	public function getBadLinks()
	{
		return $this->badlinks;
	}
	
	public function getPages()
	{
		return $this->pages;
	}
	/*
    public function getPages()
    {
        $pages = $this->pages;
        for ($i = 0; $i < self::PARSE_LEVEL; $i++) {
            $pages = $this->parsePages($pages);
            if ($pages) {
                $this->pages = array_merge($this->pages, array_diff($pages, $this->pages));            
            }

        }

        echo '<pre>';var_dump($this->pages);echo '</pre>';
    }
    */
	public function parsePages($pages)
    {   
        $domain = $this->domain;
        $res = [];
		foreach ($pages as $url) {
            $page = new Page($url);
			$res = array_merge($res, array_map(function($item) use ($domain) {return $domain.$item;}, $page->getSubpages()));
        }
        return $res;    
    }

	
	
}
