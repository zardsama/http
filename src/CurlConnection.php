<?PHP

	namespace zardsama\http;

	class CurlConnection {
		private $curl;
		private $url;
		private $method;
		private $args;
		private $agent;
		private $result;
		private $info;
		private $opts;

		public function __construct($url, $method = 'GET', $args = null) {
			$this->url = $url;
			$this->method = $method;
			$this->args = $args;

			if($method == 'GET' && is_null($args) == false) {
				$this->url .= http_build_query($args);
			}

			$this->curl = curl_init();
            $this->agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.229 Whale/2.10.123';
		}

		public function setHeader($header) {
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
		}

        public function compressed()
        {
            curl_setopt($this->curl, CURLOPT_ENCODING , 'gzip');
        }

		public function saveCookie($path = 'cookie.txt') {
			curl_setopt($this->curl, CURLOPT_COOKIEJAR, $path);
		}

		public function loadCookie($path = 'cookie.txt') {
			curl_setopt($this->curl, CURLOPT_COOKIEFILE, $path);
		}

		public function setReferer($refer) {
			$this->opts[CURLOPT_REFERER] = $refer;
		}

		public function setAgent($angent) {
			$this->agent = $angent;
		}

		public function setopt($field, $value) {
			curl_setopt($this->curl, $field, $value);
		}

		public function exec($param = null) {
			curl_setopt($this->curl, CURLOPT_URL, $this->url);
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->curl, CURLOPT_HEADER, ($param['header'] == true));
			curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($this->curl, CURLOPT_USERAGENT, $this->agent);
			curl_setopt($this->curl, CURLOPT_VERBOSE, false);
            if (isset($this->opts[CURLOPT_REFERER]) == false) {
			    curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
            }

			$url = parse_url($this->url);
			if($url['scheme'] == 'https'){
				curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
			}

			if($this->method == 'POST') {
				curl_setopt($this->curl, CURLOPT_POST, true);
		        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->args);
			}

			if(is_array($this->opts) && count($this->opts) > 0) {
				foreach($this->opts as $field => $value) {
					$this->setopt($field, $value);
				}
			}

			$this->result = curl_exec($this->curl);
			$this->info = curl_getinfo($this->curl);

			return $this->getResult();
		}

		public function close() {
			curl_close($this->curl);
		}

		public function getResult($get_all = false) {
			return $this->result;
		}

		public function getInfo() {
			if(is_array($this->info)) {
				return $this->info;
			}
			return false;
		}
	}

?>