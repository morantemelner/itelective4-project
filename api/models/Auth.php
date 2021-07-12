<?php
    class Auth{
        protected $globalMethods, $pdo;

        public function __construct(\PDO $pdo){
            $this->pdo = $pdo;
			$this->globalMethods = new GlobalMethods($pdo);
        }

        function encryptPassword($pword): ?string {
            $hashFormat="$2y$10$";
            $saltLength=22;
            $salt=$this->generateSalt($saltLength);
            return crypt($pword, $hashFormat.$salt);
        }

        function generateSalt($len){
            $urs=md5(uniqid(mt_rand(), true));
            $b64String=base64_encode($urs);
            $mb64String=str_replace('+','.', $b64String);
            return substr($mb64String, 0, $len);
        }

        public function generateHeader(){
            $header = [
                "typ" => "JWT",
                "alg" => "HS256",
                "app" => "App Name",
                "dev" => "Daryl John Tadeo"
            ];
            return str_replace("=", "", base64_encode(json_encode($header)));
         }

        public function generatePayload($uc, $ue, $ito){
            $payload = [
				"uc" => $uc,
				"ue" => $ue,
				"ito" => $ito,
				"iby" => "Daryl John Tadeo",
				"ie" => "daryljohntadeo359@gmail.com",
				"exp" => date("Y-m-d H:i:s")
			];
			return str_replace("=", "", base64_decode(json_encode($payload)));
        }

        protected function generateToken($userCode, $userEmail, $fullName) {
			$header = $this->generateHeader();
			$payload = $this->generatePayload($userCode, $userEmail, $fullName);
			$signature = hash_hmac("sha256", "$header.$payload", base64_encode(SECRET));
			return "$header.$payload." .str_replace("=", "", base64_encode($signature));
		}
        
        public function showToken($data){
            $user_data = []; 
            foreach ($data as $key => $value) {
                array_push($user_data, $value);
            }
            return $this->generateToken($user_data[1], $user_data[2], $user_data[3]);
        }


        public function sendPayload($payload, $remarks, $message, $code) {
            $status = array("remarks"=>$remarks, "message"=>$message);
            http_response_code($code);
            return array(
                "status"=>$status,
                "payload"=>$payload,
                'prepared_by'=>'Daryl John Tadeo',
                "timestamp"=>date_create()
            );
        }

        function accountLogin($dt){
             
            $code = 403; 
            $msg = ""; 
            $remarks = "";
            $payload = "";

            $this->sql="SELECT * FROM tbl_accounts WHERE acc_email='$dt->acc_email' LIMIT 1";

            try {
                if ($res = $this->pdo->query($this->sql)->fetchColumn() > 0) {
                    $result=$this->pdo->query($this->sql)->fetchAll();

                    foreach ($result as $rec) { 
                        if($this->accountPasswordCheck($dt->acc_password, $rec['acc_password'])){
                            $uc = $rec['acc_id'];
                            $ue = $rec['acc_email'];
                            $fn = $rec['acc_fname'].' '.$rec['acc_lname'];
                            $user_status = $rec['acc_status'];
                            $user_role = $rec['acc_role'];
                            $tk = $this->generateToken($uc, $ue, $fn);

                            $sql = "UPDATE tbl_accounts SET acc_token='$tk' WHERE acc_id='$uc'";
                            $this->pdo->query($sql);

                            $payload = $rec;
                            $code = 200; 
                            $msg = "Login Success!"; 
                            $remarks = "success";
                            
                        } else{
                            $payload = "incorrectCredentials"; $code= 200; $msg = "Incorrect Password"; $remarks = "failed";
                        }
                    }
                } else{
                    $payload = "notExist!"; $code = 200; $msg = "User does not exist"; $remarks = "notExist";
                }
            } catch (\PDOException $e) {
                $msg = $e->getMessage(); $remarks = "failed";
            }
            return $this->sendPayload($payload, $remarks, $msg, $code); // For security encoding data that is returned
        }

        function accountRegister($dt) {
            $encryptedPassword = $this->encryptPassword($dt->acc_password);

            $i = 0; $fields=[]; $values=[];
            foreach ($dt as $key => $value) {
                array_push($fields, $key);
                array_push($values, $value);
            }

            $otp = rand(111111, 999999);

            $data = array(); $code = 0; $msg = ""; $remarks = "";

            //Check if the email exists...
            if($dt->acc_email != null){
                $sql="SELECT * FROM tbl_accounts WHERE acc_email='$dt->acc_email' LIMIT 1";
                    if($this->pdo->query($sql)->fetchAll()) {
                        $data = "exist!"; $code = 200; $msg = "Email Does Exist!"; $remarks = "existing";
                        return $this->sendPayload($data, $remarks, $msg, $code);
                    } else {
                        try {
                            $sqlstr = "INSERT INTO tbl_accounts (acc_email, acc_password, acc_fname, acc_mname, acc_lname, acc_role, acc_otp, acc_createdAt, acc_updatedAt) 
                                VALUES ('$dt->acc_email', '$encryptedPassword', '$dt->acc_fname', '$dt->acc_mname', '$dt->acc_lname', '$dt->acc_role', '$otp', NOW(), NOW())";
                                
                            if($this->pdo->query($sqlstr)) {
                                $data = $dt->acc_email; $code = 200; $msg = "Successfully retrieved the requested records"; $remarks = "success";
                            } else { 
                                $data = null; $code = 400; $msg = "Bad Request"; $remarks = "failed";
                            }
                            
                        } catch (\PDOException $e) {
                            $errmsg = $e->getMessage();
                            $code = 403;
                        }
                    }
                    return $this->sendPayload($data, $remarks, $msg, $code);
                }
        }

        public function accountUpdatePassword($dt, $filter_data) {
            $encryptedPassword = $this->encryptPassword($dt->acc_password);

            try {
                $sqlstr = "UPDATE tbl_user SET user_pword = '$encryptedPassword' WHERE $filter_data";
                    
                if($this->pdo->query($sqlstr)) {
                    $data = null; $code = 200; $msg = "Successfully Changed Password"; $remarks = "success";
                } else { 
                    $data = null; $code = 400; $msg = "Bad Request"; $remarks = "failed";
                }
                
            } catch (\PDOException $e) {
                $errmsg = $e->getMessage();
                $code = 403;
            }
            return $this->sendPayload($data, $remarks, $msg, $code);
        }

        public function accountVerifyEmail($dt) {

            $this->sql="SELECT * FROM tbl_user WHERE user_email='$dt->user_email' LIMIT 1";

            try {
                if ($res = $this->pdo->query($this->sql)->fetchColumn()>0) {
                    $result=$this->pdo->query($this->sql)->fetchAll();

                    $data = array(); $code = 0; $msg = ""; $remarks = ""; $token = "";
                    foreach ($result as $rec) { 
                        if($dt->acc_otp == $rec['acc_otp']){
                            $this->sql = "UPDATE tbl_accounts SET is_activated=1 WHERE acc_email='$dt->user_email'";
                            $sqlstr = $this->pdo->prepare($this->sql);
                            $sqlstr->execute();
                            $res = null; $code = 200; $msg = "Successfully retrieved the requested records"; $remarks = "success";
                        } else{
                            http_response_code(401);
                            $res = null; $code = 401; $msg = "Incorrect otp"; $remarks = "failed";
                        }
                    }
                } else{
                    http_response_code(401);
                    $res = null; $code = 401; $msg = "User does not exist"; $remarks = "failed";
                }
            } catch (\PDOException $e) {
                $msg = $e->getMessage(); $code = 401; $remarks = "failed";
            }
            return $this->sendPayload(base64_encode(json_encode($res)), $remarks, $msg, $code);
        }

        function accountPasswordCheck($pw, $existingpw){
            $hash=crypt($pw, $existingpw);
            if($hash === $existingpw){return true;} else {return false;}
        } 

    } 

?>