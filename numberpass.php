<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once __DIR__ . '/constants.php';

//Define necessary functions and variables
$number = 0;
$reverse = FALSE;
if (sizeof($argv) > 1)
{
    $reverse = $argv[1] == "r";
}

function increase($num)
{
    return $num + 1;
}

function decrease($num)
{
    return $num - 1;
}

//Output mode
if ($reverse)
{
    echo "Running in reverse mode.\n";
}
else
{
    echo "Running in normal mode.\n";
}

//Create connection to the server
echo "Connecting to ".Constant::ADDRESS.":".Constant::PORT." ...";
$connection = new AMQPStreamConnection(Constant::ADDRESS, Constant::PORT, Constant::USER, Constant::PASSWORD);
assert($connection.is_open(), " Unable to connect!\n");
echo "Success!\n";

$channel = $connection->channel();

//Declare queues
$channel->queue_declare(Constant::PASS_QUEUE_0, false, false, false, false);
$channel->queue_declare(Constant::PASS_QUEUE_1, false, false, false, false);

//Figure out which queue will be used for sending and which will be used for receiving data
$send_queue = ($reverse == TRUE ? Constant::PASS_QUEUE_1 : Constant::PASS_QUEUE_0);
$receive_queue = ($reverse == TRUE ? Constant::PASS_QUEUE_0 : Constant::PASS_QUEUE_1);

//Same with operations
$operation = ($reverse == TRUE ? 'decrease' : 'increase');

//Sends the number to $send_queue
function send_number()
{
    global $number, $channel, $send_queue;
    echo "Sending ".$number."...\n";
    $msg = new AMQPMessage($number);
    $channel->basic_publish($msg, '', $send_queue);
}

//Callback function to be executed upon a message retrieval:
$callback = function ($data) 
{
    global $number, $operation;
    echo 'Received '.$data->body."\n";
    $number = $operation($number);
    send_number();
};

//Set the callback:
$channel->basic_consume($receive_queue, '', false, true, false, false, $callback);

//Send the number at least once before listening
send_number();

echo "Waiting for messages from ".$receive_queue."...\n";

while ($channel->is_consuming()) 
{
    $channel->wait();
}
?>