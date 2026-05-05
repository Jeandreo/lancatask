<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectTypeController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, ProjectType $content)
    {

        $this->request = $request;
        $this->repository = $content;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.types.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // RENDER VIEW
        return view('pages.types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // GET FORM DATA
        $data = $request->all();

        // Obtém projeto
        $data['created_by'] = Auth::id();

        // SEND DATA
        $this->repository->create($data);

        // REDIRECT AND MESSAGES
        return redirect()
                ->route('projects.types.index')
                ->with('message', 'Tipo de projeto adicionado com sucesso.');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // GET ALL DATA
        $content = $this->repository->find($id);

        // VERIFY IF EXISTS
        if(!$content) return redirect()->back();

        // GENERATES DISPLAY WITH DATA
        return view('pages.types.edit')->with([
            'content' => $content,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id))
        return redirect()->back();

        // GET FORM DATA
        $data = $request->all();

        // UPDATE BY
        $data['updated_by'] = Auth::id();

        // STORING NEW DATA
        $content->update($data);

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('projects.types.index')
            ->with('message', 'Tipo de projeto atualizado com sucesso.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // GET DATA
        $content = $this->repository->find($id);
        $status = $content->status == true ? false : true;

        // STORING NEW DATA
        $this->repository->where('id', $id)->update(['status' => $status, 'updated_by' => Auth::id()]);

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('projects.types.index')
            ->with('message', 'Tipo de projeto ' . ($status == false ? 'desativado' : 'habilitado') . ' com sucesso.');

    }

    public function delete($id)
    {
        $content = $this->repository->find($id);

        if (!$content) {
            return redirect()->route('projects.types.index')->with('message', 'Tipo de projeto não encontrado.');
        }

        $fallbackType = $this->repository->where('id', '!=', $id)->orderBy('id')->first();

        if (!$fallbackType) {
            return redirect()->route('projects.types.index')->with('message', 'Crie outro tipo de projeto antes de excluir este.');
        }

        DB::transaction(function () use ($content, $fallbackType) {
            Project::where('type_id', $content->id)->update([
                'type_id' => $fallbackType->id,
                'updated_by' => Auth::id(),
            ]);

            $content->delete();
        });

        return redirect()->route('projects.types.index')->with('message', 'Tipo de projeto excluído com sucesso.');
    }
}
