<?php $routes = ["/" => "front.home", "/login" => "login", "/register" => "register", "/about" => "about", "/contact" => "contact", "/jobs" => "jobs.index", "/companies" => "companies.index"]; echo "ROUTE TESTING REPORT
===================
"; foreach ($routes as $path => $name) { $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, "http://localhost:8000" . $path); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_HEADER, true); curl_setopt($ch, CURLOPT_NOBODY, true); curl_setopt($ch, CURLOPT_TIMEOUT, 10); $response = curl_exec($ch); $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); $error = curl_error($ch); curl_close($ch); if ($error) { echo $path . " (" . $name . "): ERROR - " . $error . "
"; } else { echo $path . " (" . $name . "): " . $httpCode . "
"; } } ?>
