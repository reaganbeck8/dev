<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\PageModel;
use App\Models\ActivityLogModel;

class PageController extends Controller
{
    public function index(): void
    {
        $this->view('admin/pages/index', [
            'title' => 'Pages',
            'pages' => (new PageModel())->getAll(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/pages/form', [
            'title' => 'Add Page',
            'page'  => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->getPostData();
        $id   = (new PageModel())->create($data);

        (new ActivityLogModel())->record('created_page', $this->currentUser()['id'], 'page', $id);
        $this->session->flash('success', 'Page created successfully.');
        $this->redirect('/admin/pages');
    }

    public function edit(int $id): void
    {
        $page = (new PageModel())->findById($id);

        if (!$page) {
            $this->abort(404);
        }

        $this->view('admin/pages/form', [
            'title' => 'Edit Page',
            'page'  => $page,
        ]);
    }

    public function update(int $id): void
    {
        (new PageModel())->update($id, $this->getPostData());
        (new ActivityLogModel())->record('updated_page', $this->currentUser()['id'], 'page', $id);
        $this->session->flash('success', 'Page updated successfully.');
        $this->redirect('/admin/pages');
    }

    public function delete(int $id): void
    {
        (new PageModel())->delete($id);
        (new ActivityLogModel())->record('deleted_page', $this->currentUser()['id'], 'page', $id);
        $this->session->flash('success', 'Page deleted.');
        $this->redirect('/admin/pages');
    }

    private function getPostData(): array
    {
        return [
            'title'      => trim($_POST['title'] ?? ''),
            'slug'       => slug(trim($_POST['slug'] ?? $_POST['title'] ?? '')),
            'content'    => $_POST['content'] ?? null,
            'meta_title' => trim($_POST['meta_title'] ?? '') ?: null,
            'meta_desc'  => trim($_POST['meta_desc'] ?? '') ?: null,
            'status'     => $_POST['status'] ?? 'draft',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];
    }
}
