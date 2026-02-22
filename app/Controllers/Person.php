<?php

namespace App\Controllers;
use App\Models\PersonModel;
use CodeIgniter\Controller;

class Person extends Controller
{
    public function index()
    {
        $model = new PersonModel();
        $data['persons'] = $model->findAll();
        return view('person_view', $data);
    }

    public function store()
    {
        $model = new PersonModel();

        $model->save([
            'name'     => $this->request->getPost('name'),
            'bday'     => $this->request->getPost('bday'),
            'lastname' => $this->request->getPost('lastname'),
        ]);

        return redirect()->to('/person');
    }
}