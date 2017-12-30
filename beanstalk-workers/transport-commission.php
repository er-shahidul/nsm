<?php
require_once 'worker-config.php';

// Set which queues to bind to
$queue->watch('transport_commission');

// pick a job and process it
while ($job = $queue->reserve()) {
    $data = $job->getData();
    $data = json_decode($data, true);

    sleep(1);
    exec('php ' . $console . ' nsm:initiate:transport-commission ' . $data['deliveryId']);
    $queue->delete($job);
}