<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Fase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return 'Hola mundo';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        Auth::user()->comments()->create($request->only('comment', 'user_id', 'fase_id', 'quality_control_id', 'comment_id'));
        return redirect()->back()->with('comments', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }

    public function getCommentsByFase(Fase $fase)
    {
        $comments = $fase->comments()
            ->whereNull('comment_id')
            ->orderByDesc('created_at')->get();
        $breadcrumbsItems = [
            [
                'name' => $fase->qualityControl->name,
                'url' => route('qualityControls.details', $fase->qualityControl),
                'active' => false
            ],
            [
                'name' => 'Comentarios',
                'url' => route('comments.index'),
                'active' => true
            ],
        ];

        return view('comments.index', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => __("Comentarios"),
            "comments" => $comments
        ]);

    }
}
