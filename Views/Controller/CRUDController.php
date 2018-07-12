@php namespace {namespace};

use CodeIgniter\Controller;

class {name} extends Controller
{
    /**
     * The object model for the main data type
     * on this controller.
     */
    protected $model;

    public function __construct(...$params)
    {
        parent::__construct(...$params);

        $this->model = new {model}();
    }

    /**
     * Displays the paginated results.
     */
    public function listAll()
    {
        echo view('{name}/listAll', [
            'rows' => $this->model->paginate(20),
        ]);
    }

    /**
     * Displays the New Object form.
     */
    public function create()
    {
        echo view('{name}/form', [
            'pageAction' => 'create'
        ]);
    }

    /**
     * Handles the GET request to display the object.
     *
     * @param int $id
     */
    public function show(int $id)
    {
        $item = $this->model->find($id);

        if (is_null($item))
        {
            return redirect()->back()->withInput()->with('error', 'Object not found');
        }

        echo view('{name}/show', [
            'pageAction' => 'edit',
            'item'       => $item
        ]);
    }

    /**
     * Saves an object. Used for both creating
     * a new object, and updating an existing one.
     */
    public function save(int $id = null)
    {
        $item = new Entity($this->request->getPost());
        $item->id = $id;

        if (! $this->model->save($item))
        {
            return redirect()->withInput()->with('errors', $model->errors());
        }

        return redirect('{namespace}\{name}::listAll');
    }

    /**
     * Handles the POST request to delete an existing object.
     *
     * @param int The user id
     */
    public function delete(int $id)
    {
        if (! $this->model->delete($id))
        {
            session()->setFlashdata('error', $this->model->errors());
        }

        return redirect('{namespace}\{name}::listAll');
    }

}
