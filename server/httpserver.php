<?php

// set some variables
$host = "127.0.0.1";
$port = 9001;
// don't timeout!
set_time_limit(0);
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// bind socket to port
socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
// start listening for connections
socket_listen($socket) or die("Could not set up socket listener\n");
echo "listen \n";
// accept incoming connections
// spawn another socket to handle communication
$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
echo "accetp \n";
// read client input
$input = socket_read($spawn, 1024) or die("Could not read input\n");
// clean up input string
//$input = trim($input);
echo "Client Message : \n" . $input;
// reverse client input and send back
$file = file_get_contents('./test.html');

$respons = "HTTP/1.1 200 OK\r\n" .
    "date: Sun, 18 Dec 2022 14:57:21 GMT\r\n" .
    "expires: Sun, 18 Dec 2022 15:07:21 GMT\r\n" .
    "cache-control: public, max-age=600\r\n" .
    "etag: \"Pi0LFw\"\r\n" .
    "content-type: text/html\r\n" .
    "\n";
$output = $respons . $file;
echo "---------------------\n Server Message : \n" . $output;


socket_write($spawn, $output, strlen($output)) or die("Could not write output\n");
// close sockets
socket_close($spawn);
