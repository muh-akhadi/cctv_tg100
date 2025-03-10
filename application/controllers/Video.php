<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Video extends CI_Controller {
	public function stream($stationCode)
	{
        $this->load->helper('url');
		$videoUrl = "http://localhost/" . strtolower($stationCode);
        redirect($videoUrl);
	}
}
