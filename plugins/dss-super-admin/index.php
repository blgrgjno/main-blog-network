<?php
/*

Plugin Name: DSS Network Super Admin
Plugin URI: http://metronet.no/
Description: Provides more functionality for super admins
Author: Ryan Hellyer
Version: 1.0
Author URI: http://metronet.no/

Copyright (c) 2012 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/

require( 'inc/class-dss-network-super-admin.php' );
require( 'inc/class-dss-framework-home-avatars.php' );

$super_admin = new DSS_Network_Super_Admin();
new DSS_Framework_Home_Avatars();
