<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Video extends CI_Controller {
	public function stream($stationCode)
	{
        $this->load->helper('url');
		$videoUrl = "http://172.19.3.219:8889/" . strtolower($stationCode);
        redirect($videoUrl);
	}
}
