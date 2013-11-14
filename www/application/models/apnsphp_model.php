<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Apnsphp_model extends CI_Model {

    protected $apnsDir = '';
    protected $certDir = NULL;
    protected $environment = 'testing';

    // -----------------------------------------------

    /**
     * Setup some basic stuffs
     * @param void
     * @return void
     * @access public 
     */
    public function __construct() {

        parent::__construct();

        /* get all the APNS files */
        $this->apnsDir = APPPATH.'/third_party/ApnsPHP/';
        $this->certDir = APPPATH.'../../certificates/';
        $this->environement = 'testing';
        $this->_apns_req();

        return;

    } /* /__construct() */
    
    public function set_environment($new_environment)
    {
    	if (in_array($new_environment,array('testing','production')))
    	{
	    	$this->environment = $new_environement;
    	}
    }

    // -----------------------------------------------

    /**
     * Will send the actual iOS notification to the user
     * @param $token string iOS device token
     * @param $msg string 
     * @param $attrs array Key/value pairs to be sent as meta with APN
     * @return void
     * @access public
     */
    public function send_ios($token=null, $msg=null, $attrs=array()) {

        if(!$token || !$msg) return;

        switch ($this->environment)
        {
            default:
            case 'testing':
                $env = ApnsPHP_Abstract::ENVIRONMENT_SANDBOX;
                $certPath = "{$this->certDir}apns-development.pem";
                break;
            
            case 'production':
                $env = ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION;
                $certPath = "{$this->certDir}apns-production.pem";
                break;
            
        }
        
        // Instantiate a new ApnsPHP_Push object
        $push = new ApnsPHP_Push($env,$certPath);

        // Set the Provider Certificate passphrase
        // $push->setProviderCertificatePassphrase('tablecan29');

        // Set the Root Certificate Autority to verify the Apple remote peer
        //$push->setRootCertificationAuthority(APPPATH.'/config/SSL/entrust_root_certification_authority.pem');

        // Connect to the Apple Push Notification Service
        $push->connect();

        // Instantiate a new Message with a single recipient
        $message = new ApnsPHP_Message($token);

        // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
        // over a ApnsPHP_Message object retrieved with the getErrors() message.
        $message->setCustomIdentifier("Message-Badge-3");

        // Set badge icon to "3"
        // $message->setBadge(0);

        // Set a simple welcome text
        $message->setText($msg);

        // Play the default sound
        $message->setSound();

        // Set custom properties
        if( is_array($attrs) && count($attrs) )
        {
            foreach( $attrs as $attr_key => $attr_val )
            {
                $message->setCustomProperty($attr_key, $attr_val);
            }
        }

        // Set the expiry value - in seconds
        $message->setExpiry(120);

        // Add the message to the message queue
        $push->add($message);

        // Send all messages in the message queue
        $push->send();

        // Disconnect from the Apple Push Notification Service
        $push->disconnect();

        // Examine the error message container
        // $aErrorQueue = $push->getErrors();
        // if (!empty($aErrorQueue)) {
        //  var_dump($aErrorQueue);
        // }

        return TRUE;

    } /* /send_ios() */

    // -----------------------------------------------

    private function _apns_req() {

        require_once $this->apnsDir.'Abstract.php';
        require_once $this->apnsDir.'Exception.php';
        require_once $this->apnsDir.'Feedback.php';
        require_once $this->apnsDir.'Message.php';
        require_once $this->apnsDir.'Log/Interface.php';
        require_once $this->apnsDir.'Log/Embedded.php';
        require_once $this->apnsDir.'Message/Custom.php';
        require_once $this->apnsDir.'Message/Exception.php';
        require_once $this->apnsDir.'Push.php';
        require_once $this->apnsDir.'Push/Exception.php';
        require_once $this->apnsDir.'Push/Server.php';
        require_once $this->apnsDir.'Push/Server/Exception.php';

        return;

    } /* /_apns_req() */

} /* /Notification_model{} */

/* End of file apnsphp_model.php */
/* Location: ./application/models/apnsphp_model.php */