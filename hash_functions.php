<?php

//Takes a password and returns the salted hash
//$password - the password to hash
//returns - the hash of the password (128 hex characters)
function HashPassword($password)
{	
	$password = md5($password);
	$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); //get 256 random bits in hex
	$hash = hash("sha512", $salt . $password); //prepend the salt, then hash
	//store the salt and hash in the same string, so only 1 DB column is needed
	$final = $salt . $hash; 
	return $final;
}


//Validates a password
//returns true if hash is the correct hash for that password
//$hash - the hash created by HashPassword (stored in your DB)
//$password - the password to verify
//returns - true if the password is valid, false otherwise.


function ValidatePassword($password, $correctHash)
// hopefully we should never use this, since it receives plaintext passwords from client
// super unsecure.
{	
	$password = md5($password);
	$salt = substr($correctHash, 0, 64); //get the salt from the front of the hash
	$validHash = substr($correctHash, 64, 128); //the SHA512

	$testHash = hash("sha512", $salt . $password); //hash the password being tested
	
	//if the hashes are exactly the same, the password is valid
	return $testHash === $validHash;
}


function ValidateHashedPassword($md5Password, $correctHash)
{	
	$salt = substr($correctHash, 0, 64); //get the salt from the front of the hash
	$validHash = substr($correctHash, 64, 128); //the SHA512

	$testHash = hash("sha512", $salt . $md5password); //hash the password being tested
	
	//if the hashes are exactly the same, the password is valid
	return $testHash === $validHash;
}
	
	
function Login()
{
	if(empty($_POST['username']))
	{
		$error = "Username is empty!";
		return array (false, $error);
	}
		
	if(empty($_POST['password']))
	{
		$error = "Password is empty!";
		return array (false, $error);
	}
		
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	
	$query = sprintf("SELECT password, first_name, preferred_name, statusID FROM users WHERE username = '%s'", $username);
	
	$get_hash = mysql_query($query);
	if (!$get_hash) {
		die ('This is an error message: ' . mysql_error());
	}
	$row = mysql_fetch_array($get_hash, MYSQL_NUM);
	
	if(!$row){
		$error = "User not found!";
		return array (false, $error);
	}
	
	$hash = $row[0];
	$name = $row[1];
	$pref = $row[2];
	$statusID = $row[3];
	
	if(!ValidatePassword($password,$hash))
	{
		$error = "Password not correct!";
		return array (false, $error);
	}
	/* Ensure that we don't already have a session */
	session_set_cookie_params(86400, '/', '.wsbf.net');
	if(!session_id())
		session_start();
	
	$query = sprintf("SELECT positionID FROM staff WHERE username = '%s' AND start_date < NOW() AND end_date > NOW()", $username);
	
	$get_position = mysql_query($query);
	if (!$get_position) {
		die ('This is an error message: ' . mysql_error());
	}
	
	if(mysql_num_rows ($get_position) > 0){
		
		$num = 0;
		
		while($row = mysql_fetch_array($get_position, MYSQL_NUM)){
			$positionID[$num] = $row[$num];
			$num++;
			
		}
		
		$_SESSION['positionID'] = $positionID;
	}
		
	
		
	$_SESSION['username'] = $username;
	$_SESSION['preferred_name'] = $pref;
	$_SESSION['statusID'] = $statusID;
	
	
	return array (true, $name);
}	

/*
//moving pass from the OLD DB, they are already MD5'd, so we wont MD5 them again THESE ARE ONLY FOR MOVING THEM INTO THE NEW DB
//when someone logs in on the website, the password wont be MD5'd yet
function HashOLDPassword($password)
{	
	$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); //get 256 random bits in hex
	$hash = hash("sha512", $salt . $password); //prepend the salt, then hash
	//store the salt and hash in the same string, so only 1 DB column is needed
	$final = $salt . $hash; 
	return $final;
}

function ValidateOLDPassword($password, $correctHash)
{	
	$salt = substr($correctHash, 0, 64); //get the salt from the front of the hash
	$validHash = substr($correctHash, 64, 128); //the SHA512

	$testHash = hash("sha512", $salt . $password); //hash the password being tested
	
	//if the hashes are exactly the same, the password is valid
	return $testHash === $validHash;
}
*/

?>
