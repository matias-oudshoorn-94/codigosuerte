<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Usuarios que pueden entrar al panel (tabla users, via Ion Auth).
 */
class Usuarios extends MY_Controller
{
	const GRUPO_ADMIN = 1;

	function index()
	{
		$usuarios = $this->ion_auth->users()->result();
		foreach ($usuarios as $u)
		{
			$u->es_admin = $this->ion_auth->in_group(self::GRUPO_ADMIN, $u->id);
		}
		usort($usuarios, function ($a, $b) {
			return array((int) ! $a->es_admin, (int) ! $a->active, $a->username) <=> array((int) ! $b->es_admin, (int) ! $b->active, $b->username);
		});
		$this->data['usuarios'] = $usuarios;
		$this->data['acciones'] = array(
			array('url' => site_url('admin/usuarios/edit/0'), 'texto' => 'Nuevo usuario', 'icono' => 'ti-plus', 'clase' => 'principal'),
		);
		$this->mostrar('admin/usuarios/list', 'Usuarios del panel', 'usuarios');
	}

	/** Alta (id 0) o edicion. */
	function edit($id = 0)
	{
		$id = (int) $id;
		$usuario = $id ? $this->ion_auth->user($id)->row() : NULL;
		if ($id && ! $usuario)
		{
			p_flash('Ese usuario no existe.', 'error');
			redirect('admin/usuarios');
		}
		$es_yo = $usuario && (int) $usuario->id === (int) $this->data['admin_usuario']->id;
		$minimo = (int) $this->config->item('min_password_length', 'ion_auth');
		$errores = array();
		$valores = $usuario ? (array) $usuario : array('active' => 1);
		$valores['es_admin'] = $usuario ? (int) $this->ion_auth->in_group(self::GRUPO_ADMIN, $usuario->id) : 1;

		if ($this->input->method() === 'post')
		{
			$datos = array(
				'username'   => trim((string) $this->input->post('username')),
				'email'      => trim((string) $this->input->post('email')),
				'first_name' => trim((string) $this->input->post('first_name')),
				'last_name'  => trim((string) $this->input->post('last_name')),
				'active'     => $es_yo ? 1 : ($this->input->post('active') == 1 ? 1 : 0),
			);
			$es_admin = $es_yo ? 1 : ($this->input->post('es_admin') == 1 ? 1 : 0);
			$clave = (string) $this->input->post('clave');

			if ($datos['username'] === '') $errores['username'] = 'El usuario es obligatorio.';
			elseif ($this->db->where('username', $datos['username'])->where('id !=', $id)->count_all_results('users') > 0) $errores['username'] = 'Ya existe otro usuario con ese nombre.';
			if ($datos['email'] === '' || ! filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) $errores['email'] = 'Poné un email válido.';
			if ( ! $id && $clave === '') $errores['clave'] = 'Elegí una contraseña.';
			elseif ($clave !== '' && strlen($clave) < $minimo) $errores['clave'] = 'La contraseña tiene que tener al menos '.$minimo.' caracteres.';

			if ( ! $errores)
			{
				if ($id)
				{
					if ($clave !== '') $datos['password'] = $clave; // Ion Auth la encripta
					$this->ion_auth->update($id, $datos);
				}
				else
				{
					$id = (int) $this->ion_auth->register($datos['username'], $clave, $datos['email'],
						array('first_name' => $datos['first_name'], 'last_name' => $datos['last_name']), array(self::GRUPO_ADMIN));
					if ( ! $id)
					{
						p_flash('No se pudo crear el usuario.', 'error');
						redirect('admin/usuarios');
					}
					if ( ! $datos['active']) $this->ion_auth->deactivate($id);
				}

				$tiene = $this->ion_auth->in_group(self::GRUPO_ADMIN, $id);
				if ($es_admin && ! $tiene) $this->ion_auth->add_to_group(self::GRUPO_ADMIN, $id);
				if ( ! $es_admin && $tiene) $this->ion_auth->remove_from_group(self::GRUPO_ADMIN, $id);

				p_flash($usuario ? 'Se guardó el usuario.' : 'Se creó el usuario.');
				redirect('admin/usuarios');
			}
			$valores = array_merge($valores, $datos, array('es_admin' => $es_admin));
		}

		$this->data['id'] = $id;
		$this->data['es_yo'] = $es_yo;
		$this->data['minimo_clave'] = $minimo;
		$this->data['valores'] = $valores;
		$this->data['errores'] = $errores;
		$this->data['acciones'] = array(
			array('url' => site_url('admin/usuarios'), 'texto' => 'Usuarios', 'icono' => 'ti-arrow-left'),
		);
		$this->mostrar('admin/usuarios/edit', $id ? 'Editar usuario' : 'Nuevo usuario', 'usuarios');
	}

	function eliminar()
	{
		$this->solo_post();
		$id = (int) $this->input->post('id');
		if ($id === (int) $this->data['admin_usuario']->id)
		{
			p_flash('No podés eliminar tu propio usuario.', 'error');
		}
		elseif ($this->ion_auth->delete_user($id))
		{
			p_flash('Se eliminó el usuario.');
		}
		else
		{
			p_flash('No se pudo eliminar el usuario.', 'error');
		}
		redirect('admin/usuarios');
	}
}
