<?php
    // To Avoid CORS Policy
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: POST");   
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Max-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-Width, X-Auth-User");

	// date_timezone_set("Asia/Manila");
	set_time_limit(1000);
    
    // Importing all the php file from models folder
    require_once('./models/Auth.php');
    require_once('./models/Global.php');
    require_once('./models/OTP.php');

    // Database Connection Pattern
    define("DBASE", "db_ticketeer");
	define("USER", "root");
	define("PW", "");
	define("SERVER", "localhost");
	define("CHARSET", "utf8");
	define("SECRET", base64_encode("sampleSecretKey"));

    // Connection Function to MySql
	class Connection {
		protected $connectionString = "mysql:host=".SERVER.";dbname=".DBASE.";charset=".CHARSET;
		protected $options = [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES => false
		];

		public function connect() {
			return new \PDO($this->connectionString, USER, PW, $this->options);
		}
	}
?>