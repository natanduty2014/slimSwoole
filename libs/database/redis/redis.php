<?php

namespace Lib\database\redis;

class redis
{
    static private function conn()
    {
        try {
            //create redis instance
            $redis = new \Redis();
            //connect with server and port
            $redis->connect('127.0.0.1', 6379);
            $redis->auth(['REDIS_PASSWORD' => 'redis']);
            //$redis->auth(['user' => 'phpredis', 'pass' => 'phpredis']);
            return $redis;
        } catch (\Exception $e) {
            \var_dump("redis: " . $e->getMessage());
            return $e;
        }
    }

    /**
     * @param $key
     * @param $value
     * @return bool|string
     */
    static public function save($key, $value)
    {
        $redis = self::conn();
        $redis->set($key, $value);
        return self::get($key);
    }

    /**
     * @param $key
     * @return bool|string
     */
    static public function get($key)
    {

        $redis = self::conn();
        $value = $redis->get($key);
        return $value;
    }

    /**
     * @param $key
     * @return bool|string
     */
    static public function exists($key)
    {

        $redis = self::conn();
        $value = $redis->exists($key);
        return $value;
    }

    /**
     * @param $key
     * @return bool|string
     */
    static public function delete($key)
    {

        $redis = self::conn();
        $value = $redis->del($key);
        return $value;
    }

    /**
     * @param $key
     * @param $value
     * @return bool|string
     */
    static public function update($key, $value)
    {

        $redis = self::conn();
        $redis->set($key, $value);
        return $value;
    }
}
