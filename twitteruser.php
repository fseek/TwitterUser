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
 * e.g. '$myTwitterUser->followers_count'. <BR><BR>
 *
 * (Boolean) "profile_use_background_image",<BR>
 * (String) "location",<BR>
 * (Boolean) "show_all_inline_media",<BR>
 * (Boolean) "follow_request_sent",<BR>
 * (String) "lang",<BR>
 * (Boolean) "geo_enabled",<BR>
 * (String, hexadecimal color) "profile_background_color",<BR>
 * (String) "description",<BR>
 * (Object) "status"<BR>
 * {<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(String) "text",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(Boolean) "truncated",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(?) "place",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(?) "coordinates",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(Boolean) "favorited",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(String) "id_str",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(Integer) "retweet_count",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(String) "source",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(String) "created_at",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(?) "geo",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(String) "in_reply_to_screen_name",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(String) "in_reply_to_status_id_str",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(Array) "contributors",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(Boolean) "retweeted",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(Integer) "in_reply_to_status_id",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(String) "in_reply_to_user_id_str",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(Integer) "in_reply_to_user_id",<BR>
 * 	&nbsp;&nbsp;&nbsp;&nbsp;(Integer, somehow) "id" <BR>
 * },<BR>
 * (String) "profile_background_image_url",<BR>
 * (String) "url",<BR>
 * (Boolean) "verified",<BR>
 * (String) "id_str",<BR>
 * (Boolean) "is_translator",<BR>
 * (Boolean) "default_profile",<BR>
 * (Integer) "statues_count",<BR>
 * (String) "created_at",<BR>
 * (String, hexadecimal color) "profile_text_color",<BR>
 * (Integer) "listed_count",<BR>
 * (Boolean) "protected",<BR>
 * (Boolean) "notifications",<BR>
 * (String) "time_zone",<BR>
 * (Integer) "friends_count",<BR>
 * (String, hexadecimal color) "profile_link_color",<BR>
 * (String) "profile_image_url",<BR>
 * (String) "name",<BR>
 * (Boolean) "default_profile_image",<BR>
 * (String, hexadecimal color) "profile_sidebar_border_color",<BR>
 * (Integer) "followers_count",<BR>
 * (Integer) "id",<BR>
 * (Boolean) "contributors_enabled",<BR>
 * (Integer) "utc_offset",<BR>
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
	 * @param[in] $twitterId The username of the user for the represented by this instance. no @-sign
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
	 * @param[in] $url The URL of the JSON file
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

	/*! @brief Returns the latest status of the Twitter user
	 * @param[in] $add_hyperlinks Whether getStatus() should turn links into clickable hyperlinks
	 * @return A string with the latest status
	 *
	 * This method loads the latest status of this Twitter user
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

