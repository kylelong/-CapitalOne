<?php
//PHP server configuration
set_time_limit(0);
ini_set('default_socket_timeout', 300);
session_start();

//Make Constants using define. 
define('clientId', '//your unique client id given by Instagram');
define('clientSecret', '//your unique client secret given by Instagram');
define('redirectURI', 'http://localhost:5740/capitalonedata/main.php');
define('ImageDirectory', 'images/');
 define('access_token', '//your unique access token given by Instagram');

// This function connects to instagram 
function connectToInstagram($url)
{
	$ch = curl_init(); //ch is curl handle

	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => 2,

	));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;

}

function getFollowedBy($userId)
{
  $link = 'https://api.instagram.com/v1/users/'.$userId.'/?access_token='.access_token;
  $instagramInfo = connectToInstagram($link);
  $results = json_decode($instagramInfo, true);
  return $results['data']['counts']['followed_by'];

}
function getFollows($userId)
{
   $FollowsUrl = 'https://api.instagram.com/v1/users/'.$userId.'/?access_token='.access_token;
   $instagramInfo = connectToInstagram($FollowsUrl);
   $results = json_decode($instagramInfo, true);
   return $results['data']['counts']['follows'];

}


function getHashTag()
{
	
    $url2 = 'https://api.instagram.com/v1/tags/capitalone/media/recent?access_token='.access_token.'&count=50';
  	$instagramInfo = connectToInstagram($url2);
    $results = json_decode($instagramInfo, true);

    foreach ($results['data'] as $value) 
    {
    	
    	$userId = $value['caption']['from']['id'];
    	$caption = $value['caption']['text'];
    	$likes = $value['likes']['count'];

   
    	echo "User Name: ".$value['user']['username'].'</br>';
    	echo "Full Name: ".$value['user']['full_name'].'</br>';
    	echo "Follows: ".getFollowedBy($userId).'</br>';
    	echo "Followed By: ".getFollows($userId).'</br>';
    	echo "Caption: ".$value['caption']['text'].'</br>'; 
    	echo"Likes: ".$likes.'</br></br>';
    	$likes = $value['likes']['count'];
    	if($likes == 0)
    	{ 
    		echo "Post Rating: Negative :(".'</br>'; //Thinking most will not give likes to a negative post
    	}
    	elseif($likes > 0 && $likes <= 30)
    	{

    		echo "Post Rating: Neutral".'</br>';
    	}
    	else
    	{
    		echo "Post Rating: Positive".'</br>';
    	}
    

    	
    	
    }


    	
}
   






//If the code exist, you are qunthenticated
if (isset($_GET['code'])){
	$code = ($_GET['code']);
	$url = 'https://api.instagram.com/oauth/access_token';
	$access_token_settings = array('client_id' => clientId,
									 'client_secret' => clientSecret,
									 'grant_type' => 'authorization_code',
									 'redirect_uri' => redirectURI,
									 'code' => $code
									 );
//Use the cURL library to call other APIs	
$curl = curl_init($url); //get some URL by setting a URL to get data from instagrams api
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings); //setting the POSTFIELDS to the access_token_array
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // it is 1 to get strings back
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //should be true in most cases


$result = curl_exec($curl);
curl_close($curl);

$results = json_decode($result, true);
//print_r($results);


getHashTag();


}
else{
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="Usefule capital one data">
    <meta name="viewport" content="width=device=width, initial=scale=1">
    <meta name ="author" description = "Kyle Long">
	<title> Capital One Data</title>
</head>
<body>
   <!--Aunthentication link to view this unique capital one data 
    using basic html-->
   <a href="https:api.instagram.com/oauth/authorize/?client_id=<?php echo clientId; ?>&redirect_uri=<?php echo redirectURI;?>&response_type=code">
   <img src ="http://wrm5sysfkg-flywheel.netdna-ssl.com/wp-content/uploads/2015/08/Capital_One-Logo.jpg" 
   alt="Capital One" height="300" width="400"/> </a> <!-- Click the capital one log for instagram data -->
   
</body
</html>
<?php
}
?>
