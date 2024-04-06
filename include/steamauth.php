<?php
require 'openid.php';

$location = '/';

$apikey = array('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

if(isset($_GET['login']))
{
	$openid = new LightOpenID(''); 
	$openid->identity = 'https://steamcommunity.com/openid/';
	$openid->returnUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]?login";

	if(!$openid->mode)
	{
		header('Location: '.$openid->authUrl());
		exit;
	}
	
	if($openid->mode)
	{
		if ($openid->validate())
		{
			$openid_identity = trim(substr($_GET['openid_identity'],-17));
			$_SESSION['openid_identity'] = $openid_identity;
			
			$ip = get_client_ip();
			$salt = bin2hex( random_bytes( 20 ) );
			$cookie = base64_encode("$openid_identity:" .$salt);
			
			$expiration = 365*24*3600;
			setcookie('session', $cookie, time() + $expiration, '/', $_SERVER['SERVER_NAME']);
					
			shuffle( $apikey );
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$apikey[0].'&steamids='.$openid_identity);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
			$json = json_decode(curl_exec($ch), true);

			$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="alert-heading">Uh Oh!</h4><p>Looks like our request has timed out or has received an error. Steam could be down or under high load right now. Please try again later.</p></div>';
			
			if(isset($json['response']['players'][0]['steamid']))
			{
				$steamid = $json['response']['players'][0]['steamid'];
				$communityvisibilitystate = $json['response']['players'][0]['communityvisibilitystate'];
				$personaname = $db->real_escape_string($json['response']['players'][0]['personaname']);
				$avatar = $json['response']['players'][0]['avatarhash'];
				
				$query = 'INSERT INTO user (steamid,communityvisibilitystate,personaname,avatar,membersince,lastlogin,cookie) VALUES ("'.$steamid.'","'.$communityvisibilitystate.'","'.$personaname.'","'.$avatar.'",NOW(),NOW(),"'.$salt.'") ON DUPLICATE KEY UPDATE communityvisibilitystate=VALUES(communityvisibilitystate),personaname=VALUES(personaname),avatar=VALUES(avatar),lastlogin=VALUES(lastlogin),cookie=VALUES(cookie)';
				if($db->query($query))
				{
					$query = $db->query('select * from user where steamid = "'.$openid_identity.'" and refresh < DATE_SUB(NOW(), INTERVAL 1 MINUTE)');
					if($query->num_rows)
					{
						$db->query('UPDATE user SET refresh = NOW() WHERE steamid = "'.$openid_identity.'"');
						$_GET['steamid'] = $openid_identity;
						include(realpath('./crontab/GetFriendList.php'));
					}
						
					header('Location: '.$location);
					exit;
				}
				
				if($db->error)
				{
					$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="alert-heading">Uh Oh!</h4><p>MySQL error ' . $db->error . '</p></div>';
				}
			}
		}
	}
}

if(isset($_COOKIE['session']) && !empty($_COOKIE['session']))
{
	if(!isset($_SESSION['openid_identity']) && empty($_SESSION['openid_identity']))
	{
		$hash = base64_decode($_COOKIE['session']);
		list($openid_identity, $salt) = explode (':', $hash);
		
		$openid_identity = $db->real_escape_string($openid_identity);
		$salt = $db->real_escape_string($salt);
		
		$query = $db->query('SELECT steamid FROM user WHERE steamid = "'.$openid_identity.'" AND cookie = "'.$salt.'"');
		
		if($query->num_rows)
		{
			$_SESSION['openid_identity'] = $openid_identity;
			
			shuffle( $apikey );
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$apikey[0].'&steamids='.$openid_identity);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
			$json = json_decode(curl_exec($ch), true);
			sleep(2);

			$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="alert-heading">Uh Oh!</h4><p>Looks like our request has timed out or has received an error. Steam could be down or under high load right now. Please try again later.</p></div>';
			
			if(isset($json['response']['players'][0]['steamid']))
			{
				$steamid = $json['response']['players'][0]['steamid'];
				$communityvisibilitystate = $json['response']['players'][0]['communityvisibilitystate'];
				$personaname = $db->real_escape_string($json['response']['players'][0]['personaname']);
				$avatar = $json['response']['players'][0]['avatarhash'];
				
				$query = 'INSERT INTO user (steamid,communityvisibilitystate,personaname,avatar,lastlogin,cookie) VALUES ("'.$steamid.'","'.$communityvisibilitystate.'","'.$personaname.'","'.$avatar.'",NOW(),"'.$salt.'") ON DUPLICATE KEY UPDATE communityvisibilitystate=VALUES(communityvisibilitystate),personaname=VALUES(personaname),avatar=VALUES(avatar),lastlogin=VALUES(lastlogin),cookie=VALUES(cookie)';
				if($db->query($query))
				{
					header('Location: '.$location);
					exit;
				}
				
				if($db->error)
				{
					$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="alert-heading">Uh Oh!</h4><p>MySQL error ' . $db->error . '</p></div>';
				}
			}
		}
	}
}

if(isset($_SESSION['openid_identity']) && !empty($_SESSION['openid_identity']))
{
	$query = $db->query('select * from user where steamid = "'.$_SESSION['openid_identity'].'"');
	
	if($query->num_rows)
	{
		$me = $query->fetch_array(MYSQLI_BOTH);
	
		if(isset($_GET['refresh']))
		{
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
			{
				$query = $db->query('select * from user where steamid = "'.$_SESSION['openid_identity'].'" and refresh < DATE_SUB(NOW(), INTERVAL 1 MINUTE)');
				if($query->num_rows)
				{
					$db->query('UPDATE user SET refresh = NOW() WHERE steamid = "'.$_SESSION['openid_identity'].'"');
					
					$_GET['steamid'] = $me['steamid'];
					include(realpath('./crontab/GetFriendList.php'));
				}
				exit;
			}
		}
	}
}

if(isset($_GET['logout']))
{
	if(isset($_COOKIE['session']) && !empty($_COOKIE['session']))
	{
		setcookie('session', '', time()-3600, '/', $_SERVER['SERVER_NAME']);
		unset($_COOKIE['session']);
	}
	
	unset($_SESSION['openid_identity']);
	session_destroy();
   	session_unset(); 
	
	header('Location: '.$location);
	exit;
}
