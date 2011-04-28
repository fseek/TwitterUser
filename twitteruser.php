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

class HTTPDownloadException extends Exception{}

/*! @class TwitterUser
 * @brief This class represents a user on twitter
 *
 * You can get all the values that twitter returns
 * This class is used for getting information of an twitter user,
 * e.g. how much followers he has
 */
class TwitterUser
{
	private static $alias = array('followers' => 'followers_count',
								'screeName' => 'screen_name',
								'profileImage' => 'profile_image_url');
	/*! @brief Twitter Id
	 *
	 * The Twitter Id of this instance
	 */
	public $id;
	
	/*! @brief The constructor of TwitterUser
	 * @param[in] twitterId The Id of the twitter user to use for the instance of this class. Without an @ sign
	 */
	public function __construct($twitterId)
	{
		try 
		{
			$this->id = $twitterId;
			$this->downloadUserInfo();
		} 
		catch (HTTPDownloadException $e) 
		{
			throw new Exception("Could not download user information");
		}  
	}
	
	/*! @brief Downloads and parses a JSON file
	 * @param[in] url The URL of the JSON file
	 * @return An array which content is the downloaded JSON file
	 * 
	 * This method downloads a JSON file from the given URL
	 * and parses it into an associative array.
	 */
	private function downloadJSON($url)
	{
		$data = file_get_contents($url);
		if($data)
		{
			return json_decode($data,true);
		} 
		else
		{
			throw new HTTPDownloadException();
		}
	}

	function __get($prop)
	{
		if(isset($this->$prop)) 
		{
			return $this->$prop;
		} 
	 
		if (array_key_exists($prop,$this->userInfo)) 
		{
			return $this->userInfo[$prop];
		}

		if(array_key_exists($prop,TwitterUser::$alias))
		{
			return $this->userInfo[TwitterUser::$alias[$prop]];
		}

		return null;
	}
  
	/*! @brief Loads user information from Twitter
	 * @return The information of the Twitter user
	 *
	 * Loads the user information from Twitter,
	 * called in the constructor
	 */
	private function downloadUserInfo()
	{
		$this->userInfo = $this->downloadJSON('http://twitter.com/users/show.json?screen_name='.$this->id);	  
	}

	/*! @brief Deprecated
	 *
	 * Does nothing, all vars are loaded in the constructor now
	 */
	public function getAllVars()
	{
	}

	/*! @brief Returns Twitter information as an array
	 * @return An array with the information of this Twitter user
	 *
	 * This method returns the Twitter information of this
	 * class as an array.
	 */
	public function asArray()
	{
		return $this->userInfo;
	}

	/*! @brief Deprecated
	 * @return The returned value of $instance->followers
	 *
	 * Deprecated, use $instance->followers instead
	 */
	public function getFollowers()
	{
		return $this->followers;
	}


	/*! @brief Returns the last status of the Twitter user
	 * @return A string with the last status
	 *
	 * This method loads the last status of this Twitter user
	 * from Twitter and returns it as a string
	 */
	public function getStatus($add_hyperlinks = false)
	{
	  $status = $this->userInfo['status']['text'];
		if ($add_hyperlinks)
		{
			$status = preg_replace('[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]', '<a href="%5C%22%5C%5C0%5C%22"></a>', $status);
		}
		
		return $status;
	}
}

