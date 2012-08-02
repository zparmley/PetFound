<?php
use Petfound\Exception as PetfoundException;
use Petfound\API\URL;
use Petfound\API\Request;

echo <<<MOTD
=======================
EXAMPLE SCRIPT INVOKING "PETFOUND" PETFINDER API LIBRARY
BY: Zachary Parmley

This script just invokes the associated API classes 
and uses them to display a list of dogs from a shelter
on petfinder.

You'll have to configure the SHELTER ID, API KEY and API SECRET.

The library requires at least PHP 5.3, tested on 5.3.13


=======================

MOTD;

spl_autoload_register(function($class) {
	require(implode('/', array_slice(explode('\\', $class), 1)) . '.php');
});

try {
	echo "\nSearching petfinder for dogs...";
	$requestType = 'shelter.getPets';
	$requestArgs = array(
		'id' => 'SHELTER ID',
		'status' => 'A',
		'count' => 10,
		'format' => 'json');
	$key         = 'API KEY';
	$secret      = 'API SECRET';
	$url         = new URL($requestType, $requestArgs, $key, $secret);
	$result      = new Request();
	$result->setUrl($url);
	$result->load();

	echo "\nAnd done.  Lets see who we found.\n----------------\n";
	array_walk($result->getPets(), function($pet) {
		$stringData = array($pet->name,
							$pet->mix === 'no' ? '' : 'mixed ',
							$pet->sex === 'M' ? 'male' : 'female');
		vprintf("%s is a %s%s.\n", $stringData);
	});

	echo "\n\n";
} catch (PetfoundException $ex) {
	echo 'There seems to have been a problem with the way this example was set up.  That, or the Petfinder '
	   . "API is down. Darn.\n\nError message:\n\t"
	   . $ex->getMessage();
} catch (\Exception $ex) {
	echo 'There was a PHP Exception thrown during execution of the example. It was intended to be run on '
	   . 'PHP 5.3 and should work with just the default modules.  For whatever reason, it did not work this '
	   . "time...\n\nError message:\n\t"
	   . $ex->getMessage()
	   . "\n";
}