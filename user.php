<?php
set_time_limit(0);
require realpath(__DIR__ . '/include/config.php');

error_reporting(-1);
ini_set('display_errors', 0);

require realpath(__DIR__ . '/include/steamauth.php');
require realpath(__DIR__ . '/include/paginaton.php');

if(isset($me) && !empty($me))
{
	$theme = 'light';
	$navbar = 'light';
	
	if($me['theme'] == 1)
	{
		$theme = 'night';
		$navbar = 'dark';
	}
	
	if(in_array($me['steamid'], $admins))
	{
		$admin = true;
	}
	
	if(!in_array($me['steamid'], $admins))
	{
		header('HTTP/1.0 404 Not Found');
		exit;
	}
}
if(!isset($me) && empty($me))
{
	header('HTTP/1.0 404 Not Found');
	exit;
}

if(isset($_POST['filter']))
{
	if(isset($_SESSION['openid_identity']) && !empty($_SESSION['openid_identity']))
	{
		$added = 0;
		if(isset($_POST['added']))
		{
			$added = 1;
		}
		
		$deleted = 0;
		if(isset($_POST['deleted']))
		{
			$deleted = 1;
		}
		
		$renamed = 0;
		if(isset($_POST['renamed']))
		{
			$renamed = 1;
		}
		
		$db->query('UPDATE user SET added = "'.$added.'", deleted = "'.$deleted.'", renamed = "'.$renamed.'" WHERE steamid = "'.$_SESSION['openid_identity'].'"');
	}
}
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="/css/bootstrap.slim.min.css?v<?php echo $version; ?>">
		<link rel="stylesheet" href="/css/glyphicons.min.css?v<?php echo $version; ?>">
		<link rel="stylesheet" href="/css/main.css?v<?php echo $version; ?>">
	</head>
	<body class="<?php echo $theme; ?>">
		<?php
		
		if(isset($me) && !empty($me)){
		if(isset($_GET['user']) && !empty($_GET['user'])){
		
		$userid = $db->real_escape_string($_GET['user']);
		$query = $db->query('select * from user where steamid = "'.$userid.'"');
		$user = $query->fetch_array(MYSQLI_BOTH);
			
		if($user['friends_count'] < 1 or $user['communityvisibilitystate'] < 3)
		{
			$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="alert-heading">Uh Oh!</h4><p>Please set your Steam profile and your friend list to public.</p></div>';
		}
		
		$url = '/user/'.$userid.'/';
		
		$them = '';
		if(isset($_GET['profiles']) && !empty($_GET['profiles']))
		{
			$url = '/user/'.$userid.'/profiles/'.$_GET['profiles'].'/';
			$profiles = $db->real_escape_string($_GET['profiles']);
			$them = ' AND them = "'.$profiles.'"';
		}
		
		$search = '';
		if(!isset($_GET['profiles']) && empty($_GET['profiles']))
		{
			if(isset($_POST['search']) && !empty($_POST['search']))
			{
				header('Location: /user/'.$userid.'/search/'.rawurlencode($_POST['search']).'/');
				exit;
			}
			
			if(isset($_GET['search']) && !empty($_GET['search']))
			{
				$term = $db->real_escape_string(str_replace(' ','%',rawurldecode($_GET['search'])));
				
				$search = ' AND (them LIKE "%'.$term.'%" OR previous_name LIKE "%'.$term.'%" OR current_name LIKE "%'.$term.'%")';
				
				$url = '/user/'.$userid.'/search/'.$_GET['search'].'/';
			}
		}
		
		$page = (int)(!isset($_GET['page']) ? 1 : $_GET['page']);
		if ($page <= 0) $page = 1;
		 
		$per_page = $me['per_page'];
		 
		$startpoint = ($page * $per_page) - $per_page;
		
		$type = '';
		
		if($me['added'] == 0)
		{
			$type .= ' AND type <> 1';
		}
		if($me['deleted'] == 0)
		{
			$type .= ' AND type <> 2';
		}
		if($me['renamed'] == 0)
		{
			$type .= ' AND type <> 3';
		}
		
		$statement = "history WHERE me = '$user[steamid]' AND type <> 0 $search $them $type"; // Change `records` according to your table name.
		   
		$pagination = pagination($statement,$per_page,$page,$url);
		
		$history = array();
		$query = $db->query('SELECT * FROM history WHERE me = "'.$user['steamid'] .'" AND type <> 0 '.$search.$them.$type.' ORDER BY date DESC LIMIT '.$startpoint.','.$per_page);
		?>
		<?php include 'include/navbar.php'; ?>

		<div class="container py-4">
			<?php 
			if(isset($alert) && !empty($alert))
			{
				echo $alert;
			}
			
			?>
			
			<div class="row my-4">
				<div class="col-avatar">
					<a href="/user/<?php echo $user['steamid']; ?>/"><img class="img-fluid rounded" src="https://avatars.steamstatic.com/<?php echo $user['avatar']; ?>_full.jpg" alt=""/></a>
				</div>
				<div class="col-persona">
					<p class="h3 my-2"><a href="/user/<?php echo $user['steamid']; ?>/"><?php echo htmlentities($user['personaname']); ?></a></p>
					<div class="dropdown my-3">
						<button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Actions
						</button> <!--<a href="/country/" class="btn btn-sm btn-outline-dark">Friends by country</a>-->
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<?php echo ($admin === true ? '<a class="dropdown-item" href="/members/">Members</a>' : ''); ?>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="/settings/"><span class="glyphicon glyphicon-cog"></span>&nbsp;Settings</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="/logout/"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Logout</a>
						</div>
					</div>
					<p class="h5 my-2">Total friends: <?php echo $user['friends_count']; ?></p>
				</div>
			</div>
			<?php
					
			if(isset($_GET['profiles']) && !empty($_GET['profiles']))
			{
				if($query->num_rows)
				{
					$them = $db->query('SELECT * FROM friend WHERE steamid = "'.$profiles.'"');
					
					if($them->num_rows)
					{
						$them = $them->fetch_array(MYSQLI_BOTH);
						
						$steamid32 = convert_steamid_64bit_to_32bit($profiles);
						$steamid3 = '[U:1:'.$steamid32.']';
						$steamid32bit = convert_steamid_32bit_to_steamid($steamid32);	
			?>
			
			<p class="h3">History of <?php echo htmlentities($user['personaname']); ?> friend</p>
			<a href="http://steamcommunity.com/profiles/<?php echo $profiles; ?>" target="_blank"><h3><?php echo htmlentities($them['personaname']); ?>&nbsp<span class="glyphicon glyphicon-new-window"></span></h3></a>
	
			<div class="row my-2">
				<div class="col-lg-4 col-md-5 col-sm-12">
					<table class="table table-id text-sm">
						<tbody>
							<tr><td><strong>SteamID3</strong></td> <td><span class=""><?php echo $steamid32bit; ?></span></td></tr>
							<tr><td><strong>SteamID32</strong></td> <td><span class=""><?php echo $steamid3; ?></span></td></tr>
							<tr><td><strong>SteamID64</strong></td> <td><span class=""><?php echo $profiles; ?></span></td></tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php
					}
				}
			}
			?>
			
			<div class="row my-4">
				<div class="col-md-8">
					<?php echo $pagination; ?>
					
				</div>
				<div class="col-md-4 text-right">
					<div class="dropdown float-right ml-2 mb-2">
						<button class="btn btn-outline-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Filter
						</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							<form class="form" name="filterForm" method="POST" action="">
								<input type="hidden" name="filter">
								<label class="dropdown-item">
									<input type="checkbox" name="added" <?php echo ($me['added'] == 1) ? 'checked' : ''; ?>>&nbsp;Added
								</label>
								<label class="dropdown-item">
									<input type="checkbox" name="deleted" <?php echo ($me['deleted'] == 1) ? 'checked' : ''; ?>>&nbsp;Deleted
								</label>
								<label class="dropdown-item">
									<input type="checkbox" name="renamed" <?php echo ($me['renamed'] == 1) ? 'checked' : ''; ?>>&nbsp;Renamed
								</label>
								<label class="dropdown-item">
									<button type="submit" class="btn btn-secondary">Apply</button>
								</label>
							</form>
						</div>
					</div>
					<?php
					if(!isset($_GET['profiles']) && empty($_GET['profiles']))
					{
					?>
					
					<form class="float-right" method="POST" action="">
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
							</span>
							<input class="form-control search" type="text" name="search" placeholder="Search" aria-label="Search" value="<?php echo (isset($_GET['search']) ? rawurldecode($_GET['search']) : ''); ?>">
						</div>
					</form>
					<?php
					}
					?>
					
				</div>
			</div>
<?php		

			while($row = $query->fetch_assoc())
			{
				$previous_avatar = $row['previous_avatar'];
				$current_avatar = $row['current_avatar'];
				if($row['type'] == 1)
				{
					$history[] = array('date'=>$row['date'], 'text'=>'&nbsp;<a href="/user/'.$userid.'/profiles/'.$row['them'].'/"><img width="32" height="32" class="rounded-circle mx-1" src="https://avatars.steamstatic.com/'.$current_avatar.'.jpg" alt=""/></a>&nbsp;<span class="added"><a href="/user/'.$userid.'/profiles/'.$row['them'].'/">'.htmlentities($row['current_name']).'</a>&nbsp;<strong>was added to friend list</strong></span>');
				}
				if($row['type'] == 2)
				{
					$history[] = array('date'=>$row['date'], 'text'=>'&nbsp;<a href="/user/'.$userid.'/profiles/'.$row['them'].'/"><img width="32" height="32" class="rounded-circle mx-1" src="https://avatars.steamstatic.com/'.$current_avatar.'.jpg" alt=""/></a>&nbsp;<span class="deleted"><a href="/user/'.$userid.'/profiles/'.$row['them'].'/">'.htmlentities($row['current_name']).'</a>&nbsp;<strong>was deleted from friend list</strong></span>');
				}
				if($row['type'] == 3)
				{
					$history[] = array('date'=>$row['date'], 'text'=>'&nbsp;<a href="/user/'.$userid.'/profiles/'.$row['them'].'/"><img width="32" height="32" class="rounded-circle mx-1" src="https://avatars.steamstatic.com/'.$previous_avatar.'.jpg" alt=""/></a>&nbsp;<span class="renamed"><a href="/user/'.$userid.'/profiles/'.$row['them'].'/">'.htmlentities($row['previous_name']).'</a>&nbsp;<strong>renamed to</strong></span>&nbsp;<a href="/user/'.$userid.'/profiles/'.$row['them'].'/"><img width="32" height="32" class="rounded-circle mx-1" src="https://avatars.steamstatic.com/'.$current_avatar.'.jpg" alt=""/></a>&nbsp;<span class="renamed"><a href="/user/'.$userid.'/profiles/'.$row['them'].'/">'.htmlentities($row['current_name']).'</a></span>');
				}
			}

			function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
				$sort_col = array();
				foreach ($arr as $key=> $row) {
					$sort_col[$key] = $row[$col];
				}

				array_multisort($sort_col, $dir, $arr);
			}


			array_sort_by_column($history, 'date');

			$old_day = '';
			foreach($history as $date => $value)
			{
				$tz = $me['timezone'];
				$seconds = (($tz[1].$tz[2] * 3600) + ($tz[4].$tz[5] * 60));			
				
				$result = 0;
				switch($tz[0])
				{
					case '+':
						$result = strtotime($value['date']) + $seconds;
						break;

					case '-';
						$result = strtotime($value['date']) - $seconds;
						break;
				}
							
				$day = date('d.m.Y', $result);			
				$hour = date('H:i', $result);
				
				if($day != $old_day)
				{
					echo '			<p class="date my-4"><span class="text">'.$day.'</span><span class="bar">&nbsp;</span></p>'."\r\n";
				}
				
				echo '			<p class="my-3">['.$hour.']'.$value['text'].'</p>'."\r\n";
				
				$old_day = $day;
			}
		
			?>
			
			<?php echo '<br>'.$pagination.'<br>'; ?>
			
						
			<?php include 'include/footer.php'; ?>
			
		</div>
		<?php }} ?><script src="/js/jquery-3.2.1.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/popper.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/bootstrap.slim.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/main.js?v<?php echo $version; ?>"></script>
	</body>
</html>
