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
	  $userInfo = json_decode(file_get_contents('http://twitter.com/users/show.json?screen_name='.$this->id),true);
	  $this->followers = $userInfo['followers_count'];
	  $this->name = $userInfo['name'];
	  $this->screenName = $userInfo['screen_name'];
	  $this->description = $userInfo['description'];
	  $this->location = $userInfo['location'];
	  $this->profileImage = $userInfo['profile_image_url'];

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

