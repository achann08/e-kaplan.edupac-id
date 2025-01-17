<?php
/**
 * @author Emanuel Delgado
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Unique login
 *
 * Makes sure that each user can have only one active login session simultaneously.
 * This plugin is suited for versions of Moodle 1.9.x and above. It already addresses modifications
 * in database stored sessions such as table name and new field userid.
 *
 * 2010-05-13  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/authlib.php');

/**
 * Unique login authentication plugin.
 */
class auth_plugin_uniquelogin extends auth_plugin_base {

    protected $sessionstable;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'uniquelogin';
        $this->config = get_config('auth/uniquelogin');
        if (empty($this->config->extencoding)) {
            $this->config->extencoding = 'utf-8';
        }
        $this->init();
    }

    /**
     * Class setup.
     *
     * @return bool Setup sucess
     */
    protected function init() {
        //Set sessions table according to Moodle version.
        if ($this->uniquelogin_is_version_1_9_x()) {
            $this->sessionstable = 'sessions2';
        } else {
            $this->sessionstable = 'sessions';
        }
    }

    /**
     * Return true if Moodle version is 1.9.x.
     *
     * @return bool True if Moodle version is 1.9.x.
     */
    protected function uniquelogin_is_version_1_9_x() {
        global $CFG;
        return substr($CFG->release,0,3) == '1.9';
    }

    /**
     * Return true if Moodle version is 2.x.x.
     *
     * @return bool True if Moodle version is 2.x.x.
     */
    protected function uniquelogin_is_version_2_x_x() {
        global $CFG;
        return substr($CFG->release,0,2) == '2.';
    }
	
    /**
    * Return true if Moodle version is 3.x.x.
    *
    * @return bool True if Moodle version is 3.x.x.
    */
    protected function uniquelogin_is_version_3_x_x() {
        global $CFG;
        return substr($CFG->release,0,2) == '3.';
    }
	
	/**
    * Return true if Moodle version is 4.x.x.
    *
    * @return bool True if Moodle version is 4.x.x.
    */
    protected function uniquelogin_is_version_4_x_x() {
        global $CFG;
        return substr($CFG->release,0,2) == '4.';
    }

	/**
     * Return true if Moodle version is > 2.6.x.
     *
     * @return bool True if Moodle version is 2.6.x.
     */
    protected function uniquelogin_is_big_version_2_6_x() {
        global $CFG;
        return substr($CFG->release,2,1) >= 6;
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password) {
        global $CFG;
        return false; //This plugin never authenticates successfully an user, it always defer to other auth plugin
    }

    /**
     * Method called when a user has a sucessfull login in any authentication plugin.
     *
     * @return bool Always returns true.
     */
    public function user_authenticated_hook(&$user, $username, $password) {
        $this->uniquelogin_logout_user($user->id,$user);
        return true;
    }

    /**
     * Searches for user sessions in database, identifies the ones that belong to user
     * identified by $userid and terminates those sessions.
     *
     * @param $userid
     * @return bool Always returns true.
     */
    protected function uniquelogin_logout_user($userid,$user) {
    	global $USER,$SESSION;
        if ($this->uniquelogin_is_version_1_9_x()) {
            $sessionsindatabase = get_recordset('sessions2');
            $sessions = array();
            while ($row = rs_fetch_next_record($sessionsindatabase)) {
                $sessiondata = adodb_unserialize(urldecode($row->sessdata));
                $sessions[$sessiondata['USER']->id][] = $row->sesskey;
            }
            if (!array_key_exists($userid, $sessions) || is_null($sessions[$userid])) {
                return true;
            }
            
            foreach ($sessions[$userid] as $sessionKey) {
                $this->uniquelogin_end_dbsession_by_sesskey($sessionKey);
            }
        } else if($this->uniquelogin_is_version_2_x_x() || $this->uniquelogin_is_version_3_x_x() || $this->uniquelogin_is_version_4_x_x()){
			global $DB,$CFG;
			//If setting apply admin is ative
			if(isset($CFG->auth_uniquelogin_aplly_to_admin) && $CFG->auth_uniquelogin_aplly_to_admin==0){
				if (has_capability('moodle/site:config',context_system::instance(),$user) ) {
					return true;
				}	
			}
			
			//If setting apply to teacher
       		if(isset($CFG->auth_uniquelogin_aplly_to_teacher) &&  $CFG->auth_uniquelogin_aplly_to_teacher==0){
       			$select = "(roleid = '2' OR roleid = '3' OR roleid = '4' ) AND userid='".$userid."'  ";
				$aTeacher = $DB->get_records_select('role_assignments',$select);
				if (!empty($aTeacher) && count($aTeacher)>0) {
					return true;
				}	
			}
			
			$sessionsindatabase = $DB->get_recordset($this->sessionstable,array('userid'=>$userid));
			
            $sessions = array();
			foreach ($sessionsindatabase as $row) {
				$sessions[$row->userid][] = $row->sid;
			}
			
			
            if (!array_key_exists($userid, $sessions) || is_null($sessions[$userid])) {
                return true;
            }
            
			
            //Is a force password
			if(isset($_POST['newpassword1']) && $_POST['newpassword1']!=''){
				return true;
			}
            
            foreach ($sessions[$userid] as $sessionKey) {
                $this->uniquelogin_end_dbsession_by_sesskey($sessionKey);
            }
		}else {
            session_kill_user($userid);
        }
        return true;
    }

    /**
     * This method ends a database session using the sesskey.
     *
     * @param $sessionKey
     * @return unknown_type
     * @deprecated Moodle 2.0. Please do not call this function any more.
     */
    protected function uniquelogin_end_dbsession_by_sesskey($sessionKey) {
		if ($this->uniquelogin_is_version_1_9_x()) {
			delete_records_select($this->sessionstable, "sesskey='".$sessionKey."'");
		}else{
			global $DB;
			$DB->delete_records_select($this->sessionstable, "sid='".$sessionKey."'");
		}
        
    }
    
	public function has_config() {return true;}

}
?>