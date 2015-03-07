<?php

class Q
{
	public $ip = '127.0.0.1';
	public $port = 11600;
	
	private $bufsize = 2048;	// max accepted response (in bytes)
	
	/***** Public methods *****/
	
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
	
	public function list_bulbs()
	{
		// List the bulbs known by a Q station
		$bulbs = $this->q_send_command_expect_response("{ 'cmd':'light_list' }");
		return json_decode($bulbs, true)['led'];
	}
	
	public function set_bulb_title($bulb, $title='lightX')
	{
		$json = "{ 'cmd':'set_title', 'sn':'" . $bulb . "','title':'" . $title . "' }";
		$this->q_send_command($json);
	}
	
	public function save_lights()
	{
		// FIXME: Unimplemented (Not sure what this does yet!)
	}
	
	public function music_sync($bulb)
	{
		// FIXME: It doesn't look like there's a way to *unset* this!
		$json = "{ 'cmd':'set_musicled', 'sn':'" . $bulb . "' }";
		$this->q_send_command($json);
	}
	
	public function music_sync_group($group)
	{
		// FIXME: Unimplemented (How does this differ from normal music sync?)
	}
	
	public function delete($bulb)
	{
		// Delete a bulb from the Q station
		// FIXME: Is there a way to re-add them?  (This is untested until I find that out!)
		$json = "{ 'cmd':'del_device', 'sn':'" . $bulb . "' }";
		$this->q_send_command($json);
	}

	/***** Private methods *****/

	private function q_send_command($json)
	{
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		$len = strlen($json);
		
		socket_sendto($sock, $json, $len, 0, $this->ip, $this->port);
		
		socket_close($sock); 
	}
	
	private function q_send_command_expect_response($json)
	{
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>1, "usec"=>0));
		$len = strlen($json);
		$response = '';
		
		socket_sendto($sock, $json, $len, 0, $this->ip, $this->port);
		socket_recvfrom($sock, $response, $this->bufsize, 0, $this->ip, $this->port);
		
		socket_close($sock);
		
		return $response;
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
