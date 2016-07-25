<?php
//NOTES:
//条粉肠 Jens Segers好像后来用了别的框架重写，网上找到2013的（即本文件最接近的) 在这里：
//https://github.com/jw2013/sharedProxy/blob/master/proxy.php
//暂时未知有没有解决不了的反向问题，先用来玩着先.
//201509: 还真有个BUG，就是在上传附件时不知道什么原因 500，所以暂时后台不使用这个入口....
//2016: 这个BUG已经解决了.查找下方带 hack的位置.
//20160723: TODO 文件上传还有BUG（用ACE的upload测试的。要两边都调到可用
//NOTES: this one don't follow forward auto
class cmppx
{
	protected $ch;

	protected $config = array();

	function __construct()
	{
		$config = array();

		$config['timeout'] = 58;//ISP like ALIYUN default 1-min...

		$this->config = $config;

		// initialize curl
		$this->ch = curl_init();
		@curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, false);
		//@curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		//curl_setopt($this->ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);//JUST GET CONTENT DONT OUTPUT

		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);//
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);//

		curl_setopt($this->ch, CURLOPT_HEADER, true);//include header in the output
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->config["timeout"]);
	}

	public function forward($url = '',$server,$port=80,$scheme='http')
	{
		// build the correct url
		$config['server'] = $server;
		$config['port']  = $port;
		$this->config=$config;

		$url = "$scheme://" . $this->config["server"] . ":" . $this->config["port"] . "/" . ltrim($url, "/");

		// set url
		curl_setopt($this->ch, CURLOPT_URL, $url);

		// forward request_headers
		$req_headers = $this->get_request_headers();
		$this->set_request_headers($req_headers);

		//handle the POST specially
		if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			//if(in_array($this->get_content_type($header), array('application/x-www-form-urlencoded','multipart/form-data')))
			//{
			//	$this->set_post($_POST);
			//}
			//else
			//{
			//	// just grab the raw post data
			//	$fp = fopen('php://input','r');
			//	$post = stream_get_contents($fp);
			//	fclose($fp);
			//	$this->set_post($post);
			//}
			$this->set_post(
				(in_array($this->get_content_type($header),
				array('application/x-www-form-urlencoded','multipart/form-data')))
				? $_POST: file_get_contents('php://input')
			);
		}

		$data = curl_exec($this->ch);
		$info = curl_getinfo($this->ch);

		$body = $info["size_download"] ? substr($data, $info["header_size"], $info["size_download"]) : "";

		curl_close($this->ch);

		$resp_headers = substr($data, 0, $info["header_size"]);
		$this->set_response_headers($resp_headers);

		if($body){
			echo $body;
		}
	}

	protected function get_content_type( $headers )
	{
		foreach( $headers as $name => $value ){
			if( 'content-type' == strtolower($name) ){
				$parts = explode(';', $value);
				return strtolower($parts[0]);
			}
		}
		return null;
	}

	protected function get_request_headers()
	{
		// use native getallheaders function if any
		if (function_exists('getallheaders')) return getallheaders();

		// fallback
		$headers = '';
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}

		return $headers;
	}

	protected function set_request_headers($request)
	{
		$header_to_skip = array("Content-Length", "Host");
		$flag_file=count($_FILES);//hack

		$headers = array();
		foreach ($request as $key => $value)
		{
			//hack:
			if($key=="Content-Type")
			{
				//$headers[] = "Debug-Flag-File: $flag_file";
				//$headers[]="$key: multipart/form-data;";
				if($flag_file>1) continue;//if have file upload, skip this header and curl will pack it later..
			}
			if ($key && !in_array($key, $header_to_skip))
			{
				$headers[] = "$key: $value";
			}
		}
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
	}

	protected function set_response_headers($response)
	{
		$header_to_skip = array("Transfer-Encoding");

		$headers = explode("\n", $response);

		$dbga=$response;

		// process response headers
		foreach ($headers as &$header)
		{
			if (!$header) continue;

			$pos = strpos($header, ":");
			$key = substr($header, 0, $pos);

			if (strtolower($key) == "location")
			{
				//$base_url = $_SERVER["HTTP_HOST"];
				//$base_url .= rtrim(str_replace(basename($_SERVER["SCRIPT_NAME"]), "", $_SERVER["SCRIPT_NAME"]), "/");
				//// replace ports and forward url
				//$header = str_replace(":" . $this->config["port"], "", $header);
				//$header = str_replace($this->config["server"], $base_url, $header);

				header($header);die;
			}

			if (!in_array($key, $header_to_skip))
			{
				header($header, FALSE);
			}
		}
	}

	protected function set_post($post)
	{
		if (count($_FILES))
		{
			$post2=array();
			foreach ($_FILES as $file_key => $file)
			{
				$full_path = realpath( $file['tmp_name'] );
				$filename=$file['name'];
				$post2[$file_key] = '@'.$full_path .';filename='.$filename;
			}
			//TODO other than the _FILES is not yet supported:
			//$post=$post2+$post;//WTF...
			//$post=array_merge($post2,$post);//WTF...
			$post=$post2;
		}
		else if( is_array( $post ) )
		{
			$post = http_build_query($post);
		}

		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);
	}
}
