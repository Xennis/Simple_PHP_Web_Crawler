<?php

/**
 * Simple Web Crawler
 *
 * Crawls the web starting from a given URL.
 */
class SimpleWebCrawler {

	/**
	 * Match pattern to find HTTP URLs
	 *
	 * @var string Pattern
	 */
	private $_http_match_pattern = '/(href="http:\/\/)(.*?)(")/i';	

	/**
	 * 
	 * 
	 * @var boolean True, if crawler runs.
	 */
	private $_isCrawling = false;
	
	/**
	 * Name of the database table
	 * 
	 * @var string
	 */
	private $_db_table = 'swc_url';
	
	/**
	 * 
	 * @param string $url
	 * @return string 
	 */
	private function _get_main_url($url){
		$host = parse_url($url, PHP_URL_HOST);
		return 'http://'.preg_replace('/www./', '', $host);
	}

	/**
	 * 
	 * @param string $file Content of a web page as string
	 * @return array.string
	 */
	private function _get_urls($file){
		preg_match_all($this->_http_match_pattern, $file, $patterns);

		$count = count($patterns[2]);
		for($i = 0; $i < $count; $i++) {
			$patterns[2][$i] = $this->_get_main_url(htmlentities('http://'.$patterns[2][$i])); // Save main url only		
		}
		return array_unique($patterns[2]); // Delete duplicate entries
	}

	/**
	 * Insert URL into database.
	 * 
	 * @param string $url URL
	 * @return boolean Result of the query
	 */
	private function _url_insert($url){
		$insert_url = "INSERT INTO $this->_db_table (url, date) VALUES ('$url', '".time()."')";
		return mysql_query($insert_url);
	}

	/**
	 * Update the status of an URL.
	 * 
	 * @param type $url URL
	 * @param type $status Status (number 0 to 2)
	 * @return boolean Result of the query
	 */
	private function _url_update_status($url, $status){
		$update_url = "UPDATE $this->_db_table
						SET status = '$status'
						WHERE url = '$url'";
		return mysql_query($update_url);
	}

	/**
	 * Get a next URL with status 0.
	 * 
	 * @return string URL
	 */
	public function get_next_url(){
		$select_url = "SELECT url
						FROM $this->_db_table
						WHERE status = '0'
						LIMIT 1";
		$query_select_url = mysql_query($select_url);
		while($row = mysql_fetch_object($query_select_url)) {
			return $row->url;
		}
	}
	
	public function crawl_start($url) {
		$this->_isCrawling = true;
		$this->_crawl($url);
	}
	
	public function crawl_stop() {
		$this->_isCrawling = false;		
	}
	
	/**
	 * 
	 * @param type $url
	 */
	private function _crawl($url){
		
		if (empty($url)) {
			return;
		}

		// Eingabe URL
		$url = htmlentities(strip_tags($url));
		if(strpos($url, 'http', 0) === false){
			$url = "http://$url";
		}   

		// Suche
		if(true == ($file = @file_get_contents($url))){

			echo '<br><span class="style_normal">'.$url.' is crawled:</span><br>';
			$this->_url_insert($url);
			$this->_url_update_status($url, '1');

			foreach($this->_get_urls($file) as $key => $val) {

				if ($val === 'http://') {
					continue;
				}

				// In DB speichern
				$this->_url_insert($val);

				echo ' - <span class="style_green">'.$val.'</span>';
				}
		}
		else {
			$this->_url_update_status($url, '2');

			echo ' - <span class="style_red">'.$url.'</span>';
		}

		// Continue crawling
		if ($this->_isCrawling) {
			$this->_crawl($this->get_next_url());
		}
	}	
	
}
