<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//require('config.php');

require 'vendor/autoload.php';


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'registration');

// Connexion à la base de données MySQL
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$mail = new PHPMailer(true);
// Vérifier la connexion

if($conn === false){
    die("ERREUR : Impossible de se connecter. " . mysqli_connect_error());
}
echo json_encode($_POST['username']);
session_start();
if (isset($_POST['username'])){
	$username = stripslashes($_REQUEST['username']);
	//$username = mysqli_real_escape_string($conn, $username);
	$password = stripslashes($_REQUEST['password']);
    $hash = hash('sha256', $password);
	//$password = mysqli_real_escape_string($conn, $password);
    $query = "SELECT * FROM `users` WHERE username='$username' and password='".hash('sha256', $password)."'";


	$result = mysqli_query($conn,$query) or die(mysqli_error());
    $row = mysqli_num_rows($result);
    $user = mysqli_fetch_row($result);
	if($row==1){

        $username = $user[1];
        $email = $user[2];
        $hash = $user[3];

        try {
            //Enable verbose debug output
            $mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;

            //Send using SMTP
            $mail->isSMTP();

            //Set the SMTP server to send through
            $mail->Host = 'smtp.gmail.com';

            //Enable SMTP authentication
            $mail->SMTPAuth = true;

            //SMTP username
            $mail->Username = 'nathanaelkaaa@gmail.com';

            //SMTP password
            $mail->Password = 'wyjjnvrwtpjbzlsk';

            //Enable TLS encryption;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;//PHPMailer::ENCRYPTION_STARTTLS //ssl //PHPMailer::ENCRYPTION_SMTPS

            //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->Port = 587; //587

            //Recipients
            $mail->setFrom('nathanaelkaaa@gmail.com');

            //Add a recipient
            $mail->addAddress($email, $username);

            //Set email format to HTML
            $mail->isHTML(true);

            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

            $mail->Subject = 'Email verification';
            $mail->Body = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';

            $mail->send();

            $date = date("Y-m-d h:i:sa");
            $query = "UPDATE users SET verification_code='$verification_code' , email_verified_at=NOW() WHERE username='$username'";
            mysqli_query($conn, $query);
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }


	    $_SESSION['username'] = $username;
	    header("Location: email_verification.php");
	}else{
		$message = "Le nom d'utilisateur ou le mot de passe est incorrect.";
	}
}
?>
<form class="box" action="" method="post" name="login">
<h1 class="box-title">Connexion</h1>
<input type="text" class="box-input" name="username" placeholder="Nom d'utilisateur">
<input type="password" class="box-input" name="password" placeholder="Mot de passe">
<input type="submit" value="Connexion " name="submit" class="box-button">

</form>
</body>
</html>