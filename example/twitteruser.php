<?php

class TwitterUser
{
	public $id, $followers, $name, $screenName, $location,
				$description, $profileImage;
	
	public function __construct($twitterId)
	{
		if ($this->idExists($twitterId))
		{
			$this->id = $twitterId;
		}
		else
		{
			throw new Exception("Sorry, but it seems like this id doesn't exist.");
		}
	}
	
	private function idExists($twitterId)
	{
		if (@file_get_contents('http://twitter.com/users/show.xml?screen_name='.$twitterId))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getAllVars()
	{
		$userInfo = file_get_contents('http://twitter.com/users/show.xml?screen_name='.$this->id);
		if (preg_match('/followers_count>(.*)</', $userInfo, $match))
		{
			$this->followers = $match[1];
		}
		if (preg_match('/name>(.*)</', $userInfo, $match))
		{
			$this->name = $match[1];
		}
		if (preg_match('/screen_name>(.*)</', $userInfo, $match))
		{
			$this->screenName = $match[1];
		}
		if (preg_match('/description>(.*)</', $userInfo, $match))
		{
			$this->description = $match[1];
		}
		if (preg_match('/location>(.*)</', $userInfo, $match))
		{
			$this->location = $match[1];
		}
		if (preg_match('/profile_image_url>(.*)</', $userInfo, $match))
		{
			$this->profileImage = $match[1];
		}
	}
	
	public function getFollowers()
	{
		$xml = file_get_contents('http://twitter.com/users/show.xml?screen_name='.$this->id);
		
		if (preg_match('/followers_count>(.*)</', $xml, $match))
		{
			$followers['count'] = $match[1];
		}
		
		return $followers['count'];
	}
	
	public function getStatus($hyperlinks = false)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://twitter.com/statuses/user_timeline/'.$this->id.'.xml?count=1');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$source = curl_exec($curl);
		curl_close($curl);
		preg_match('/<text>(.*)<\/text>/', $source, $match);
		$status = utf8_decode($match[1]);
		
		if ($hyperlinks)
		{
			$status = preg_replace('[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]', '<a href="%5C%22%5C%5C0%5C%22"></a>', $status);
		}
		
		return $status;
	}
}

?>