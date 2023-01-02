<?php

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

include "./functions.php";

// set some variables
$address = "127.0.0.1";
$port = 9001;
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($sock, $address, $port);
socket_listen($sock);
// This variable will hold client informations. 
$clients = [$sock];
$members = [];
echo "socket listening \n";
while (true) {
    $read = $writes = $exceptions = $clients;
    if (socket_select($read, $writes, $exceptions, 0)) {
        //echo "one";
        if (in_array($sock, $read)) {
            $c_socket = socket_accept($sock);
            // $msg = "\nWelcome to the PHP Test Server. \n" .
            //     "To quit, type 'quit'. To shut down the server type 'shutdown'.\r\n";
            // socket_write($c_socket, $msg, strlen($msg));
            $header = socket_read($c_socket, 1024);
            echo $header;
            handshake($header, $c_socket, $address, $port);
            $clients[] = $c_socket;
            $key = array_search($sock, $read);
            unset($read[$key]);
            $number_users = count($clients) - 1;
            echo "user " . $number_users . " is connections\n";
        }
        if (count($read) > 0) {
            foreach ($read as $current_socket) {
                $content = @socket_read($current_socket, 1024);

                if ($content == '' || $content === "") {
                    //var_dump($content);
                    $key3 = array_search($current_socket, $clients);
                    unset($clients[$key3]);
                    socket_close($current_socket);
                    //$clients =array_values($clients);
                    echo "connection of user " . $key3 . " is closed\n";
                    //echo 'number is ' . count($clients) . "\n";
                    // remove client from members array 
                    if (count($members) > 0) {
                        $key4 = array_search($current_socket, array_column($members, 'connection'));
                        unset($members[$key4]);
                        $members = array_values($members);
                    }
                } else {
                    $content = unmask($content);
                    $key2 = array_search($current_socket, $clients);
                    echo "user " . $key2 . " send " . $content . "\n";
                    //var_dump($content);
                    $decoded_message = json_decode($content, true);
                    //sleep(1);
                    if ($decoded_message) {
                        if ($decoded_message['type'] == 'config' && $decoded_message['number'] == 1) {
                            $members[] = [
                                'channel' => $decoded_message['channel'],
                                'connection' => $current_socket
                            ];
                            print_r($members);
                            echo 'user ' . $key2 . " conntected with " . $decoded_message['channel'] . " channel\n";
                        }
                        if ($decoded_message['type'] == "message") {
                            print_r($members);
                            $key = array_search($decoded_message['to'], array_column($members, 'channel'));
                            echo "message send to user $key in array \n";
                            if ($key !== false) {
                                //echo "three";
                                $replay = pack_data($content);
                                socket_write($members[$key]['connection'], $replay, strlen($replay));
                            }
                        }
                    }
                    // foreach ($clients as $client) {
                    //     if ($client != $sock && $client == $current_socket) {
                    //         $replay = "PHP: You said $content .\r\n";
                    //         $replay = pack_data($replay);
                    //         @socket_write($client, $replay, strlen($replay));
                    //     }
                    // }
                }
            }
        }
    }
}

socket_close($sock);
