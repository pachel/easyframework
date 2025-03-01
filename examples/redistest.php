<?php
echo "asda";
exit();
$redis = new Redis();
//Connecting to Redis
$redis->connect('redis.tdfsteel.local', 6379);
$redis->auth('redispassword');

if ($redis->ping()) {
    echo "PONG";
}

