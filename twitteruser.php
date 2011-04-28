<?php

class HTTPDownloadException extends Exception{}

class TwitterUser
{
	public $id, $followers, $name, $screenName, $location,
				$description, $profileImage;
	
	public function __construct($twitterId)
	{
	  try {
	    $this->id = $twitterId;
	    $this->downloadUserInfo();
	  } catch (HTTPDownloadException $e) {
	    throw new Exception("Could not download user information");
	  }
	  
	}
	
	private function downloadJSON($url)
	{
	  $data = file_get_contents($url);
	  if($data)
	    {
	      return json_decode($data,true);
	    } else
	    {
	      throw new HTTPDownloadException();
	    }
	}

	private function downloadUserInfo()
	{
	  $userInfo = $this->downloadJSON('http://twitter.com/users/show.json?screen_name='.$this->id);
	  $this->followers = $userInfo['followers_count'];
	  $this->name = $userInfo['name'];
	  $this->screenName = $userInfo['screen_name'];
	  $this->description = $userInfo['description'];
	  $this->location = $userInfo['location'];
	  $this->profileImage = $userInfo['profile_image_url'];
	  
	}

	// deprecated: no longer does anything. downloading of vars is done in the constructor
	public function getAllVars()
	{
	}

	// deprecated use $instance->followers instead.
	public function getFollowers()
	{
	  return $this->followers;
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

