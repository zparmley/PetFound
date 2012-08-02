<?php
namespace Petfound\API;

use Petfound\Exception as PetfoundException;
/**
 * Petfound\API\URL
 *
 * @package Petfound\API
 * @author  Zachary Parmley
 **/
class URL {
    /**
     * Petfinder API key
     *
     * @var string
     **/
    protected $_key;

    /**
     * Petfinder API secret
     *
     * @var string
     **/
    protected $_secret;

    /**
     * Petfinder API request type
     *
     * @var string
     **/
    protected $_type;

    /**
     * Petfinder API request arguments
     *
     * @var string
     **/
    protected $_args;
    
    /**
     * Constructor
     *
     * @param  string $type   API call type
     * @param  mixed  $args   Arg string to append to url before signing, or array of args
     * @param  string $key    (optional) API key for making calls against the Petfinder API
     * @param  string $secret (optional) API secret for making calls to the Petfinder API
     * @return void
     **/
    public function __construct($type, $args, $key = null, $secret = null) {
        if (!is_null($key)) {
            $this->setKey($key);
        }

        if (!is_null($secret)) {
            $this->setSecret($secret);
        }
        
        $this->_type = $type;

        if (is_array($args) === true) {
            $this->_args = implode('&', array_map(function($k, $v) {
                return $k . '=' . urlencode($v);
            }, array_keys($args), $args));
        } elseif (is_string($args) === true) {
            $this->_args = $args;
        } else {
            throw new PetfoundException('URL args must be a string or associative array of args');
        }

    }
    
    /**
     * Assemble and sign API call url
     *
     * @return string
     **/
    protected function _generateUrl() {
        if (isset($this->_key) === false) {
            throw new PetfoundException('API Key must be set for url generation');
        }

        if (isset($this->_secret) === false) {
            throw new PetfoundException('API Secret must be set for url generation');
        }


        $args = 'key=' . $this->_key . '&' . $this->_args;
        $sig  = md5($this->_secret . $args);
        $url  = 'http://api.petfinder.com/' . $this->_type . '?' . $args . '&signature=' . $sig;
        return $url;
    }
    
    /**
     * Public accessor to protected url generate method
     *
     * @return string
     **/
    public function getRequestUrl() {
        return $this->_generateUrl();
    }
    
    /**
     * Magic method stringifys object, returns signed API call url
     *
     * @return string
     **/
    public function __toString()
    {
        return $this->getRequestUrl();
    }

    /**
     * Setter for the api key
     *
     * @param  string $key API key
     * @return void
     **/
    public function setKey($key)
    {
        $this->_key = $key;
    }

    /**
     * Setter for the api secret
     *
     * @param  string $secret API secret
     * @return void
     **/
    public function setSecret($secret)
    {
        $this->_secret = $secret;
    }
}