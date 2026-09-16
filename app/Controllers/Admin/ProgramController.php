<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;

/**
 * Programs and Facilities manager (two tables, one controller —
 * the structures are identical apart from the table name).
 */
class ProgramController extends AdminController
{
    private const TABLES = ['programs', 'facilities'];

    public function index(): string
    {
        [$table, $section, $viewDir, $label, $plural] = $this->context();
        return $this->adminView("admin/items/index", [
            'rows'          => Database::fetchAll("SELECT * FROM `{$table}` ORDER BY sort_order, id"),
            'section'       => $section,
            'label'         => $label,
            'plural'        => $plural,
            'title'         => $plural,
            'activeSection' => $section,
        ]);
    }

    public function create(): string
    {
        [$table, $section, , $label] = $this->context();
        return $this->adminView('admin/items/form', [
            'item'          => null,
            'section'       => $section,
            'label'         => $label,
            'title'         => "Add {$label}",
            'activeSection' => $section,
        ]);
    }

    public function store(): string
    {
        [$table, $section, , $label] = $this->context();
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect("/admin/{$section}/create");
        }
        Database::insert($table, $data);
        Session::flash('success', "{$label} created.");
        return $this->redirect("/admin/{$section}");
    }

    public function edit(string $id): string
    {
        [$table, $section, , $label] = $this->context();
        $item = Database::fetch("SELECT * FROM `{$table}` WHERE id = ?", [(int) $id]);
        if (!$item) {
            return $this->redirect("/admin/{$section}");
        }
        return $this->adminView('admin/items/form', [
            'item'          => $item,
            'section'       => $section,
            'label'         => $label,
            'title'         => "Edit {$label}",
            'activeSection' => $section,
        ]);
    }

    public function update(string $id): string
    {
        [$table, $section, , $label] = $this->context();
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect("/admin/{$section}/" . (int) $id . '/edit');
        }
        Database::update($table, $data, (int) $id);
        Session::flash('success', "{$label} updated.");
        return $this->redirect("/admin/{$section}");
    }

    public function destroy(string $id): string
    {
        [$table, $section, , $label] = $this->context();
        Database::delete($table, (int) $id);
        Session::flash('success', "{$label} deleted.");
        return $this->redirect("/admin/{$section}");
    }

    // ------- facility aliases -------
    public function facilityIndex(): string { $this->use('facilities'); return $this->index(); }
    public function facilityCreate(): string { $this->use('facilities'); return $this->create(); }
    public function facilityStore(): string { $this->use('facilities'); return $this->store(); }
    public function facilityEdit(string $id): string { $this->use('facilities'); return $this->edit($id); }
    public function facilityUpdate(string $id): string { $this->use('facilities'); return $this->update($id); }
    public function facilityDestroy(string $id): string { $this->use('facilities'); return $this->destroy($id); }

    private string $forced = '';

    private function use(string $section): void
    {
        $this->forced = $section;
    }

    /** Resolve which table/section is being managed from the request path. */
    private function context(): array
    {
        $section = $this->forced !== '' ? $this->forced : $this->guessSection();
        $table = $section === 'facilities' ? 'facilities' : 'programs';
        $label = $section === 'facilities' ? 'Facility' : 'Program';
        $plural = $section === 'facilities' ? 'Facilities' : 'Programs';
        return [$table, $section, 'items', $label, $plural];
    }

    private function guessSection(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        return str_contains($uri, '/facilities') ? 'facilities' : 'programs';
    }

    private function validate(): array
    {
        $v = new Validator($_POST);
        $v->required('title', 'Title')->max('title', 150, 'Title');

        $data = $v->validated();
        $data['active'] = isset($_POST['active']) ? 1 : 0;
        $data['sort_order'] = (int) ($_POST['sort_order'] ?? 0);

        // Photo upload (optional; falls back to emoji on the website).
        $table = $this->context()[0];
        $section = $this->context()[1];
        try {
            $old = $_POST['_current_photo'] ?? null;
            $data['photo'] = Uploader::image('photo', $table, $old !== '' ? $old : null);
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data, ['photo' => []]);
            return [$data, false];
        }
        if (!$data['photo']) {
            unset($data['photo']);
        }

        if ($v->fails()) {
            with_input($data, $v->errors());
            return [$data, false];
        }
        return [$data, true];
    }
}
