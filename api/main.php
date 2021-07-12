<?php
    require_once('./config/Config.php');

    $db = new Connection();
    $pdo = $db->connect();
    $gm = new GlobalMethods($pdo);
    $auth = new Auth($pdo);

    if (isset($_REQUEST['request'])) {
		$req = explode('/', rtrim($_REQUEST['request'], '/'));
		// $req = explode('/', rtrim(base64_decode($_REQUEST['request']), '/'));
	} else {
		$req = array("errorcatcher");
	}
    switch($_SERVER['REQUEST_METHOD']) {
		case 'POST':
			switch ($req[0]) {

                // *************************** ACCOUNT RELATED SIDE ****************************** //
				case 'accountLogin':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->accountLogin($d));
				break;

                case 'accountRegister':
                    $d = json_decode(base64_decode(file_get_contents("php://input")));
                    echo json_encode($auth->accountRegister($d));
                break;

                case 'accountVerifyEmail':
                	$d = json_decode(base64_decode(file_get_contents("php://input")));
                    echo json_encode($auth->accountVerifyEmail($d));
                break;

                 case 'accountUpdatePassword':
                	$d = json_decode(base64_decode(file_get_contents("php://input")));
                    echo json_encode($auth->accountUpdatePassword($d, $req[1]));
                break;



				// ******************************** USER SIDE *************************************//

                // ******************************** ADMIN SIDE *************************************//
				
                // ******************************** REPORTS SIDE ***********************************//

                // ******************************** ADDITONALS SIDE ********************************//

				default:
					http_response_code(403);
					echo "Invalid Route/Endpoint";
				break;
			}

		break;

		default:
			http_response_code(403);
			echo "Please contact the Systems Administrator";
		break;
	}
?>