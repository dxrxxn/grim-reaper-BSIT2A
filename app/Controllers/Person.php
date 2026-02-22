<?php

namespace App\Controllers;

use App\Models\PersonModel;
use App\Models\LogModel;
use CodeIgniter\Controller;

class Person extends Controller
{
    public function index()
    {
        $model = new PersonModel();
        $data['persons'] = $model->findAll();
        return view('person/index', $data);
    }

    public function save()
    {
        $model = new PersonModel();
        $logModel = new LogModel();

        $id       = $this->request->getPost('id');
        $name     = $this->request->getPost('name');
        $lastname = $this->request->getPost('lastname');
        $bday     = $this->request->getPost('bday');

        if (!$id || !$name || !$lastname || !$bday) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID, Firstname, Lastname and Birthday are required'
            ]);
        }

        if ($model->find($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID already exists'
            ]);
        }

        $data = [
            'id'       => $id,
            'name'     => $name,
            'lastname' => $lastname,
            'bday'     => $bday,
        ];

        if ($model->insert($data)) {
            $logModel->addLog('New Person added: ' . $name . ' ' . $lastname, 'ADD');
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save person'
        ]);
    }

    public function update()
    {
        $model = new PersonModel();
        $logModel = new LogModel();

        $id       = $this->request->getPost('id');
        $name     = $this->request->getPost('name');
        $lastname = $this->request->getPost('lastname');
        $bday     = $this->request->getPost('bday');

        if (empty($id) || empty($name) || empty($lastname) || empty($bday)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID, Firstname, Lastname and Birthday are required'
            ]);
        }

        if (!$model->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Person not found'
            ]);
        }

        $data = [
            'name'     => $name,
            'lastname' => $lastname,
            'bday'     => $bday,
        ];

        if ($model->update($id, $data)) {
            $logModel->addLog('Person updated: ' . $name . ' ' . $lastname, 'UPDATED');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Person updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error updating person'
        ]);
    }

    public function edit($id)
    {
        $model = new PersonModel();
        $person = $model->find($id);

        if ($person) {
            return $this->response->setJSON(['data' => $person]);
        }

        return $this->response->setStatusCode(404)
                              ->setJSON(['error' => 'Person not found']);
    }

    public function delete($id)
    {
        $model = new PersonModel();
        $logModel = new LogModel();

        if (!$model->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Person not found'
            ]);
        }

        if ($model->delete($id)) {
            $logModel->addLog('Person deleted', 'DELETED');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Person deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete person'
        ]);
    }

    public function fetchRecords()
    {
        $request = service('request');
        $model = new PersonModel();

        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? '';

        $totalRecords = $model->countAll();
        $result = $model->getRecords($start, $length, $searchValue);

        $data = [];
        $counter = $start + 1;

        foreach ($result['data'] as $row) {
            $row['row_number'] = $counter++;
            $data[] = $row;
        }

        return $this->response->setJSON([
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $result['filtered'],
            'data' => $data,
        ]);
    }
}