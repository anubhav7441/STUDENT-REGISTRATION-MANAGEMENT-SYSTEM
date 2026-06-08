<?php
// Deny direct browsing of the uploads directory
http_response_code(403);
exit('403 Forbidden — Direct access denied.');
