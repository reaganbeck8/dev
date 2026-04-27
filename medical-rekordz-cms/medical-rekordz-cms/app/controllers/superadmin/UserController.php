<?php

namespace App\Controllers\Superadmin;

use Core\Controller;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class UserController extends Controller
{
    public function index(): void
    {
        $this->view('superadmin/users/index', [
            'title' => 'All Users',
            'users' => (new UserModel())->getAll(),
        ]);
    }

    public function create(): void
    {
        $this->view('superadmin/users/form', [
            'title' => 'Add User',
            'user'  => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->getPostData();

        if (empty($data['password'])) {
            $this->session->flash('error', 'Password is required.');
            $this->redirect('/' . SUPERADMIN_SLUG . '/users/create');
        }

        $id = (new UserModel())->create($data);
        (new ActivityLogModel())->record('created_user', $this->currentUser()['id'], 'user', $id);
        $this->session->flash('success', 'User created.');
        $this->redirect('/' . SUPERADMIN_SLUG . '/users');
    }

    public function edit(int $id): void
    {
        $user = (new UserModel())->findById($id);

        if (!$user) {
            $this->abort(404);
        }

        $this->view('superadmin/users/form', [
            'title' => 'Edit User',
            'user'  => $user,
        ]);
    }

    public function update(int $id): void
    {
        $model = new UserModel();
        $data  = $this->getPostData();

        $model->update($id, $data);

        if (!empty($data['password'])) {
            $model->updatePassword($id, $data['password']);
        }

        (new ActivityLogModel())->record('updated_user', $this->currentUser()['id'], 'user', $id);
        $this->session->flash('success', 'User updated.');
        $this->redirect('/' . SUPERADMIN_SLUG . '/users');
    }

    public function delete(int $id): void
    {
        if ($id === (int) $this->currentUser()['id']) {
            $this->session->flash('error', 'Cannot delete your own account.');
            $this->redirect('/' . SUPERADMIN_SLUG . '/users');
        }

        (new UserModel())->delete($id);
        (new ActivityLogModel())->record('deleted_user', $this->currentUser()['id'], 'user', $id);
        $this->session->flash('success', 'User deleted.');
        $this->redirect('/' . SUPERADMIN_SLUG . '/users');
    }

    private function getPostData(): array
    {
        return [
            'name'      => trim($_POST['name'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'password'  => $_POST['password'] ?? '',
            'role'      => $_POST['role'] ?? 'admin',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }
}
