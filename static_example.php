<?php
require("twitteruser.php");

$user = new TwitterUser("bbc");


print($user->screen_name);

print_r($user->asArray());