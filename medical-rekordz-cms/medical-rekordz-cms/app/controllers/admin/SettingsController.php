<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\SettingModel;
use App\Models\ActivityLogModel;

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->view('admin/settings/index', [
            'title'    => 'Settings',
            'settings' => (new SettingModel())->getAll(),
        ]);
    }

    public function update(): void
    {
        $model    = new SettingModel();
        $settings = $_POST['settings'] ?? [];

        foreach ($settings as $key => $value) {
            $type = $_POST['types'][$key] ?? 'string';
            $model->set($key, $value, $type);
        }

        (new ActivityLogModel())->record('updated_settings', $this->currentUser()['id']);
        $this->session->flash('success', 'Settings saved.');
        $this->redirect('/admin/settings');
    }
}
