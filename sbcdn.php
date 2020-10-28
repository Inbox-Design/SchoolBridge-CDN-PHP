<?php 

	/***
		SchoolBridge CDN Client - PHP
		Inbox Design Limited
		https://www.inboxdesign.co.nz/
		https://bitbucket.org/sheldonlendrum/schoolbridge-cdn-client-php/
	**/

	defined('SB_CDN_AUTH_USERNAME') or die('Please define your SchoolBridge SB_CDN_AUTH_USERNAME.');
	defined('SB_CDN_AUTH_PASSWORD') or die('Please define your SchoolBridge SB_CDN_AUTH_PASSWORD.');

	class SBCDN
	{

		// upload vars
		public $file_path;
		public $file_name;
		public $destination;

		// error var
		public $errors;

		// Internal vars
		protected $upload_server = 'https://cdn.bridge.school.nz/upload';
		protected $curl;
		protected $response_string;
		protected $response;



		function __construct()
		{
			$this->errors = [];
			$this->file_path = NULL;
			$this->file_name = NULL;
			$this->destination = NULL;
		}

		// allow for error capturing
		public function last_error()
		{
			return end($this->errors);
		}
		public function errors()
		{
			return $this->errors;
		}


		// Add the file to be uploaded ... (path)
		public function add($file_path = NULL)
		{
			$this->file_path = $file_path;
			return $this;
		}

		// set WHERE this will be uploaded.
		// the first segment MUST match the school to upload it to.
		public function destination($destination = NULL)
		{
			$this->destination = $destination;
			return $this;
		}

		public function name($file_name = NULL)
		{
			$this->file_name = $file_name;
			return $this;
		}






		public function upload()
		{

			if(empty($this->file_path)) {
				$this->errors[] = 'The file_path parameter is required.';
				return FALSE;
			}
			if(!file_exists($this->file_path)) {
				$this->errors[] = 'The attached file could not be found.';
				return FALSE;
			}
			if(empty($this->destination)) {
				$this->errors[] = 'The destination parameter is required.';
				return FALSE;
			}

			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$this->file_type = finfo_file($finfo, $this->file_path);
			if(empty($this->file_type)) {
				$this->errors[] = 'Unable to find the filetype of the uploaded file.';
				$this->errors[] = $this->file_type;
				return FALSE;
			}

			$this->curl = curl_init();

			curl_setopt($this->curl, CURLOPT_URL, $this->upload_server);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, [
				'file' => new CURLFile($this->file_path, $this->file_type, basename($this->file_path)),
				'destination' => $this->destination,
				'file_name' => (!empty($this->file_name) ? $this->file_name : basename($this->file_path))
			]);
			curl_setopt($this->curl, CURLOPT_USERPWD, SB_CDN_AUTH_USERNAME .':'. SB_CDN_AUTH_PASSWORD);
			curl_setopt($this->curl, CURLOPT_TIMEOUT, 86400);
			curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 60000);
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->curl, CURLOPT_REFERER, @$_SERVER['HTTP_HOST']);

			$this->response_string = curl_exec($this->curl);
			if (curl_errno($this->curl)) {
				$this->errors[] = 'Error: '. curl_errno($this->curl) .' -> '. curl_error($this->curl);
				return FALSE;
			}

			$this->response = json_decode($this->response_string);

			curl_close($this->curl);

			return $this->response;

		}

	}
