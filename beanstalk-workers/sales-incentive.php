<?php
require_once 'worker-config.php';

// Set which queues to bind to
$queue->watch('salas_incentive');

// pick a job and process it
while ($job = $queue->reserve()) {
    $data = $job->getData();
    $data = json_decode($data, true);

    sleep(1);
    exec('php ' . $console . ' nsm:generate:sales-incentive ' . $data['orderId'] . ' ' . $data['durationType']);
    echo $data['agentId'].PHP_EOL;
    $queue->delete($job);
}