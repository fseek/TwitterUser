<?php

class HTTPDownloadException extends Exception{}

class TwitterUser
{
  private static $alias = array('followers' => 'followers_count',
			  'screeName' => 'screen_name',
			  'profileImage' => 'profile_image_url');
  public $id;
	
	public function __construct($twitterId)
	{
	  try {
	    $this->id = $twitterId;
	    $this->downloadUserInfo();
	  } catch (HTTPDownloadException $e) {
	    throw new Exception("Could not download user information");
	  }
	  print_r(TwitterUser::$alias);
	  
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

       function __get($prop)
       {
	 if(isset($this->$prop)) {
	   return $this->$prop;
	 } 
	 
	 if (array_key_exists($prop,$this->userInfo)) {
	   return $this->userInfo[$prop];
	 }

	 if(array_key_exists($prop,TwitterUser::$alias))
	   {
	     return $this->userInfo[TwitterUser::$alias[$prop]];
	   }

	 return null;
       }
  

	private function downloadUserInfo()
	{
	  $this->userInfo = $this->downloadJSON('http://twitter.com/users/show.json?screen_name='.$this->id);	  
	}

	// deprecated: no longer does anything. downloading of vars is done in the constructor
	public function getAllVars()
	{
	}

	public function asArray()
	{
	  return $this->userInfo;
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

