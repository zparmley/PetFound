<?php
namespace Petfound;

/**
 * Petfound\Pet represents a pet returned from the API parsed into a more useable format
 *
 * @package Petfound
 * @author  Zachary Parmley
 **/
class Pet {

	/**
	 * Data fields to pull directly from output if possible
	 *
	 * @var array
	 **/
	protected $_dataFields = array('age', 'animal', 'description', 'id', 'lastUpdate', 'mix', 'name', 'sex', 'shelterId', 'shelterPetId', 'size', 'status');

	/**
	 * Placeholder for parsed pet data
	 *
	 * @var string
	 **/
	protected $_data = array();

	/**
	 * Constructor
	 *
	 * @param  array $petData (Optional) Raw rata decoded from petfinder api JSON response
	 * @return void
	 **/
	public function __construct($petData = null) {
		if (is_null($petData) === false) {
			$this->_parseData($petData);
		}
	}

	
	/**
	 * Parse the data from petfinder into a more user friendly format
	 *
	 * @param  array $petData Raw rata decoded from petfinder api JSON response
	 * @return void
	 **/
	protected function _parseData($data) {
		foreach ($this->_dataFields as $field) {
			if (isset($data->$field)) {
				if (isset($data->$field->{'$t'})) {
					$this->_data[$field] = $data->$field->{'$t'};
				}
			}
		}

		// Breeds and options are formated as arrays of objects, so we map them here
		$this->_data['breeds'] = array();
		if (is_array($data->breeds->breed)) {
			foreach ($data->breeds->breed as $breed) {
				$this->_data['breeds'][] = $breed->{'$t'};
			}
		} else {
			$this->_data['breeds'][] = $data->breeds->breed->{'$t'};
		}

		$this->_data['options'] = array();
		if (is_array($data->options->option)) {
			foreach ($data->options->option as $option) {
				$this->_data['options'][] = $option->{'$t'};
			}
		} else {
			$this->_data['options'][] = $data->options->option->{'$t'};
		}

		// Contact data is sent as an associative array of key => value object
		$this->_data['contact'] = array();
		foreach ($data->contact as $key => $val) {
			$this->_data['contact'][$key] = (!empty($val)) ? $val->{'$t'} : null;
		}

		// Reorganize photos by image id and size
		$petPhotos = array();
		foreach ($data->media->photos->photo as $photo) {
			$pId = $photo->{'@id'};
			$pUrl = $photo->{'$t'};
			$pSize = $photo->{'@size'};
			if (!isset($petPhotos[$pId])) {
				$petPhotos[$pId] = array();
			}

			$petPhotos[$pId][$pSize] = $pUrl;
		}
		$this->_data['photos'] = $petPhotos;
	}

	/**
	 * Getter method for top level parsed data item
	 *
	 * @return array
	 **/
	public function getData() {
		return $this->_data;
	}

	/**
	 * Magic getter for fields of the parsed petfinder data
	 *
	 * @param  string $key The key of the item to return from the parsed data
	 * @return mixed
	 **/
	public function __get($k) {
		if (isset($this->_data[$k])) {
			return $this->_data[$k];
		}

		return null;
	}
}