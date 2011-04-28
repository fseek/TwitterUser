<?php
/*
 *   fseek.org - TwitterUser PHP class
 *
 *   Copyright (C) 2011 Niklas Korz, William Högman
 *
 *   This file is part of TwitterUser.
 *
 *   TwitterUser is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   TwitterUser is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with TwitterUser.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require('../twitteruser.php');

$title = '';
$info = array();
$shouldDisplayInfo;

if (isset($_GET['id']))
{
	global $info;
	global $title;
	global $shouldDisplayInfo;
	
	try
	{
		$user = new TwitterUser($_GET['id']);
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
		exit;
	}
	
	$user->getAllVars();
	$title = 'Info for "'.$user->id.'"';
	$info['profile_image'] = $user->profileImage;
	$info['name'] = $user->name;
	$info['screen_name'] = $user->screenName;
	$info['location'] = $user->location;
	$info['followers'] = $user->followers;
	$info['description'] = $user->description;
	
	$shouldDisplayInfo = true;
}
else
{
	global $title;
	global $shouldDisplayInfo;
	$title = 'Example';
	$shouldDisplayInfo = false;
}

?>

<!DOCTYPE html>
<html lang="">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $title ?></title>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="robots" content="" />
		<style>
			table
			{
				margin: auto;
				margin-top: 100px;
			}
			td
			{
				padding-left: 10px;
				padding-right: 10px;
				padding-top: 5px;
				padding-bottom: 5px;
			}
		</style>
	</head>
	<body>
		<?php if ($shouldDisplayInfo) { ?>
		<table>
			<tr>
				<td style="text-align: right;">Profile Image</td>
				<td><img src="<?php echo $info['profile_image']; ?>" alt="Profile Image" /></td>
			</tr>
			<tr>
				<td style="text-align: right;">Name</td>
				<td style="max-width: 300px;"><?php echo $info['name']; ?></td>
			</tr>
			<tr>
				<td style="text-align: right;">Screen Name</td>
				<td style="max-width: 300px;"><?php echo $info['screen_name']; ?></td>
			</tr>
			<tr>
				<td style="text-align: right;">Location</td>
				<td style="max-width: 300px;"><?php echo $info['location']; ?></td>
			</tr>
			<tr>
				<td style="text-align: right;">Followers</td>
				<td style="max-width: 300px;"><?php echo $info['followers']; ?></td>
			</tr>
			<tr>
				<td style="text-align: right;">Description</td>
				<td style="max-width: 300px;"><?php echo $info['description']; ?></td>
			</tr>
		</table>
		<?php } else { ?>
		<form action="twitter.php" method="get" target="_self">
			<p>
				Twitter ID: <input type="text" name="id" required /><br />
				<input type="submit" value="Submit" />
			</p>
		</form>
		<?php } ?>
	</body>
</html>
