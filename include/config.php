<?php
session_start();

date_default_timezone_set('UTC');

$db = new mysqli('localhost', 'user', 'password', 'database');
$db->set_charset('utf8mb4');

function get_client_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

$admins = array('765XXXXXXXXXXXXX');

$version = 13;
$version = sha1('v'.$version);
$version = strip_tags(stripslashes($version));
$version = str_replace('.', '', $version);
$version = strrev(str_replace('/', '', $version));
$version = substr($version, 0, 12);

function base64url_encode($data) { 
	return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 

function base64url_decode($data) { 
	return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
}

function convert_steamid_64bit_to_32bit($id)
{
	$result = substr($id, 3) - 61197960265728;
	return (string) $result;
}

function convert_steamid_32bit_to_64bit($id)
{
	$result = '765'.($id + 61197960265728);
	return (string) $result;
}

function convert_steamid_32bit_to_steamid($id)
{
	if($id % 2 == 1)
	{
	   $y = 1;
	}
	elseif ($id % 2 == 0)
	{
	   $y = 0;
	}
	
	$result = 'STEAM_0:'.$y.':'.floor($id / 2);
	return $result;
}

function friendid($id)
{
	if(strpos(strtolower($id), 'team_'))
	{
		$steamid = $id;
	
		$x = (int)substr($steamid, 6, 1);
		$y = (int)substr($steamid, 8, 1);
		$z = (int)substr($steamid, 10);
		$z2 = $z * 2;
		$z3 = $z2 + 7960265728;
		$z4 = $z3 + $y;
		
		return '7656119'.$z4;
	}
	elseif(preg_match('/(.*?):(.*?):(\d+)/', $id, $out))
	{
		$result = '765'.($out[3] + 61197960265728);
		return (string) $result;
	}
	else
	{
		return $id;
	}
}
