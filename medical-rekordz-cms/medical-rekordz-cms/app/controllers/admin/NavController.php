<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\NavModel;
use App\Models\PageModel;
use App\Models\ActivityLogModel;

class NavController extends Controller
{
    public function index(): void
    {
        $this->view('admin/nav/index', [
            'title' => 'Navigation',
            'items' => (new NavModel())->getMenuItems(false),
            'pages' => (new PageModel())->getAll(),
        ]);
    }

    public function store(): void
    {
        $data = $this->getPostData();
        (new NavModel())->create($data);

        (new ActivityLogModel())->record('created_nav_item', $this->currentUser()['id']);
        $this->session->flash('success', 'Menu item added.');
        $this->redirect('/admin/nav');
    }

    public function update(int $id): void
    {
        (new NavModel())->update($id, $this->getPostData());
        (new ActivityLogModel())->record('updated_nav_item', $this->currentUser()['id'], 'nav_item', $id);
        $this->session->flash('success', 'Menu item updated.');
        $this->redirect('/admin/nav');
    }

    public function delete(int $id): void
    {
        (new NavModel())->delete($id);
        $this->session->flash('success', 'Menu item deleted.');
        $this->redirect('/admin/nav');
    }

    public function reorder(): void
    {
        $ids = $_POST['order'] ?? [];
        if (!empty($ids)) {
            (new NavModel())->reorder($ids);
        }
        $this->json(['success' => true]);
    }

    private function getPostData(): array
    {
        return [
            'label'      => trim($_POST['label'] ?? ''),
            'url'        => trim($_POST['url'] ?? '') ?: null,
            'page_id'    => !empty($_POST['page_id']) ? (int) $_POST['page_id'] : null,
            'parent_id'  => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];
    }
}
