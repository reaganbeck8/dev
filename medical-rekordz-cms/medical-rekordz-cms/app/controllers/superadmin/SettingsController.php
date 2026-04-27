<?php

namespace App\Controllers\Superadmin;

use Core\Controller;
use App\Models\SettingModel;
use App\Models\ActivityLogModel;

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->view('superadmin/dashboard/settings', [
            'title'    => 'System Settings',
            'settings' => (new SettingModel())->getAll(),
        ]);
    }

    public function update(): void
    {
        $model    = new SettingModel();
        $settings = $_POST['settings'] ?? [];

        foreach ($settings as $key => $value) {
            $type  = $_POST['types'][$key] ?? 'string';
            $group = $_POST['groups'][$key] ?? 'general';
            $model->set($key, $value, $type, $group);
        }

        (new ActivityLogModel())->record('updated_system_settings', $this->currentUser()['id']);
        $this->session->flash('success', 'Settings saved.');
        $this->redirect('/' . SUPERADMIN_SLUG . '/settings');
    }
}
