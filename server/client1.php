<?php

// set some variables
$host = "127.0.0.1";
$port = 9001;

// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
echo "created \n";
// bind socket to port
$result = socket_connect($socket, $host, $port) or die("Could not bind to socket\n");
echo "binded \n";
// start listening for connections

while(true){
    $message = readline('Enter your message:');
    socket_write($socket, $message, strlen($message)) or die("Could not write output\n");
    $input = socket_read($socket, 1024) or die("Could not read input\n");
    echo "server say :" . $input;
}
socket_close($spawn);