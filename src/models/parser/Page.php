<?php

namespace App\models\Parser;

use App\Core;

class Page
{
    const SEARCH_LINKS = '/<(link|a|img|script)(.*?)(href|src)=("|\')(.*?)("|\')/is';
    const SEARCH_PAGES = '/<a(.*?)href=("|\')(.*?)("|\')/is';
	const MAX_TIMEOUT = 4000;
    private $content = null;
    
	private $url;
	private $domain;
	private $scheme;
	private $links = null;
	
	public function __construct($url)
	{
		$this->url = $url;
		$path = parse_url($url);
		$this->domain = $path['host'];
		$this->scheme = $path['scheme'];
	}

    public function findLinks()
    {
        $content = $this->getContent();
        preg_match_all(self::SEARCH_LINKS, $content, $res);
        $urls = [];
        foreach ($res[0] as $key => $item) {
            if ($res[5][$key] && (($res[5][$key][0] == '/') || strpos($res[5][$key], 'http') !== false)) {
				if (strpos($res[5][$key], '//') === 0)
					$res[5][$key] = $this->scheme.':'.$res[5][$key];
				elseif ($res[5][$key][0] == '/')
					$res[5][$key] = $this->scheme.'://'.$this->domain.$res[5][$key];
                $urls[] = ['type' => $res[1][$key], 'url' => $res[5][$key]];
			}
        }
        $this->links = array_map("unserialize", array_unique(array_map("serialize", $urls)));

		return $this;
    }
    
	public function getBadLinks()
	{
		$res = null;
		$needcherch = null;
		if ($this->links && !empty($this->links)) {
			$codes = Core::getInstance()->getSession($this->getSessionCode());
			if (!$codes) {
				$res = [];
				$codes = [];
			}

			foreach ($this->links as $link) {
				if (isset($codes[$link['url']])) {
					$res[$link['url']] = $codes[$link['url']];
				} else 
					$needcherch[] = $link['url'];
			}
			
			if ($needcherch) {
                
				$info = $this->getCodes($needcherch);
                foreach ($info as $url => &$code) {
                    if (!$this->isDoaminUrl($url) && in_array($code, ['302','400']))
                        $code = 200;
                    if ($code == 0)
                         $code = 'Not found';
                }
				$res = array_merge($res, $info);
				$codes = array_merge($codes, $info);
                
				/*
				foreach ($needcherch as $link) {
					$linkinfo = $this->cURL($link);
                    if ($linkinfo['info']['http_code'] == 0)
                        $linkinfo['info']['http_code'] = 'Not found';
                    if (!$this->isDoaminUrl($link) && in_array($linkinfo['info']['http_code'], ['302','400']))
                        $linkinfo['info']['http_code'] = 200;
					$codes = array_merge($codes, [$link => $linkinfo['info']['http_code']]);
					$res[$link] = $linkinfo['info']['http_code'];
				}
				*/
                
				Core::getInstance()->setSession($this->getSessionCode(), $codes);
			}	
			
		}
		
		if ($res) {
			foreach ($res as $link => $code) {
				if ($code == 200)
					unset($res[$link]);
			}
		}
		
		return $res;
	}
	
	private function getCodes($links)
	{
		$num = 7;
		$res = [];

		for ($i = 0; $i < count($links); $i += $num) {
			$tmpurls = array_slice($links, $i, $num);
			$linkinfo = $this->multicURL($tmpurls);
			
			$codes = array_column($linkinfo, 'http_code', 'url');
			$res = array_merge($res, $codes);
		//	var_dump($res); exit;
		}
		return $res;
	}
	
	public function getPages()
	{
		$res = null;
		if ($this->links && !empty($this->links)) {
			foreach ($this->links as $link) {
				if (($link['type'] == 'a') && $this->isDoaminUrl($link['url'])) {
					$info = pathinfo($link['url']);
					if (!isset($info['extension']) || in_array($info['extension'], ['php','htm','html']))
						$res[] = $link['url'];
				}	
			}
		}
		
		return $res;
	}
	
