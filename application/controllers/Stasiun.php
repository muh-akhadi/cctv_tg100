<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stasiun extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function detail($kode)
	{
		// $this->load->view('station');
		$geojsonPath = FCPATH . 'assets/ProgressTG.geojson';
		$geojsonData = file_get_contents($geojsonPath);

		if (!$geojsonData) {
            show_error('File GeoJSON tidak ditemukan.', 404);
        }

		// Decode GeoJSON menjadi array
        $features = json_decode($geojsonData, true)['features'];
        $stasiun = null;

        // Cari data berdasarkan 'code'
        foreach ($features as $feature) {
            if ($feature['properties']['code'] == $kode) {
                $stasiun = $feature['properties'];
                break;
            }
        }

		// Jika data tidak ditemukan
        if (!$stasiun) {
            show_error('Stasiun tidak ditemukan.', 404);
        }

        // Kirim data ke view
        $data['stasiun'] = $stasiun;
        $this->load->view('stasiun_detail', $data);
	}
}
