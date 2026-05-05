<?php

namespace App\Http\Controllers;

use App\Models\UserPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPositionController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, UserPosition $content)
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

        // Verify if user is admin
        if(!Auth::user()->canManage()) return redirect()->back();

        return view('pages.positions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Verify if user is admin
        if(!Auth::user()->canManage()) return redirect()->back();

        // RENDER VIEW
        return view('pages.positions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Verify if user is admin
        if(!Auth::user()->canManage()) return redirect()->back();

        // GET FORM DATA
        $data = $request->all();

        // Obtém projeto
        $data['created_by'] = Auth::id();

        // SEND DATA
        $this->repository->create($data);

        // REDIRECT AND MESSAGES
        return redirect()
                ->route('positions.index')
                ->with('message', 'Cargo adicionado com sucesso.');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Verify if user is admin
        if(!Auth::user()->canManage()) return redirect()->back();

        // GET ALL DATA
        $content = $this->repository->find($id);

        // VERIFY IF EXISTS
        if(!$content) return redirect()->back();

        // GENERATES DISPLAY WITH DATA
        return view('pages.positions.edit')->with([
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

        // Verify if user is admin
        if(!Auth::user()->canManage()) return redirect()->back();

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
            ->route('positions.index')
            ->with('message', 'Cargo atualizado com sucesso.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // Verify if user is admin
        if(!Auth::user()->canManage()) return redirect()->back();

        // GET DATA
        $content = $this->repository->find($id);
        $status = $content->status == true ? false : true;

        // STORING NEW DATA
        $this->repository->where('id', $id)->update(['status' => $status, 'updated_by' => Auth::id()]);

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('positions.index')
            ->with('message', 'Cargo ' . ($status == false ? 'desativado' : 'habilitado') . ' com sucesso.');

    }

    public function delete($id)
    {
        if(!Auth::user()->canManage()) return redirect()->back();

        $content = $this->repository->find($id);

        if (!$content) {
            return redirect()->route('positions.index')->with('message', 'Cargo não encontrado.');
        }

        $fallback = $this->repository->where('id', '!=', $id)->orderBy('id')->first();

        if (!$fallback) {
            return redirect()->route('positions.index')->with('message', 'Crie outro cargo antes de excluir este.');
        }

        DB::transaction(function () use ($content, $fallback) {
            User::where('position_id', $content->id)->update([
                'position_id' => $fallback->id,
                'updated_by' => Auth::id(),
            ]);

            $content->delete();
        });

        return redirect()->route('positions.index')->with('message', 'Cargo excluído com sucesso.');
    }
}
