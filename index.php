<?php
set_time_limit(0);
require realpath(__DIR__ . '/include/config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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

if(isset($_GET['settings']))
{
	if(isset($_POST) && !empty($_POST))
	{
		if(isset($_POST['timezone']) && !empty($_POST['timezone']))
		{
			switch($_POST['timezone'])
			{
				case '-12:30':
					$timezone = '-12:30';
					break;
				case '-12:00':
					$timezone = '-12:00';
					break;
				case '-11:30':
					$timezone = '-11:30';
					break;
				case '-11:00':
					$timezone = '-11:00';
					break;
				case '-10:30':
					$timezone = '-10:30';
					break;
				case '-10:00':
					$timezone = '-10:00';
					break;
				case '-09:30':
					$timezone = '-09:30';
					break;
				case '-09:00':
					$timezone = '-09:00';
					break;
				case '-08:30':
					$timezone = '-08:30';
					break;
				case '-08:00':
					$timezone = '-08:00';
					break;
				case '-07:30':
					$timezone = '-07:30';
					break;
				case '-07:00':
					$timezone = '-07:00';
					break;
				case '-06:30':
					$timezone = '-06:30';
					break;
				case '-06:00':
					$timezone = '-06:00';
					break;
				case '-05:30':
					$timezone = '-05:30';
					break;
				case '-05:00':
					$timezone = '-05:00';
					break;
				case '-04:30':
					$timezone = '-04:30';
					break;
				case '-04:00':
					$timezone = '-04:00';
					break;
				case '-03:30':
					$timezone = '-03:30';
					break;
				case '-03:00':
					$timezone = '-03:00';
					break;
				case '-02:30':
					$timezone = '-02:30';
					break;
				case '-02:00':
					$timezone = '-02:00';
					break;
				case '-01:30':
					$timezone = '-01:30';
					break;
				case '-01:00':
					$timezone = '-01:00';
					break;
				case '+00:00':
					$timezone = '+00:00';
					break;
				case '+00:30':
					$timezone = '+00:30';
					break;
				case '+01:00':
					$timezone = '+01:00';
					break;
				case '+01:30':
					$timezone = '+01:30';
					break;
				case '+02:00':
					$timezone = '+02:00';
					break;
				case '+02:30':
					$timezone = '+02:30';
					break;
				case '+03:00':
					$timezone = '+03:00';
					break;
				case '+03:30':
					$timezone = '+03:30';
					break;
				case '+04:00':
					$timezone = '+04:00';
					break;
				case '+04:30':
					$timezone = '+04:30';
					break;
				case '+05:00':
					$timezone = '+05:00';
					break;
				case '+05:30':
					$timezone = '+05:30';
					break;
				case '+06:00':
					$timezone = '+06:00';
					break;
				case '+06:30':
					$timezone = '+06:30';
					break;
				case '+07:00':
					$timezone = '+07:00';
					break;
				case '+07:30':
					$timezone = '+07:30';
					break;
				case '+08:00':
					$timezone = '+08:00';
					break;
				case '+08:30':
					$timezone = '+08:30';
					break;
				case '+09:00':
					$timezone = '+09:00';
					break;
				case '+09:30':
					$timezone = '+09:30';
					break;
				case '+10:00':
					$timezone = '+10:00';
					break;
				case '+10:30':
					$timezone = '+10:30';
					break;
				case '+11:00':
					$timezone = '+11:00';
					break;
				case '+11:30':
					$timezone = '+11:30';
					break;
				case '+12:00':
					$timezone = '+12:00';
					break;
				case '+12:30':
					$timezone = '+12:30';
					break;
				case '+13:00':
					$timezone = '+13:00';
					break;
				case '+13:30':
					$timezone = '+13:30';
					break;
				case '+14:00':
					$timezone = '+14:00';
					break;
				case '+14:30':
					$timezone = '+14:30';
					break;
				default:
				   $timezone = '+00:00';
			}
			
			$db->query('UPDATE user SET timezone = "'.$timezone.'" WHERE steamid = "'.$_SESSION['openid_identity'].'"');
		}

		if(isset($_POST['per_page']) && !empty($_POST['per_page']))
		{
			switch($_POST['per_page'])
			{
				case '10':
					$per_page = '10';
					break;
				case '15':
					$per_page = '15';
					break;
				case '20':
					$per_page = '20';
					break;
				case '25':
					$per_page = '25';
					break;
				case '30':
					$per_page = '30';
					break;
				case '50':
					$per_page = '50';
					break;
				case '100':
					$per_page = '100';
					break;
				case '150':
					$per_page = '150';
					break;
				case '200':
					$per_page = '200';
					break;
				case '300':
					$per_page = '300';
					break;
				case '500':
					$per_page = '500';
					break;
				default:
				   $per_page = '25';
			}
			
			$db->query('UPDATE user SET per_page = "'.$per_page.'" WHERE steamid = "'.$_SESSION['openid_identity'].'"');
		}
		
		if(isset($_POST['theme']) && !empty($_POST['theme']))
		{
			switch($_POST['theme'])
			{
				case 'light':
					$theme = '0';
					break;
				case 'night':
					$theme = '1';
					break;
				default:
				   $theme = '1';
			}
			
			$db->query('UPDATE user SET theme = "'.$theme.'" WHERE steamid = "'.$_SESSION['openid_identity'].'"');
		}
		
		header('Location: /');
		exit;
	}
}

require realpath(__DIR__ . '/include/steamauth.php');
require realpath(__DIR__ . '/include/paginaton.php');

$theme = 'light';
$navbar = 'light';
if(isset($me) && !empty($me))
{
	if($me['theme'] == 1)
	{
		$theme = 'night';
		$navbar = 'dark';
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
		if(!isset($_GET['settings'])){
			
		if($me['friends_count'] < 1 or $me['communityvisibilitystate'] < 3)
		{
			$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="alert-heading">Uh Oh!</h4><p>Please set your Steam profile and your friend list to public.</p></div>';
		}
		
		$url = '/';
		
		$them = '';
		if(isset($_GET['profiles']) && !empty($_GET['profiles']))
		{
			$url = '/profiles/'.$_GET['profiles'].'/';
			$profiles = $db->real_escape_string($_GET['profiles']);
			$them = ' AND them = "'.$profiles.'"';
		}
		
		$search = '';
		if(!isset($_GET['profiles']) && empty($_GET['profiles']))
		{
			if(isset($_POST['search']) && !empty($_POST['search']))
			{	
				header('Location: /search/'.rawurlencode($_POST['search']).'/');
				exit;
			}
			
			if(isset($_GET['search']) && !empty($_GET['search']))
			{
				$term = $db->real_escape_string(str_replace(' ','%',rawurldecode($_GET['search'])));
				
				$search = ' AND (them LIKE "%'.$term.'%" OR previous_name LIKE "%'.$term.'%" OR current_name LIKE "%'.$term.'%")';
				
				$url = '/search/'.$_GET['search'].'/';
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
		
		$statement = "history WHERE me = '$me[steamid]' AND type <> 0 $search $them $type"; // Change `records` according to your table name.
		   
		$pagination = pagination($statement,$per_page,$page,$url);
		
		$history = array();
		$query = $db->query('SELECT * FROM history WHERE me = "'.$me['steamid'] .'" AND type <> 0 '.$search.$them.$type.' ORDER BY date DESC LIMIT '.$startpoint.','.$per_page);
		
		}
		?>
		<?php include 'include/navbar.php'; ?>

		<div class="container py-4">
			<?php 
			if(isset($alert) && !empty($alert))
			{
				echo $alert;
			}
			
			include 'include/user.php';
			?>
			
			<div id="ajax" class="my-3"><div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div></div></div>
			<?php
			if(isset($_GET['settings']))
			{
			?>
			
			<form action="" method="POST">
				<div class="form-group row">
					<label for="inputEmail3" class="col-avatar col-form-label">Timezone</label>
					<div class="col">
						<select class="form-control" name="timezone">
							<?php
							for ($i = -12; $i < 0; $i++)
							{
								for($j = 30; $j >= 0; $j -=30)
								{																	
									$minute = $j;
									if ($j < 10)
									{
										$minute = str_pad($j, 2, '0', STR_PAD_LEFT);
									}
									
									$selected = ($me['timezone'] == sprintf('%+03d', $i).':'.$minute) ? 'selected' : '';
									echo '<option value="'.sprintf('%+03d', $i).':'.$minute.'" '.$selected.'>'.sprintf('%+03d', $i).':'.$minute.'</option>';
								}
							}
							
							for ($i = 0; $i <= 14; $i++)
							{
								for ($j = 0; $j <= 30; $j+=30)
								{								
									$minute = $j;
									if ($j < 10)
									{
										$minute = str_pad($j, 2, '0', STR_PAD_LEFT);
									}
									
									$selected = ($me['timezone'] == sprintf('%+03d', $i).':'.$minute) ? 'selected' : '';
									echo '<option value="'.sprintf('%+03d', $i).':'.$minute.'" '.$selected.'>'.sprintf('%+03d', $i).':'.$minute.'</option>';
								}
							}
							?>
							
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label for="inputPassword3" class="col-avatar col-form-label">Entries per page</label>
					<div class="col">
						<select class="form-control" name="per_page">
							<option value="10" <?php echo ($me['per_page'] == 10) ? 'selected' : ''; ?>>10</option>
							<option value="15" <?php echo ($me['per_page'] == 15) ? 'selected' : ''; ?>>15</option>
							<option value="20" <?php echo ($me['per_page'] == 20) ? 'selected' : ''; ?>>20</option>
							<option value="25" <?php echo ($me['per_page'] == 25) ? 'selected' : ''; ?>>25</option>
							<option value="30" <?php echo ($me['per_page'] == 30) ? 'selected' : ''; ?>>30</option>
							<option value="50" <?php echo ($me['per_page'] == 50) ? 'selected' : ''; ?>>50</option>
							<option value="100" <?php echo ($me['per_page'] == 100) ? 'selected' : ''; ?>>100</option>
							<option value="150" <?php echo ($me['per_page'] == 150) ? 'selected' : ''; ?>>150</option>
							<option value="200" <?php echo ($me['per_page'] == 200) ? 'selected' : ''; ?>>200</option>
							<option value="300" <?php echo ($me['per_page'] == 300) ? 'selected' : ''; ?>>300</option>
							<option value="500" <?php echo ($me['per_page'] == 500) ? 'selected' : ''; ?>>500</option>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label for="inputPassword3" class="col-avatar col-form-label">Theme</label>
					<div class="col">
						<select class="form-control" name="theme">
							<option value="night" <?php echo ($me['theme'] == 1) ? 'selected' : ''; ?>>Dark</option>
							<option value="light" <?php echo ($me['theme'] == 0) ? 'selected' : ''; ?>>Light</option>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col">
						<button type="submit" class="btn btn-outline-dark">Save</button>
					</div>
				</div>
			</form>
			<?php
			}
			
			if(!isset($_GET['settings'])){
			
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
			
			<p class="h3">History of your friend</p>
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
				<div class="col-md-7">
					<?php echo $pagination; ?>
					
				</div>
				<div class="col-md-5 text-right">
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
					$history[] = array('date'=>$row['date'], 'text'=>'&nbsp;<a href="/profiles/'.$row['them'].'/"><img width="32" height="32" class="rounded-circle mx-1" src="https://avatars.steamstatic.com/'.$current_avatar.'.jpg" alt=""/></a>&nbsp;<span class="added"><a href="/profiles/'.$row['them'].'/">'.htmlentities($row['current_name']).'</a>&nbsp;<strong>was added to friend list</strong></span>');
				}
				if($row['type'] == 2)
				{
					$history[] = array('date'=>$row['date'], 'text'=>'&nbsp;<a href="/profiles/'.$row['them'].'/"><img width="32" height="32" class="rounded-circle mx-1" src="https://avatars.steamstatic.com/'.$current_avatar.'.jpg" alt=""/></a>&nbsp;<span class="deleted"><a href="/profiles/'.$row['them'].'/">'.htmlentities($row['current_name']).'</a>&nbsp;<strong>was deleted from friend list</strong></span>');
				}
				if($row['type'] == 3)
				{
					$history[] = array('date'=>$row['date'], 'text'=>'&nbsp;<a href="/profiles/'.$row['them'].'/"><img width="32" height="32" class="rounded-circle mx-1" src="https://avatars.steamstatic.com/'.$previous_avatar.'.jpg" alt=""/></a>&nbsp;<span class="renamed"><a href="/profiles/'.$row['them'].'/">'.htmlentities($row['previous_name']).'</a>&nbsp;<strong>renamed to</strong></span>&nbsp;<a href="/profiles/'.$row['them'].'/"><img width="32" height="32" class="rounded-circle mx-1" src="https://avatars.steamstatic.com/'.$current_avatar.'.jpg" alt=""/></a>&nbsp;<span class="renamed"><a href="/profiles/'.$row['them'].'/">'.htmlentities($row['current_name']).'</a></span>');
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
			
			<?php } ?>
						
			<?php include 'include/footer.php'; ?>
			
		</div>
		<?php }		
		if(!isset($me) && empty($me)){ $navbar = 'dark fixed-top'; $theme = '';?><div class="night">
		<?php
		} 
		?><script src="/js/jquery-3.2.1.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/popper.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/bootstrap.slim.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/main.js?v<?php echo $version; ?>"></script>
	</body>
</html>
