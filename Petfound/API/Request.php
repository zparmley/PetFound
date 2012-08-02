<?php
namespace Petfound\API;

use Petfound\Exception as PetfoundException;
use Petfound\Pet;

/**
 * Petfound\API\Request Takes a url and requests data from the petfinder api, the parses into a more usable data type
 *
 * @package Petfound\API
 * @author  Zachary Parmley
 **/
class Request {

    /**
     * Signed API request url
     *
     * @var string
     **/
    protected $_url;

    /**
     * JSON decoded results from a pietfinder API call
     *
     * @var mixed
     **/
    protected $_result;

    /**
     * Constructor
     *
     * @param  string|Petfound\URL $url (Optional) A string or object representing an API request call
     * @return void
     **/
    public function __construct($url = null) {
        if (is_null($url) === false) {
            $this->setUrl($url);
        }
    }

    /**
     * Set the internal url to a passed in string or the string version of 
     *
     * @return void
     * @author 
     **/
    public function setUrl($url) {
        if (($url instanceof URL)
          or ((is_string($url) === true) and (substr($url, 0, 7) === 'http://'))){
            $this->_url = (string) $url;
        } else {
            var_dump($url);
            throw new PetfoundException ('PetfinderAPI Result url must be Petfound\API\URL object or a url begining with "http://"');
        }
    }

    /**
     * Load data from the petfinder API based on the URL, and decode them into internal _results property
     *
     * @return void
     * @author 
     **/
    public function load($url = null) {
        if (is_null($url) === false) {
            $this->_url = $url;
        }

        if (is_null($this->_url) === true) {
            throw new PetfoundException('Cannot load with a null url');
        }

        $this->_result = json_decode(file_get_contents($this->_url));
    }

    /**
     * Public accessor for _result property
     *
     * @return mixed
     **/
    public function getResult() {
        return $this->_result;
    }

    /**
     * Method to parse a pet list result (which some api methods will return) into a more usable data structure
     *
     * @return array
     **/
    public function getPets() {

        if (is_null($this->_result) === true) {
            throw new PetfoundException('Cannot parse pets before loading api results;  Use $obj->load()');
        }

        $result = $this->getResult();
        if ((isset($result->petfinder) === false)
          or (isset($result->petfinder->pets) === false)
          or (isset($result->petfinder->pets->pet) === false)) {
            throw new PetfoundException('Result data does not match expected format - does the api call type you specified return a list of pets?');
        }

        $pets = $this->getResult()->petfinder->pets->pet;
        $petObjects = array();
        foreach ($pets as $pet) {
            $petObjects []= new Pet($pet);
        }

        return $petObjects;
    }


}