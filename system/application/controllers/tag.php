<?php

class Tag extends Controller {

	function Tag()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->load->model('Tag_model');
		$this->template->set('page_title', 'Tags');
		$this->template->set('nav_links', array('tag/add' => 'New Tag'));
		$this->template->load('template', 'tag/index');
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'New Tag');

		/* Form fields */
		$data['tag_title'] = array(
			'name' => 'tag_title',
			'id' => 'tag_title',
			'maxlength' => '50',
			'size' => '50',
			'value' => '',
		);
		$data['tag_color'] = array(
			'name' => 'tag_color',
			'id' => 'tag_color',
			'maxlength' => '6',
			'size' => '6',
			'value' => '',
		);
		$data['tag_background'] = array(
			'name' => 'tag_background',
			'id' => 'tag_background',
			'maxlength' => '6',
			'size' => '6',
			'value' => '',
		);

		/* Form validations */
		$this->form_validation->set_rules('tag_title', 'Tag title', 'trim|required|min_length[2]|max_length[50]|unique[tags.title]');
		$this->form_validation->set_rules('tag_color', 'Tag color', 'trim|required|exact_length[6]|is_hex');
		$this->form_validation->set_rules('tag_background', 'Background color', 'trim|required|exact_length[6]|is_hex');

		/* Re-populating form */
		if ($_POST)
		{
			$data['tag_title']['value'] = $this->input->post('tag_title', TRUE);
			$data['tag_color']['value'] = $this->input->post('tag_color', TRUE);
			$data['tag_background']['value'] = $this->input->post('tag_background', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'tag/add', $data);
			return;
		}
		else
		{
			$data_tag_title = $this->input->post('tag_title', TRUE);
			$data_tag_color = $this->input->post('tag_color', TRUE);
			$data_tag_color = strtoupper($data_tag_color);
			$data_tag_background = $this->input->post('tag_background', TRUE);
			$data_tag_background = strtoupper($data_tag_background);

			$this->db->trans_start();
			if ( ! $this->db->query("INSERT INTO tags (title, color, background) VALUES (?, ?, ?)", array($data_tag_title, $data_tag_color, $data_tag_background)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding ' . $data_tag_title . ' - Tag', 'error');
				$this->template->load('template', 'tag/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add("Added " . $data_tag_title . ' - Tag successfully', 'success');
				redirect('tag');
				return;
			}
		}
		return;

	}

	function edit($id = 0)
	{
		$this->template->set('page_title', 'Edit Tag');

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->messages->add('Invalid Tag', 'error');
			redirect('tag');
			return;
		}

		/* Loading current group */
		$tag_data_q = $this->db->query("SELECT * FROM tags WHERE id = ?", array($id));
		if ($tag_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Tag', 'error');
			redirect('tag');
			return;
		}
		$tag_data = $tag_data_q->row();

		/* Form fields */
		$data['tag_title'] = array(
			'name' => 'tag_title',
			'id' => 'tag_title',
			'maxlength' => '50',
			'size' => '50',
			'value' => $tag_data->title,
		);
		$data['tag_color'] = array(
			'name' => 'tag_color',
			'id' => 'tag_color',
			'maxlength' => '6',
			'size' => '6',
			'value' => $tag_data->color,
		);
		$data['tag_background'] = array(
			'name' => 'tag_background',
			'id' => 'tag_background',
			'maxlength' => '6',
			'size' => '6',
			'value' => $tag_data->background,
		);
		$data['tag_id'] = $id;

		/* Form validations */
		$this->form_validation->set_rules('tag_title', 'Tag title', 'trim|required|min_length[2]|max_length[50]|uniquewithid[tags.title.' . $id . ']');
		$this->form_validation->set_rules('tag_color', 'Tag color', 'trim|required|exact_length[6]|is_hex');
		$this->form_validation->set_rules('tag_background', 'Background color', 'trim|required|exact_length[6]|is_hex');

		/* Re-populating form */
		if ($_POST)
		{
			$data['tag_title']['value'] = $this->input->post('tag_title', TRUE);
			$data['tag_color']['value'] = $this->input->post('tag_color', TRUE);
			$data['tag_background']['value'] = $this->input->post('tag_background', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'tag/edit', $data);
			return;
		}
		else
		{
			$data_tag_title = $this->input->post('tag_title', TRUE);
			$data_tag_color = $this->input->post('tag_color', TRUE);
			$data_tag_color = strtoupper($data_tag_color);
			$data_tag_background = $this->input->post('tag_background', TRUE);
			$data_tag_background = strtoupper($data_tag_background);

			$this->db->trans_start();
			if ( ! $this->db->query("UPDATE tags SET title = ?, color = ?, background = ? WHERE id = ?", array($data_tag_title, $data_tag_color, $data_tag_background, $id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating ' . $data_tag_title . ' - Tag', 'error');
				$this->template->load('template', 'tag/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add("Updated " . $data_tag_title . ' - Tag successfully', 'success');
				redirect('tag');
				return;
			}
		}
		return;

	}

	function delete($id)
	{
		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->messages->add('Invalid Tag', 'error');
			redirect('tag');
			return;
		}
		$data_valid_q = $this->db->query("SELECT * FROM tags WHERE id = ?", array($id));
		if ($data_valid_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Tag specified', 'error');
			redirect('tag');
			return;
		}

		/* Deleting Tag */
		$this->db->trans_start();
		if ( ! $this->db->query("UPDATE vouchers SET tag_id = 0 WHERE tag_id = ?", array($id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error removing Tags', 'error');
			redirect('tag');
			return;
		} else {
			if ( ! $this->db->query("DELETE FROM tags WHERE id = ?", array($id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error deleting Tag', 'error');
				redirect('tag');
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Tag deleted successfully', 'success');
				redirect('tag');
				return;
			}
		}
		return;
	}

}

/* End of file tag.php */
/* Location: ./system/application/controllers/tag.php */
