<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Event;

class EventController extends AdminController
{
    public function index(): string
    {
        return $this->adminView('admin/events/index', [
            'events'        => Event::allOrdered(),
            'title'         => 'Events',
            'activeSection' => 'events',
        ]);
    }

    public function create(): string
    {
        return $this->adminView('admin/events/form', [
            'item'          => null,
            'title'         => 'Add Event',
            'activeSection' => 'events',
        ]);
    }

    public function store(): string
    {
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/events/create');
        }
        try {
            $data['image'] = Uploader::image('image', 'events');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data);
            return $this->redirect('/admin/events/create');
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        Event::create($data);
        Session::flash('success', 'Event created.');
        return $this->redirect('/admin/events');
    }

    public function edit(string $id): string
    {
        $item = Event::find((int) $id);
        if (!$item) {
            return $this->redirect('/admin/events');
        }
        return $this->adminView('admin/events/form', [
            'item'          => $item,
            'title'         => 'Edit Event',
            'activeSection' => 'events',
        ]);
    }

    public function update(string $id): string
    {
        $item = Event::find((int) $id);
        if (!$item) {
            return $this->redirect('/admin/events');
        }
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/events/' . (int) $id . '/edit');
        }
        try {
            $data['image'] = Uploader::image('image', 'events', $item['image']);
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data);
            return $this->redirect('/admin/events/' . (int) $id . '/edit');
        }
        Event::update((int) $id, $data);
        Session::flash('success', 'Event updated.');
        return $this->redirect('/admin/events');
    }

    public function destroy(string $id): string
    {
        $item = Event::find((int) $id);
        if ($item) {
            if ($item['image']) {
                Uploader::delete($item['image']);
            }
            Event::remove((int) $id);
        }
        Session::flash('success', 'Event deleted.');
        return $this->redirect('/admin/events');
    }

    private function validate(): array
    {
        $v = new Validator($_POST);
        $v->required('title', 'Title')->max('title', 200, 'Title')
          ->required('event_date', 'Event date');

        $data = $v->validated();
        $data['event_date'] = date('Y-m-d', strtotime((string) $data['event_date'])) ?: date('Y-m-d');

        if ($v->fails()) {
            with_input($data, $v->errors());
            return [$data, false];
        }
        return [$data, true];
    }
}
