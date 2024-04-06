<?php
set_time_limit(0);
require realpath(__DIR__ . '/include/config.php');

error_reporting(-1);
ini_set('display_errors', 0);

$me = array();

require realpath(__DIR__ . '/include/steamauth.php');
require realpath(__DIR__ . '/include/paginaton.php');

if(empty($me))
{
	header('Location: /');
	exit;
}

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
	<body class="<?php echo ($me['theme'] == 1 ? 'night' : ''); ?>">	
		<?php include 'include/navbar.php'; ?>

		<div class="container py-4">
			<?php include 'include/user.php'; ?>
			
			<div id="ajax" class="my-3"><div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div></div></div>
			
			<?php 
			$country = array();
			$from = array();
			$friends = json_decode($me['friends'], true);

			foreach($friends as $friend)
			{
				$query = $db->query('SELECT loccountrycode, avatar, personaname FROM friend WHERE loccountrycode <> "" AND steamid = "'.$friend.'"');
					
				if($query->num_rows)
				{
					$row = $query->fetch_row();
					$country[] = $row[0];
					$from[] = array($row[2],$row[0], '<p class="my-2"><img width="32" height="32" class="img-thumbnail mx-1" src="https://avatars.steamstatic.com/'.$row[1].'.jpg" alt="">&nbsp;<a href="/profiles/'.$friend.'/">'.htmlentities($row[2]).'</a></p>');
				}
			}
			
			asort($from);
		
			$json = json_decode(file_get_contents('include/steam_countries.json', true));
			
			if(isset($_GET['country']) && !empty($_GET['country']))
			{
				$countrycode = $_GET['country'];
				if(isset($json->$countrycode->name))
				{
					echo '<p class="h3 my-4">Friends from '.$json->$countrycode->name.'</p>';
				}

				echo '<div class="row">';
				$count = 1;
				foreach($from as $friend)
				{
					if($friend[1] == $_GET['country'])
					{
						if($count%5 == 1)
						{  
							echo '<div class="col col-md-4 my-2">';
						}
						
						echo $friend[2];
						
						if($count%5 == 0)
						{
							echo '</div>';
						}
						
						$count++;
					}
					
				}
				if ($count%5 != 1) echo "</div>";
				echo '</div>';
			}
			?>
			
			<p class="h3 my-4">Friends by country</p>

			<div class="row">
			
			<?php
			asort($country);
			$array = array_count_values($country);
			$gdpData = array();
								
			$count = 1;
			foreach($array as $key => $value)
			{
				if($count%5 == 1)
				{  
					echo '<div class="col col-md-4 my-4">';
				}
				
				echo '<p><a href="/country/'.$key.'/">'. $json->$key->name .'</a> ('.$value.')</p>';
				
				if($count%5 == 0)
				{
					echo '</div>';
				}
			
				$count++;
			}
			if ($count%5 != 1) echo "</div>";
			?>
			
			</div>		
			
			<?php include 'include/footer.php'; ?>
			
		</div>	
		<script src="/js/jquery-3.2.1.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/popper.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/bootstrap.slim.min.js?v<?php echo $version; ?>"></script>
		<script src="/js/main.js?v<?php echo $version; ?>"></script>		
	</body>
</html>
