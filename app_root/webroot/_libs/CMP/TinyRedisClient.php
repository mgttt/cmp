<?php
/**
 * TinyRedisClient - the most lightweight Redis client written in PHP
 *
 * Usage example:
 * $client = new TinyRedisClient("host:port");
 * $client->set("key", "value");
 * $value = $client->get("key");
 *
 * Full list of commands you can see on http://redis.io/commands
 *
 * @author Petr Trofimov <petrofimov@yandex.ru>
 * @see https://github.com/ptrofimov
 * @see https://github.com/ptrofimov/tinyredisclient
 */
namespace CMP;
class TinyRedisClient
{
	private $_server;
	private $_socket;
	public function __construct($server = 'localhost:6379')
	{
		$this->_server = $server;
	}
	public function __call($method, array $args)
	{
		array_unshift($args, $method);
		$cmd = '*' . count($args) . "\r\n";
		foreach ($args as $item) {
			$cmd .= '$' . strlen($item) . "\r\n" . $item . "\r\n";
		}
		fwrite($this->_getSocket(), $cmd);
		return $this->_parseResponse();
	}
	private function _getSocket()
	{
		return $this->_socket ? $this->_socket
			: ($this->_socket = stream_socket_client($this->_server));
	}
	private function _parseResponse()
	{
		$line = fgets($this->_getSocket());
		list($type, $result) = array($line[0], substr($line, 1, strlen($line) - 3));
		if ($type == '-') { // error message
			throw new Exception($result);
		} elseif ($type == '$') { // bulk reply
			if ($result == -1) {
				$result = null;
			} else {
				$line = fread($this->_getSocket(), $result + 2);
				$result = substr($line, 0, strlen($line) - 2);
			}
		} elseif ($type == '*') { // multi-bulk reply
			$count = ( int ) $result;
			for ($i = 0, $result = array(); $i < $count; $i++) {
				$result[] = $this->_parseResponse();
			}
		}
		return $result;
	}
}
