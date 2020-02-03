# PHPTestTasks
This repository contains following scripts:
- sendrand.php - sends a random number into a RANDOM_QUEUE (Defined in constants.php)
- recvrand.php - recieves a number from RANDOM_QUEUE (Defined in constants.php)
- numberpass.php - recievens a number from the first queue and sends its own number into the second one. This script can be ran in 2 modes: normal and reverse. Normal mode operates normally, while reverse mode has first and second qeues reversed. This script runs in normal mode by default. To run it in reverse mode, just pass an "r" argument (like this "php ./numberpass.php r")
