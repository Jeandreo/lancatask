<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
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
        // RETURN VIEW WITH DATA
        return view('pages.account');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find(Auth::id()))
        return redirect()->back();

        // GET FORM DATA
        $data = $request->all();

        // Verifica se tem outro usuário com email
        $existEmail = User::where('email', $data['email'])->whereNot('id', Auth::id())->exists();

        if($existEmail){
            // REDIRECT AND MESSAGES
            return redirect()
                ->route('account')
                ->with('message', 'Já existe um usuário com esse email.');
        }

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
            $data['photo']->storeAs('users/photos', Auth::id() . '.jpg', 'public');
        }

        // REDIRECT AND MESSAGES
        return redirect()
            ->route('account.index')
            ->with('message', 'Conta atualizada.');

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
    public function sounds()
    {

        // GET DATA
        $content = User::find(Auth::id());
        $content->sounds = $content->sounds == true ? false : true;
        $content->save();

        // RETURN
        return response()->json('Success', 200);

    }

}