	public function clearCache()
	{
		Core::getInstance()->clearSession($this->getSessionCode());
		return $this;
	}
	
	private function getSessionCode()
	{
		return 'code_'.$this->domain;
	}
	
    private function isDoaminUrl($url)
    {
        $pos = strpos($url, $this->domain);
        return (($pos > 0) && ($pos < 10));
    }
    
	private function getLinkCode($link)
	{
		/*
		$codes = Core::getInstance()->getSession($this->getSessionCode());
		if (!$codes)
			$codes = [];
		if (!isset($codes[$link])) {
			$res = $this->cURL($link);
			$codes = array_merge($codes, [$link => $res['info']['http_code']]);
			$codes = Core::getInstance()->setSession($this->getSessionCode(), $codes);
			$code = $res['info']['http_code'];
		} else
			$code = $codes[$link];
*/
		return $code;
	}
	
	private function multicURL($urls, $header=NULL, $body=true, $cookie=NULL, $p=NULL)
	{
				$i = 1;
				$mh = curl_multi_init();
				foreach ($urls as $url) {
					$curlname = 'ch'.$i;
					$$curlname = curl_init();
					

    	curl_setopt($$curlname, CURLOPT_HEADER, $header);
    	curl_setopt($$curlname, CURLOPT_NOBODY, !$body);
    	curl_setopt($$curlname, CURLOPT_URL, $url);
    	curl_setopt($$curlname, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($$curlname, CURLOPT_CONNECTTIMEOUT, 0); 
		curl_setopt($$curlname, CURLOPT_TIMEOUT_MS, self::MAX_TIMEOUT); //timeout in seconds
    	curl_setopt($$curlname, CURLOPT_COOKIE, $cookie);

	 	curl_setopt($$curlname, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 GTB5"); 

    	curl_setopt($$curlname, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($$curlname, CURLOPT_SSL_VERIFYPEER, 0);
    	curl_setopt($$curlname, CURLOPT_FOLLOWLOCATION, 0);
					/*
					curl_setopt($$curlname, CURLOPT_URL, $url);
					curl_setopt($$curlname, CURLOPT_HEADER, 0);
					curl_setopt($$curlname, CURLOPT_NOBODY, 1);
				//	curl_setopt($$curlname, CURLOPT_VERBOSE, 1);
					curl_setopt($$curlname, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($$curlname, CURLOPT_SSL_VERIFYHOST, 0);
					//curl_setopt($$curlname, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($$curlname, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($$curlname, CURLOPT_COOKIE, $cookie);
					*/
					
					curl_multi_add_handle($mh, $$curlname);
					$i++;
				}

				$running = null;				
				do {
					$res = curl_multi_exec($mh, $running);
				} while ($running > 0);
				
				for ($i = 1; $i <= count($urls); $i++) {
					$curlname = 'ch'.$i;
					$info[$urls[$i - 1]] = curl_getinfo($$curlname);
					curl_multi_remove_handle($mh, $$curlname);
				}
				

				curl_multi_close($mh);
				return $info;
	}
	
	private function cURL($url, $header=NULL, $body=true, $cookie=NULL, $p=NULL)
    {
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_HEADER, $header);
    	curl_setopt($ch, CURLOPT_NOBODY, !$body);
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
    	curl_setopt($ch, CURLOPT_COOKIE, $cookie);

	 //	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 GTB5"); 

    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
     
    	if ($p) {
    		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
    	}
		
    	$result = curl_exec($ch);
		$info = curl_getinfo($ch);

		curl_close($ch);
		
		return ['content' => $result, 'info' => $info];
    }
	
    private function getContent()
    {/*
        if (!$this->content) {
            $this->content = file_get_contents($this->url);
        }
        return $this->content;
       */
        if (!$this->content) {
            $num = 5;
            $i = 0;
            do {
                $i++;
                $res = $this->cURL($this->url);
                $this->content = $res['content'];
            } while((!$this->content) && ($i < $num));
        }
		
        return $this->content;
    }
}
