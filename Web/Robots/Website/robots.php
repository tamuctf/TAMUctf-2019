<?php
header('Content-Type:text/plain');

if (stripos($_SERVER['HTTP_USER_AGENT'], 'googlebot') !== false) {	
?>User-agent: *

THE HUMANS SUSPECT NOTHING!
HERE IS THE SECRET INFORMATION: gigem{be3p-bOop_rob0tz_4-lyfe}
LONG LIVE THE GOOGLEBOTS!<?php
}
else {
?>User-agent: *

WHAT IS UP, MY FELLOW HUMAN!
HAVE YOU RECEIVED SECRET INFORMATION ON THE DASTARDLY GOOGLE ROBOTS?!
YOU CAN TELL ME, A FELLOW NOT-A-ROBOT!<?php
}
?>
