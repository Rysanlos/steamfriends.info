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

if(isset($_POST['search']) && !empty($_POST['search']))
{			
	header('Location: /members/search/'.rawurlencode($_POST['search']).'/');
	exit;
}

if(isset($_POST['donation_amount']))
{
	if(isset($_POST['steamid']) && !empty($_POST['steamid']))
	{
		$donation_amount = (str_replace(',','.',$_POST['donation_amount']) * 100);
		$db->query('UPDATE user SET donation_amount = "'.$donation_amount.'" WHERE steamid = "'.$_POST['steamid'].'"');
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
		<?php include 'include/navbar.php'; ?>

		<div class="container py-4">
			<?php 
			include 'include/user.php';
			?>
			
			<p class="h3">Active Members</p>
			<?php
			$search = '';
			$url = '/members/';
			
			if(isset($_GET['search']) && !empty($_GET['search']))
			{
				$term = $db->real_escape_string(str_replace(' ','%',rawurldecode($_GET['search'])));
				
				$search = 'WHERE (steamid LIKE "%'.$term.'%" OR personaname LIKE "%'.$term.'%") ';
				
				$url = '/members/search/'.$_GET['search'].'/';
			}
			
			$page = (int)(!isset($_GET['page']) ? 1 : $_GET['page']);
			if ($page <= 0) $page = 1;
			 
			$per_page = $me['per_page'];
			$startpoint = ($page * $per_page) - $per_page;
			$statement = 'user '.$search;
	   
			$pagination = pagination($statement,$per_page,$page,$url);
			
			?>
			
			<div class="row my-4">
				<div class="col-md-8">
					<?php echo $pagination; ?>
					
				</div>
				<div class="col-md-4 text-right">
					<form method="POST" action="">
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
							</span>
							<input class="form-control search" type="text" name="search" placeholder="Search" aria-label="Search" value="<?php echo (isset($_GET['search']) ? rawurldecode($_GET['search']) : ''); ?>">
						</div>
					</form>
				</div>
			</div>
			<?php
	
			$query = $db->query('SELECT * FROM user '.$search.' ORDER BY lastlogin DESC, membersince ASC LIMIT '.$startpoint.','.$per_page);
			while($row = $query->fetch_assoc())
			{
				echo '<div class="row my-3">
					<div class="col-12">
						<div class="float-left mr-4">
							<a href="/user/'.$row['steamid'].'/">
								<img width="45" height="45" class="img-responsive rounded" src="https://avatars.steamstatic.com/'.$row['avatar'].'_medium.jpg" alt="">
							</a>
						</div>
						<div class="float-left">
							<div><a href="/user/'.$row['steamid'].'/">'.htmlentities($row['personaname']).'</a></div>
							<small>Member since: <span class="text-muted">'.$row['membersince'].'</span></small>&nbsp;
							<small>Last login: <span class="text-muted">'.$row['lastlogin'].'</span></small>&nbsp;
							<small>Total friends: <span class="text-muted">'.$row['friends_count'].'</span></small>
						</div>
					</div>
				</div>
				<hr>';
			}
			
			echo '<div class="my-4">'.$pagination.'</div>';
			?>
			
			<?php
			include 'include/footer.php';
			?>
			
		</div>
		<script src="/js/jquery-3.2.1.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/popper.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/bootstrap.slim.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/main.js?v<?php echo $version; ?>"></script>
	</body>
</html>
