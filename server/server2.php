<?php

// set some variables
$host = "127.0.0.1";
$port = 9001;

$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
echo "created \n";
// bind socket to port
socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
echo "binded \n";
// start listening for connections
socket_listen($socket) or die("Could not set up socket listener\n");
echo "listening \n";

// accept clinet 
// read write from any client 


$clients = [$socket];
echo "socket listening \n";
while (true) {
    $read = $writes = $exceptions = $clients;
    //client 1 
    if (socket_select($read, $writes, $exceptions, 0)) {
        //echo "one";
        if (in_array($socket, $read)) {
            $c_socket = socket_accept($socket);
            $clients[] = $c_socket;
            $key = array_search($socket, $read);
            unset($read[$key]);
            $number_users = count($clients) - 1;
            echo "user " . $number_users . " is connections\n";
        }        
        if (count($read) > 0) {
            foreach ($read as $current_socket) {
                $content = @socket_read($current_socket, 1024);

                if ($content == '') {
                    $key3 = array_search($current_socket, $clients);
                    unset($clients[$key3]);
                    socket_close($current_socket);
                    echo "connection of user " . $key3 . " is closed\n";
                } else {
                    $key3 = array_search($current_socket, $clients);
                    echo "Client $key3 send Message : \n" . $content . "\n";
                    $output = "from server : \n" . $content . "\n";
                    socket_write($current_socket, $output, strlen($output)) or die("Could not write output\n");
                }
            }
        }
    }
    
    
}
// accept incoming connections
// spawn another socket to handle communication
$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
echo "accetping \n";
// read client input
$input = socket_read($spawn, 1024) or die("Could not read input\n");
// clean up input string
//$input = trim($input);
echo "Client Message : \n" . $input;
// reverse client input and send back
$output = "from server : \n" . $input . "\n";
socket_write($spawn, $output, strlen($output)) or die("Could not write output\n");
// close sockets
socket_close($spawn);
