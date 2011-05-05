from urllib2 import urlopen
import json
"""
This file is part of TwitterUser.

    TwitterUser is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    TwitterUser is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with TwitterUser.  If not, see <http://www.gnu.org/licenses/>.
"""
class TwitterUser:
    _URL = "http://twitter.com/users/show.json?screen_name={}"
    def __init__(self,username):
        response = urlopen(self._URL.format(username))
        self.data = json.load(response)

    def __getattr__(self,attr):
        if attr in self.data:
            return self.data[attr]
        raise AttributeError("{} object has no attribute {}".format(type(self).__name__,attr))
