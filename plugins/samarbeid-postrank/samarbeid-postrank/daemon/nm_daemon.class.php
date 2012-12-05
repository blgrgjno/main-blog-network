<?php
/**
 * An abstract class to instantiate a command-line based daemon, with simple start/stop commands
 * This daemon can then be automatically started via the common SYSV interface
 *
 * @author  Nelson Menezes
 */



if (!defined('STDERR'))
{
    define('STDERR', fopen("php://stderr","r"));
    register_shutdown_function( create_function('' , 'fclose(STDERR); return true;') );
}



abstract class nm_daemon
{
    const temp_dir = '/tmp/';

    /**
     *
     */
    protected  $_lockfile;

    /**
     *
     */
    protected $_unique_name;

    /**
     *
     */
    public $sleep_period = 6000;

    /**
     * The root dir that the daemon will have
     */
    protected $_daemon_chroot;

    /**
     * The username that will run the daemon
     */
    protected $_daemon_username;

    /**
     * The group the daemon will run under
     */
    protected $_daemon_group;

    /**
     *
     */
    public function __construct($unique_name)
    {
        global $argv;

        if (!$unique_name)
        {
            fputs(STDERR, "Internal error (No unique name provided)\n");
            exit(1);
        }

        // -------- give up privileges, if set ---------------------------

        if ($this->_daemon_chroot)
        {
            chroot($this->_daemon_chroot);
        }

        if ($this->_daemon_username)
        {
            $user_details = posix_getpwnam($this->_daemon_username);

            posix_setuid($user_details['uid']);
        }

        if ($this->_daemon_group)
        {
            $group_details = posix_getgrnam($this->_daemon_group);

            posix_setgid($group_details['gid']);
        }

        // ---------------------------------------------------------------

        $this->_unique_name = '.' . $unique_name;

        // Check command line parameter

        switch (@$argv[1])
        {
            case 'stop' :

                if ($this->stop())
                {
                    echo "Stopped.\n";
                    exit;
                }
                else
                {
                    exit(1);
                }

            case 'restart' :

                if ($this->stop(true))
                {
                    if ($this->start())
                    {
                        echo "Restarted.\n";
                    }
                }

                exit;

            case 'start' :

               if ($this->start())
               {
                  echo "Started.\n";
               }

               exit;

            case 'status' :

               if ($this->check_already_running())
               {
                   echo "Running (PID " . file_get_contents(self::temp_dir . $this->_unique_name . '.pid') . ")\n";
               }
               else
               {
                   echo "Not running.\n";
               }

               exit;

            default:

               echo "Usage: {$argv[0]} {start|stop|restart|status}\n";
               exit;
        }
    }


    /**
     * Stops the daemon, if possible
     */
    public function stop($be_quiet = false)
    {
        if (!$this->check_already_running())
        {
            if ($be_quiet)
            {
                return true;
            }

            fputs(STDERR, "Not running.\n");
            return false;
        }

        // Stop daemon...

        if ((!$pid = file_get_contents(self::temp_dir . $this->_unique_name . '.pid')) || (!is_numeric($pid)))
        {
            fputs(STDERR, "Cannot read child PID from file! You should use 'ps' to locate it and kill it.\n");
            return false;
        }

        if (!posix_kill($pid, SIGUSR1))
        {
            fputs(STDERR, "Cannot stop process!\n");
            return false;
        }

        unlink(self::temp_dir . $this->_unique_name . '.pid');
        unlink(self::temp_dir . $this->_unique_name . '.lock');

        syslog(LOG_WARNING, __FILE__ . ": Service stopped.\n");

        return true;
    }


    /**
     * Checks whether the daemon is already running
     */
    public function check_already_running()
    {
        sleep(1);  // Sleep for a second to allow any dieing processes to exit

        $lock_filename = self::temp_dir . $this->_unique_name . '.lock';

        if (!file_exists($lock_filename))
        {
            return false;
        }

        if (!$lockfile = fopen($lock_filename, 'a'))
        {
             // This shouldn't happen!
             fputs(STDERR, "Cannot access lock file " . self::temp_dir . $this->_unique_name . ".lock\n");
             exit(1);
        }

        if (flock($lockfile, LOCK_EX + LOCK_NB, $would_block) && !$would_block)
        {
            return false;
        }

        if ($lockfile && $would_block)
        {
            return true;
        }

        return false;
    }


    /**
     * Starts the daemon, if possible
     */
    public function start()
    {

        if ($this->check_already_running())
        {
            fputs(STDERR, "Already running! (Remove " . self::temp_dir . $this->_unique_name . ".lock if process died)\n");
            return false;
        }

        // Detach process to background and store PID in pid file

        if (pcntl_fork())
        {
            return true;
        }

        // from here down will only be executed in the child process

        posix_setsid();

        if ($pid = pcntl_fork())
        {
            if (!$pid_file = fopen(self::temp_dir . $this->_unique_name . '.pid', "w"))
            {
                fputs(STDERR, "Cannot write child PID to file!\n");
                exit(1);
            }

            fputs($pid_file, $pid);
            fclose($pid_file);
            exit;
        }

        // Daemonised!

        $this->_lockfile = fopen(self::temp_dir . $this->_unique_name . '.lock', 'w');

        if (!$this->_lockfile || !flock($this->_lockfile, LOCK_EX + LOCK_NB))
        {
            fputs(STDERR, "Cannot create lock file!\n");
            exit(1);
        }

        // TODO: Attach signal-handling processes; if needed

        syslog(LOG_WARNING, __FILE__ . ": Service started.\n");

        do
        {
            $this->do_work();
        }
        while ($this->keep_working());

        //$this->stop();
    }


    /**
     * Override this if you need a different approach to cycling the do_work method
     */
    public function keep_working()
    {
        sleep($this->sleep_period);

        return true;
    }


    /**
     *
     */
    abstract public function do_work();
}

?>