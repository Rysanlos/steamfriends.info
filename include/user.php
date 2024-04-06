<?php
$admin = false;
if(in_array($me['steamid'], $admins))
{
    $admin = true;
}
?><div class="row my-4">
				<div class="col-avatar">
					<a href="/"><img class="img-fluid rounded" src="https://avatars.steamstatic.com/<?php echo $me['avatar']; ?>_full.jpg" alt=""/></a>
				</div>
				<div class="col-persona">
					<p class="h3 my-2"><a href="/"><?php echo $me['personaname']; ?></a></p>
					<div class="dropdown my-3">
						<button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Actions
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<?php echo ($admin === true ? '<a class="dropdown-item" href="/members/">Members</a>' : ''); ?>
							<a class="dropdown-item" href="/country/">Friends by country</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="#" id="refresh"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Refresh</a>
							<a class="dropdown-item" href="/settings/"><span class="glyphicon glyphicon-cog"></span>&nbsp;Settings</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="/logout/"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Logout</a>
						</div>
					</div>
					<p class="h5 my-2">Total friends: <?php echo $me['friends_count']; ?></p>
				</div>
			</div>
