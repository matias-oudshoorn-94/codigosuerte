<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends MX_Controller
{
	var $data;

	function __construct()
	{
		parent::__construct();
		$this->data = $this->config->load('sitio', true);
		if (!$this->ion_auth->logged_in()) redirect('admin/login');
		$this->template->set_theme('admin');	
		$this->template->set_partial('menu', 'admin/partials/menu');
	}

	protected function get_search($dir)
	{
		if($this->input->post('search') !== FALSE):			
			$this->session->set_userdata($dir.'_search', $this->input->post('search')); 
			return $this->session->userdata($dir.'_search');
		else:
			if($this->session->userdata($dir.'_search') != '')
				return $this->session->userdata($dir.'_search');
			elseif($this->input->get('search')) 
				$this->session->set_userdata($dir.'_search', $this->input->get('search'));
			elseif($this->session->userdata($dir.'_search') != '')
				$this->session->userdata($dir.'_search');
			else
				$this->session->set_userdata($dir.'_search', '');

			return $this->session->userdata($dir.'_search');
		endif;		
		//$sql['bnombre'] = $this->input->get('bnombre');
	}
	
	protected function get_limit($dir, $segment = 7, $limit = 10)
	{
		if($this->uri->segment($segment)) $this->session->set_userdata($dir.'_per_page', $this->uri->segment($segment)); 
		
		if($this->session->userdata($dir.'_per_page')) 
			return $this->session->userdata($dir.'_per_page');			
		else return $limit;
	}
	protected function get_sort_order($dir, $sort_order, $segment = 5)
	{
		if($this->uri->segment($segment))
		{
			$this->session->set_userdata($dir.'_sort_order', $this->uri->segment($segment));
			return $this->session->userdata($dir.'_sort_order');
		}	
		else 
		{
			if($this->session->userdata($dir.'_sort_order'))	
				return $this->session->userdata($dir.'_sort_order');	
			else
				$this->session->set_userdata($dir.'_sort_order', $sort_order);
				return $this->session->userdata($dir.'_sort_order');
		}	
	}

	protected function get_sort_by($dir, $sort_by, $segment = 4)
	{
		if($this->uri->segment($segment))
		{
			$this->session->set_userdata($dir.'_sort_by', $this->uri->segment($segment));
			return $this->session->userdata($dir.'_sort_by');
		}	
		else 
		{
			if($this->session->userdata($dir.'_sort_by'))	
				return $this->session->userdata($dir.'_sort_by');	
			else
			{
				$this->session->set_userdata($dir.'_sort_by', $sort_by);
				return $this->session->userdata($dir.'_sort_by');
			}	
		}
	}
}	