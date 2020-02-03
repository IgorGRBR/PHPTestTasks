<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once __DIR__ . '/constants.php';


//Create connection to the server
echo "Connecting to ".Constant::ADDRESS.":".Constant::PORT." ...";
$connection = new AMQPStreamConnection(Constant::ADDRESS, Constant::PORT, Constant::USER, Constant::PASSWORD);
assert($connection.is_open(), "Unable to connect!\n");
echo "Success!\n";

$channel = $connection->channel();

//Declare a queue
$channel->queue_declare(Constant::RANDOM_QUEUE, false, false, false, false);

echo "Now waiting for messages...\n";

//Callback function to be executed upon a message retrieval:
$callback = function ($msg) 
{
    echo 'Received ', $msg->body, "\n";
};

//Set the callback:
$channel->basic_consume(Constant::RANDOM_QUEUE, '', false, true, false, false, $callback);

while ($channel->is_consuming()) 
{
    $channel->wait();
}
?>