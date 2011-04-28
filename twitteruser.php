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

/*! \mainpage
 * \section intro_sec Introduction
 * TwitterUser is a simple PHP class for accessing information on twitter users.
 * It supports all the returned data from twitter. The project has a minimalistic
 * approach and we don't include features that you don't *really* need. The library
 * is stand-alone and has no dependencies other than PHP json support. The library
 * does not cache request because  caching belongs in the view layer rather than in
 * the data layer.
 *
 * \section twitter_properties Twitter JSON Properties
 * You can easily get the values for this properties
 * via '$yourTwitterUserInstance->JsonPropertyName', 
 * e.g. '$myTwitterUser->followers_count'.
 *
 * (Boolean) "profile_use_background_image",
 * (String) "location",
 * (Boolean) "show_all_inline_media",
 * (Boolean) "follow_request_sent",
 * (String) "lang",
 * (Boolean) "geo_enabled",
 * (String, hexadecimal color) "profile_background_color",
 * (String) "description",
 * (Object) "status"
 * {
 * 		(String) "text",
 * 		(Boolean) "truncated",
 * 		(?) "place",
 * 		(?) "coordinates",
 * 		(Boolean) "favorited",
 * 		(String) "id_str",
 * 		(Integer) "retweet_count",
 * 		(String) "source",
 * 		(String) "created_at",
 * 		(?) "geo",
 * 		(String) "in_reply_to_screen_name",
 * 		(String) "in_reply_to_status_id_str",
 * 		(Array) "contributors",
 * 		(Boolean) "retweeted",
 * 		(Integer) "in_reply_to_status_id",
 * 		(String) "in_reply_to_user_id_str",
 * 		(Integer) "in_reply_to_user_id",
 * 		(Integer, somehow) "id" 
 * },
 * (String) "profile_background_image_url",
 * (String) "url",
 * (Boolean) "verified",
 * (String) "id_str",
 * (Boolean) "is_translator",
 * (Boolean) "default_profile",
 * (Integer) "statues_count",
 * (String) "created_at",
 * (String, hexadecimal color) "profile_text_color",
 * (Integer) "listed_count",
 * (Boolean) "protected",
 * (Boolean) "notifications",
 * (String) "time_zone",
 * (Integer) "friends_count",
 * (String, hexadecimal color) "profile_link_color",
 * (String) "profile_image_url",
 * (String) "name",
 * (Boolean) "default_profile_image",
 * (String, hexadecimal color) "profile_sidebar_border_color",
 * (Integer) "followers_count",
 * (Integer) "id",
 * (Boolean) "contributors_enabled",
 * (Integer) "utc_offset",
 * (String) "screen_name"
*/


class HTTPDownloadException extends Exception{}

/*! @class TwitterUser
 * @brief This class represents a user on twitter
 *
 * You can get all the values that twitter returns
 * This class is used for getting information of an twitter user,
 * e.g. how many followers he has.
 *
 */
class TwitterUser
{
	private static $alias = array('followers' => 'followers_count',
								'screenName' => 'screen_name',
								'profileImage' => 'profile_image_url');
	/*! @brief username on twitter
	 *
	 * The user's username on twitter without an @-sign
	 */
	protected $username;
	
	/*! @brief The constructor of TwitterUser
	 * @param[in] twitterId The username of the user for the represented by this instance. no @-sign
	 */
	public function __construct($twitterId)
	{
		try 
		{
			$this->username = $twitterId;
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
	 
		if (array_key_exists($prop, $this->userInfo)) 
		{
			return $this->userInfo[$prop];
		}

		if(array_key_exists($prop, TwitterUser::$alias))
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
		$this->userInfo = $this->downloadJSON('http://twitter.com/users/show.json?screen_name='.$this->username);	  
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

	/*! @brief Returns the last status of the Twitter user
	 * @return A string with the last status
	 *
	 * This method loads the last status of this Twitter user
	 * from Twitter and returns it as a string
	 */
	public function getStatus($add_hyperlinks = true)
	{
		$status = $this->userInfo['status']['text'];
		if ($add_hyperlinks)
		{
			$status = preg_replace("#(^|[^\"=]{1})(http://|ftp://|mailto:|https://)([^\s<>]+)([\s\n<>]|$)#sm", "\\1<a target=\"_blank\" href=\"\\2\\3\">\\3</a>\\4", $status);
		}		
		return $status;
	}
}

