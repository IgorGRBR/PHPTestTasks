<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once __DIR__ . '/constants.php';


//Create connection to the server
echo "Connecting to ".Constant::ADDRESS.":".Constant::PORT." ...";
$connection = new AMQPStreamConnection(Constant::ADDRESS, Constant::PORT, Constant::USER, Constant::PASSWORD);
assert($connection.is_open(), " Unable to connect!\n");
echo "Success!\n";

$channel = $connection->channel();

//Declare a queue
$channel->queue_declare(Constant::RANDOM_QUEUE, false, false, false, false);
/*queue_declare method has following arguments:
$queue - queue name. 'random_number_queue' in out case.
$passive - boolean flag
$durable - boolean flag
$exclusive - boolean flag
$auto delete - boolean flag
*/

$random_number = rand(1, 64);
$msg = new AMQPMessage($random_number);
$channel->basic_publish($msg, '', Constant::RANDOM_QUEUE);

echo "Sent ".$random_number." into ".Constant::RANDOM_QUEUE."\n";

//Close the channel and the connection
$channel->close();
$connection->close();
?>