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


// Vérifier la connexion
if($conn === false){
    die("ERREUR : Impossible de se connecter. " . mysqli_connect_error());
}

session_start();


// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if(!isset($_SESSION["username"])){

    header("Location: login.php");
    exit();
}
$username = $_SESSION["username"];


if (isset($_POST["verify_email"]))
{
    $query = "SELECT * FROM `users` WHERE username='$username' ";
    $result = mysqli_query($conn,$query) or die(mysqli_error());
    $user = mysqli_fetch_row($result);

    $email = $_POST["email"];
    $verification_input = $_POST["verification_input"];

    // mark email as verified
    //$query = "UPDATE users SET email_verified_at = NOW() WHERE email = '" . $email . "' AND verification_code = '" . $verification_code . "'";
    $result  = mysqli_query($conn, $query);


    if ($user[4] != $verification_input)
    {
        header("Location: email_verification.php");
        exit();
    }

    header("Location: index.php");
    exit();
}
?>
<form class="box" action="" method="post" name="login">
<h1 class="box-title">Connexion</h1>

    <form method="POST">
        <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>" required>
        <input type="text" name="verification_input" placeholder="Enter verification code" required />

        <input type="submit" name="verify_email" value="Verify Email">
    </form>
<?php if (! empty($message)) { ?>
    <p class="errorMessage"><?php echo $message; ?></p>
<?php } ?>
</form>
</body>
</html>