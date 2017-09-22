<?php namespace {namespace};

use CodeIgniter\Controller;

class {name} extends Controller
{
    /**
     * The object model for the main data type
     * on this controller.
     */
    protected $model;

    //--------------------------------------------------------------------

    public function __construct(...$params)
    {
        parent::__construct(...$params);

        $this->model = new {model}();
    }

    //--------------------------------------------------------------------

    /**
     * Displays the paginated results.
     */
    public function listAll()
    {
        echo view('{name}/listAll', [
            'rows' => $this->model->paginate(20),
        ]);
    }

    //--------------------------------------------------------------------

    /**
     * Handles the POST request to create a new object.
     */
    public function create()
    {

    }

    //--------------------------------------------------------------------

    /**
     * Handles the GET request to display the object.
     *
     * @param string $hashID
     */
    public function show(string $hashID)
    {
        echo view('{name}/show', [
            'row' => $this->model->findByHashedID($hashID)
        ]);
    }

    //--------------------------------------------------------------------

    /**
     * Handles the POST request to update an existing object.
     *
     * @param string $hashID
     */
    public function update(string $hashID)
    {

    }

    //--------------------------------------------------------------------
    /**
     * Handles the POST request to delete an existing object.
     *
     * @param string $hashID
     */
    public function delete(string $hashID)
    {
        $id = $this->model->decodeID($hashID);

        if (! $this->model->delete($id))
        {
            session()->setFlashdata('error', $this->model->errors());
        }

        return redirect('{namespace}\{name}::listAll');
    }

    //--------------------------------------------------------------------
}
