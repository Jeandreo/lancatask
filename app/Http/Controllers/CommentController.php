<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, Comment $content)
    {

        $this->request = $request;
        $this->repository = $content;

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

        // SEND DATA
        $this->repository->create($data);

        // REDIRECT AND MESSAGES
        return response()->json('', 200);

    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // GET ALL DATA
        $contents = $this->repository->where('task_id', $id)->where('status', 1)->orderBy('id', 'DESC')->get();

        // RETURN VIEW WITH DATA
        return view('pages.tasks._comments')->with([
            'contents' => $contents,
        ]);

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

        // REDIRECT AND MESSAGESS
        return response()->json($id, 200);

    }
}
