<?php
require("twitteruser.php");

$user = new TwitterUser("bbc");
$user->getAllVars();

$info = array();
$info['profile_image'] = $user->profileImage;
$info['name'] = $user->name;
$info['screen_name'] = $user->screenName;
$info['location'] = $user->location;
$info['followers'] = $user->followers;
$info['description'] = $user->description;

print_r($info);