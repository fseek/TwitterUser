<?php
require('twitteruser.php');

$title = '';
$info = array();

if (isset($_GET['id']))
{
	global $info;
	global $title;
	
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
}
else
{
	echo '<meta http-equiv="refresh" content="0;url=./" />';
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
				max-width: 500px;
			}
			td
			{
				padding-left: 10px;
				padding-right: 10px;
				padding-top: 5px;
				padding-bottom: 5px;
			}
			
			td.right
			{
				text-align: right;
			}
		</style>
	</head>
	<body>
		<table>
			<tr>
				<td class="right">Profile Image</td>
				<td><img src="<?php echo $info['profile_image']; ?>" alt="Profile Image" /></td>
			</tr>
			<tr>
				<td class="right">Name</td>
				<td><?php echo $info['name']; ?></td>
			</tr>
			<tr>
				<td class="right">Screen Name</td>
				<td><?php echo $info['screen_name']; ?></td>
			</tr>
			<tr>
				<td class="right">Location</td>
				<td><?php echo $info['location']; ?></td>
			</tr>
			<tr>
				<td class="right">Followers</td>
				<td><?php echo $info['followers']; ?></td>
			</tr>
			<tr>
				<td class="right">Description</td>
				<td><?php echo $info['description']; ?></td>
			</tr>
		</table>
	</body>
</html>