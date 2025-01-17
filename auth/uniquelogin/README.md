# uniquelogin
Moodle Uniquelogin authentication

The goal of this authentication plug-in is to make sure that each user in Moodle only has one active session simultaneously.
To work properly, the plugin needs that Database-stored sessions are enabled. Have a look at the module documentation on http://docs.moodle.org/20/en/Uniquelogin_authentication to more information.

This plugin is being maintained by ED-ROM, the portuguese Moodle Partner.

This document describes how to set up Uniquelogin Authentication in Moodle.

This is integrated into Moodle 1.9 onwards.

The Unique Authentication module is available for download from the Moodle plugins directory: http://moodle.org/plugins/view.php?plugin=auth_uniquelogin
Contents


Overview

The goal of Uniquelogin Authentication plugin is to make sure that each user in Moodle can only have one active session simultaneoulsy.

The code is prepared to work on Moodle 1.9 and 2.X as it addresses modifications in session table and database access through global DB instance.
Assumptions

To work properly, the plugin needs the following:

    Database-stored sessions are enabled (see Notes/Tips bellow).
    Uniquelogin is active in authentication plugins page.

Installation

    Download the plug-in file from http://moodle.org/plugins/view.php?plugin=auth_uniquelogin
    Unzip the file to authentication plug-ins folder moodle/auth/
    On the Administration Block, click on Site administration » Users » Authentication » Manage authentication
    Enable the authentication plug-in called Unique login
    Make sure Database-stored sessions are enabled (see Activate database-stored sessions bellow).

Activate database-stored sessions

    As mentioned in Assumptions, you must use database-stored sessions to use this plug-in. To achieve this follow these steps:

    On the Administration Block, click on Site administration » Server » Session Handling
    Enable the setting Use database for session information
	
What new in 1.1

	Administrator can configure whether teacher and administrator roles are affected by the plugin.
