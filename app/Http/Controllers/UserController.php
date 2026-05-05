<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Verifica se o usuário é administrador
        if(!Auth::user()->canManage()) return redirect()->back();

        return view('pages.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // Verifica se o usuário é administrador
        if(!Auth::user()->canManage()) return redirect()->back();

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

        // Email
        if($this->repository->where('email', $data['email'])->exists()) return redirect()->back()->with('message', 'Email já cadastrado.') ;

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

        // Verifica se o usuário é administrador
        if(!Auth::user()->canManage()) return redirect()->back();

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

        // Verifica se o usuário é administrador
        if(!Auth::user()->canManage()) return redirect()->back();

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id))
        return redirect()->back();

        // GET FORM DATA
        $data = $request->all();

        // UPDATE BY
        $data['updated_by'] = Auth::id();

        if($this->repository->where('email', $data['email'])->where('id', '!=', $id)->exists()) return redirect()->back()->with('message', 'Email já cadastrado.') ;

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
    public function destroy($id)
    {

        // Verifica se o usuário é administrador
        if(!Auth::user()->canManage()) return redirect()->back();

        // GET DATA
        $content = $this->repository->find($id);
        $status = $content->status == true ? false : true;

        // STORING NEW DATA
        $this->repository->where('id', $id)->update(['status' => $status, 'updated_by' => Auth::id()]);

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('users.index')
            ->with('message', 'Usuário ' . ($status == false ? 'desativado' : 'habilitado') . ' com sucesso.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {

        // Verifica se o usuário é administrador
        if(!Auth::user()->canManage()) return redirect()->back();

        if ((int) Auth::id() === (int) $id) {
            return redirect()->route('users.index')->with('message', 'Você não pode excluir o próprio usuário.');
        }

        $content = $this->repository->find($id);

        if (!$content) {
            return redirect()->route('users.index')->with('message', 'Usuário não encontrado.');
        }

        $fallbackUser = $this->repository->where('id', '!=', $id)->orderBy('id')->first();

        if (!$fallbackUser) {
            return redirect()->route('users.index')->with('message', 'Não foi possível excluir: precisa existir outro usuário no sistema.');
        }

        DB::transaction(function () use ($content, $fallbackUser) {
            $userId = $content->id;
            $replaceId = $fallbackUser->id;

            DB::table('users')->where('created_by', $userId)->update(['created_by' => $replaceId]);
            DB::table('users')->where('updated_by', $userId)->update(['updated_by' => $replaceId]);
            DB::table('users')->where('filed_by', $userId)->update(['filed_by' => $replaceId]);

            foreach (['projects', 'modules', 'statuses', 'tasks', 'comments', 'contracts', 'clients', 'users_positions', 'projects_types'] as $table) {
                DB::table($table)->where('created_by', $userId)->update(['created_by' => $replaceId]);
                DB::table($table)->where('updated_by', $userId)->update(['updated_by' => $replaceId]);
                DB::table($table)->where('filed_by', $userId)->update(['filed_by' => $replaceId]);
            }

            DB::table('tasks_historics')->where('created_by', $userId)->update(['created_by' => $replaceId]);
            DB::table('user_preferrences')->where('created_by', $userId)->update(['created_by' => $replaceId]);
            DB::table('agendas')->where('created_by', $userId)->update(['created_by' => $replaceId]);
            DB::table('agendas')->where('updated_by', $userId)->update(['updated_by' => $replaceId]);

            DB::table('projects_users')->where('user_id', $userId)->delete();
            DB::table('tasks_participants')->where('user_id', $userId)->delete();
            DB::table('modules_order')->where('user_id', $userId)->delete();
            DB::table('agendas_member')->where('type', 'user')->where('member_id', $userId)->delete();
            DB::table('sessions')->where('user_id', $userId)->delete();

            $content->delete();
        });

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('users.index')
            ->with('message', 'Usuário excluído com sucesso.');

    }
}
