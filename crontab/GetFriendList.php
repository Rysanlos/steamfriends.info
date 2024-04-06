<?php
set_time_limit(0);

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_GET['steamid']) && !empty($_GET['steamid']))
{
	check_friends($_GET['steamid']);
}

if(!isset($_GET['steamid']) && empty($_GET['steamid']))
{
	$db = new mysqli('localhost', 'user', 'password', 'database');
	$db->set_charset('utf8mb4');
	
	$fetch = $db->query('SELECT * FROM user');

	while($user = $fetch->fetch_assoc())
	{
		check_friends($user['steamid']);
	}
}

function check_friends($me)
{
	date_default_timezone_set('UTC');
	
	$apikey = array('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
	
	$db = new mysqli('localhost', 'user', 'password', 'database');
	$db->set_charset('utf8mb4');
	
	$me = $db->real_escape_string($me);

	$added = array();
	$pal = array();
	$friend = array();
	$friends = array();
	$friend_sinces = array();
	$friend_steamids = array();
	$history = array();
	$renamed = array();
	
	$query = $db->query('SELECT * FROM history WHERE type = 1 AND me = "'.$me .'";');
		
	while($row = $query->fetch_assoc())
	{	
		$history[] = array($row['them'], $row['since']);
	}
	
	shuffle( $apikey );
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key='.$apikey[0].'&steamid='.$me.'&relationship=friend');
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	
	$retry = 1;
	$code = 0;

	while( $retry >= 0 && ( $code == 0 || $code >= 400 ) )
	{
		$json = json_decode(curl_exec($ch), true);
		sleep(2);
		
		if(curl_errno($ch) == 28)
		{
			$code = '503';
		}
		else
		{
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		
		$retry--;
	}

	if(isset($json['friendslist']) && isset($json['friendslist']['friends']))
	{
		foreach($json['friendslist']['friends'] as $friend)
		{
			$friend_steamid = $friend['steamid'];
			$friend_steamids[] = $friend['steamid'];
			$fids[] = $friend['steamid'];
			
			$friend_since = date('Y-m-d H:i:s', $friend['friend_since']);
			$friend_sinces[$friend_steamid] = $friend_since;
			
			$friends[] = array($friend_steamid, $friend_since);
			$sync[$friend_steamid] = $friend['friend_since'];
		}
		
		$fids = array_chunk($friend_steamids, 100);
		
		for($i=0; $i <= count($fids) - 1; $i++)
		{
			shuffle( $apikey );
			curl_setopt($ch, CURLOPT_URL, 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$apikey[0].'&steamids=' . implode(',', $fids[$i]));
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			
			$retry = 1;
			$code = 0;

			while( $retry >= 0 && ( $code == 0 || $code >= 400 ) )
			{
				$json = json_decode(curl_exec($ch), true);
				sleep(2);
				
				if(curl_errno($ch) == 28)
				{
					$code = '503';
				}
				else
				{
					$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				}
				
				$retry--;
			}
			
			if(isset($json['response']) && !empty($json['response']['players']))
			{
				foreach($json['response']['players'] as $player)
				{
					$friend_steamid = $player['steamid'];
					$friend_personaname = $db->real_escape_string($player['personaname']);
					$friend_avatar = $player['avatarhash'];
					$friend_loccountrycode	= (isset($player['loccountrycode']) ? $player['loccountrycode'] : '');
					
					$pal[] = "('$friend_steamid', '$friend_personaname', '$friend_avatar', '$friend_loccountrycode')";
					
					$query = $db->query('SELECT current_name, current_avatar FROM history WHERE current_name <> "" AND me = "'.$me.'" AND them = "'.$friend_steamid.'" ORDER BY date DESC LIMIT 1');
					
					if($query->num_rows)
					{
						$row = $query->fetch_row();
						
						if($row[0] !== $player['personaname'])
						{
							$renamed_since = date('Y-m-d H:i:s', time());
							$previous = $db->real_escape_string($row[0]);
							$renamed[] = "('$me', '$friend_steamid', 3, '$previous', '$friend_personaname', '$row[1]', '$friend_avatar', '$renamed_since')";
						}
					}
					
					$added[] = "('$me', '$friend_steamid', 1, '$friend_sinces[$friend_steamid]', '$friend_personaname', '$friend_avatar', '$friend_sinces[$friend_steamid]')";
				}
			}
		}
		
		$unfriends = array_diff(array_map('json_encode', $history), array_map('json_encode', $friends));
		$unfriends = array_map('json_decode', $unfriends);
		
		foreach($unfriends as $unfriend)
		{
			$unfriend_since = date('Y-m-d H:i:s', time());
			if(isset($sync[$unfriend[0]]))
			{
				$unfriend_since = date('Y-m-d H:i:s', $sync[$unfriend[0]] - 1);
			}

			$query = $db->query('SELECT * FROM history WHERE type = 2 AND me = "'.$me.'" AND them = "'.$unfriend[0].'" AND since = "'.$unfriend[1].'" ORDER BY since DESC LIMIT 1');

			if($query->num_rows === 0)
			{
				shuffle( $apikey );
				curl_setopt($ch, CURLOPT_URL, 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$apikey[0].'&steamids='.$unfriend[0]);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
				curl_setopt($ch, CURLOPT_TIMEOUT, 5);
				
				$retry = 1;
				$code = 0;

				while( $retry >= 0 && ( $code == 0 || $code >= 400 ) )
				{
					$json = json_decode(curl_exec($ch), true);
					sleep(2);
					
					if(curl_errno($ch) == 28)
					{
						$code = '503';
					}
					else
					{
						$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					}
					
					$retry--;
				}
				
				if(isset($json['response']) && !empty($json['response']['players'][0]))
				{
					$friend_avatar = $json['response']['players'][0]['avatarhash'];
					$friend_personaname = $db->real_escape_string($json['response']['players'][0]['personaname']);
				
					$deleted[] = "('$me', '$unfriend[0]', 2, '$unfriend[1]', '$friend_personaname', '$friend_avatar', '$unfriend_since')";
				}
			}		
		}
			
		if(isset($added) && !empty($added))
		{
			$db->query('INSERT IGNORE INTO history (me,them,type,since,current_name,current_avatar,date) VALUES ' . implode(',', $added));
		}
		
		if(isset($deleted) && !empty($deleted))
		{
			$db->query('INSERT IGNORE INTO history (me,them,type,since,current_name,current_avatar,date) VALUES ' . implode(',', $deleted));
		}
		
		if(isset($renamed) && !empty($renamed))
		{
			$db->query('INSERT IGNORE INTO history (me,them,type,previous_name,current_name,previous_avatar,current_avatar,date) VALUES ' . implode(',', $renamed));
		}
		
		if(isset($pal) && !empty($pal))
		{
			$db->query('INSERT IGNORE INTO friend (steamid,personaname,avatar,loccountrycode) VALUES ' . implode(',', $pal) . ' ON DUPLICATE KEY UPDATE personaname=VALUES(personaname), avatar=VALUES(avatar), loccountrycode=VALUES(loccountrycode)');
			echo $db->error;
		}
		
		$friends = $db->real_escape_string(json_encode($friend_steamids));
		$db->query('UPDATE user SET friends = "'.$friends.'", friends_count = "'.count($friend_steamids).'", refresh = NOW() WHERE steamid = "'.$me.'"');
	}
	
	curl_close($ch);
	$db->close();
}
$db->close();
