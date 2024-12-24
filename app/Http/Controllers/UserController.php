<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, User $content)
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

        // GET ALL DATA
        $contents = $this->repository->orderBy('name', 'ASC')->get();

        // RETURN VIEW WITH DATA
        return view('pages.users.index')->with([
            'contents' => $contents,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // Obtém dados
        $positions = UserPosition::where('status', true)->get();

        // RENDER VIEW
        return view('pages.users.create')->with([
            'positions' => $positions,
        ]);
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

        // CREATED BY
        $data['created_by'] = Auth::id();
        $data['password'] = Hash::make($data['password']);

        // SEND DATA
        $created = $this->repository->create($data);

        // Salva foto
        if (isset($data['photo']) && $data['photo']->isValid()) {
            $data['photo']->storeAs('users/photos', $created->id . '.jpg', 'public');
        }

        // REDIRECT AND MESSAGES
        return redirect()
                ->route('users.index')
                ->with('message', 'Usuário adicionado com sucesso.');

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

        // Obtém dados
        $positions = UserPosition::where('status', true)->get();

        // GENERATES DISPLAY WITH DATA
        return view('pages.users.edit')->with([
            'content' => $content,
            'positions' => $positions,
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

        // Remove senha caso venha vazia
        if(!$data['password']){
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        // STORING NEW DATA
        $content->update($data);

        // Salva foto
        if (isset($data['photo']) && $data['photo']->isValid()) {
            $data['photo']->storeAs('users/photos', $id . '.jpg', 'public');
        }

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('users.index')
            ->with('message', 'Usuário editado com sucesso.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sidebar()
    {

        // GET DATA
        $content = User::find(Auth::id());
        $openOrClose = $content->sidebar == true ? false : true;
        $content->sidebar = $openOrClose;
        $content->save();

        // RETURN
        return response()->json('Success', 200);

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
            ->route('users.index')
            ->with('message', 'Usuário ' . ($status == false ? 'desativado' : 'habiliitado') . ' com sucesso.');

    }
}
