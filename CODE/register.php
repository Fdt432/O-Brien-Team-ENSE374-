<?php
	include('conn.php');
	session_start();
 
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
	function check_input($data){
		$data=trim($data);
		$data=stripslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}
         $username=check_input($_POST['username']);
	$email=check_input($_POST['email']);
	$dob=check_input($_POST['dob']);
	$year = substr($dob, 0, 4);
	$year_int=intval($year);
	$age=2021 - $year_int;
	$password=check_input($_POST['password']);
        $confirm_password=check_input($_POST['confirm_password']);

        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);


	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  		$_SESSION['sign_msg'] = "Invalid email format";
  		header('location:signup.php');
	}


       elseif(!$lowercase || !$number || strlen($password) < 3) {
	$_SESSION['sign_msg'] = "Invalid password, it should include characters and numbers";
  		header('location:signup.php');
         
         }

	elseif($password != $confirm_password){
       	$_SESSION['sign_msg'] = "Password did not match";
  		header('location:signup.php');
       

}
elseif($age < 18 ){
	$_SESSION['sign_msg'] = "You have to be over 18";
   header('location:signup.php');


}
 
	else{
 
		$query=mysqli_query($conn,"select * from user1 where email='$email'");
		if(mysqli_num_rows($query)>0){
			$_SESSION['sign_msg'] = "Email is already taken! please signup using different email";
  			header('location:signup.php');
		}
		else{
		//depends on how you set your verification code
		$set='123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$code=substr(str_shuffle($set), 0, 12);
        var_dump($code);
		mysqli_query($conn,"insert into user1 (username,email, password,dob, code) values ('$username','$email', '$password','$dob', '$code')");
		$uid=mysqli_insert_id($conn);
		//default value for our verify is 0, means it is unverified
                 $_SESSION["username"] = $username; 
		//sending email verification
		$to = $email;
			$subject = "Sign Up Verification Email";
			$message = "
				<html>
				<head>
				<title>Verification Email</title>
				</head>
				<body>
				<h2>Thank you for Registering in Movies World Website.</h2>
				<p>Your Account:</p>
				<p>Email: ".$email."</p>
				<p>Code: ".$code."</p>
				<p>Password: ".$_POST['password']."</p>
                                <p>Username: ".$_POST['username']."</p>
				<p>Please click the link below to activate your account.</p>
				<h4><a href='http://localhost/activate.php?uid=$uid&code=$code'>Activate My Account</h4>
				</body>
				</html>
				";
			//dont forget to include content-type on header if your sending html
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= "From:black47light@gmail.com";
 
		mail($to,$subject,$message,$headers);
 
		$_SESSION['sign_msg'] = "Verification link has been sent to your email.";
  		header('location:signup.php');
 
  		}
	}
	}
?>





	
