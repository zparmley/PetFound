# Petfound
## Wrapper for the Petfinder Developer API

### Usage
The included 'example.php' script invokes the classes and displayes a list of dogs from a shelter on petfinder.

You'll have to configure the SHELTER ID, API KEY and API SECRET for the script to work.  Just search for those words in the file.

### Implemented API calls
Currently the library is tested only against shelter.getPets - there is no reason I see that other calls wouldn't work, but if the return doesn't match that of shelter.getPets parsing into pet objects won't work.  Url construction and signing still should work fine.

### Requirements
The library requires at least PHP 5.3, tested on 5.3.13