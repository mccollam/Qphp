<?php

class Q
{
	public $ip = '127.0.0.1';
	public $port = 11600;
	
	public function Q($ip='127.0.0.1', $port=11600)
	{
		$this->ip = $ip;
		$this->port = $port;
	}

	public function set_color($bulbs, $red=255, $green=255, $blue=255, $bright=255)
	{
		// Set the color of one or more bulbs
		$this->q_send_command($this->build_json_light_control($bulbs, $red, $green, $blue, $bright, 1, 0, 9));
		return true;
	}
	
	public function set_on($bulbs, $bright=255)
	{
		// Set one or more bulbs to on as a standard ('white') bulb
		$this->q_send_command($this->build_json_light_control($bulbs, 255, 255, 255, $bright, 1, 0, 8)); 
		return true;
	}

	public function set_off($bulbs)
	{
		// Set one or more bulbs to off
		$this->q_send_command($this->build_json_light_control($bulbs, 0, 0, 0, 0, 0, 0, 8)); 
		return true;
	}


	private function q_send_command($json)
	{
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		$len = strlen($json);
		
		socket_sendto($sock, $json, $len, 0, $this->ip, $this->port);
		
		socket_close($sock); 
	}
	
	private function build_json_light_control($bulbs, $red, $green, $blue, $bright, $iswitch, $matchValue, $effect)
	{
		if (!is_array($bulbs))
			$bulbs = array($bulbs);
		
		$json = "{ 'cmd':'light_ctrl', ";
		$json .= "'r':'$red', 'g':'$green', 'b':'$blue', 'bright':'$bright', ";
		$json .= "'sn_list': [";
		foreach ($bulbs as $bulb)
			$json .= "{ 'sn':'$bulb' }, ";
		$json .= "], 'iswitch':'$iswitch', 'matchValue':'$matchValue', 'effect':'$effect' }";
		
		return $json;
	}
}

?>
