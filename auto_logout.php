<?php
session_start();
session_destroy();
http_response_code(204);
exit;
