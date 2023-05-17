<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Profile;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:dashboard');
    }

    public function user()
    {
        $user = Auth::user();
        
        $profile = new Profile();
        $profile->setAttribute('id', $user->getAttribute('id'));
        $profile->setAttribute('name', $user->getAttribute('name'));
        $profile->setAttribute('picture', $user->getAttribute('picture'));

        return response()->json(['user' => $profile]);
    }

    public function posts()
    {
        $user = Auth::user();
        $user_id = $user->getAttribute('id');
        $posts = Post::select(
            'posts.id',
            'user_id',
            'title',
            'comment',
            'priority',
            'posts.created_at',
            'posts.updated_at',
            'name as author',
            'picture as author_picture')
            ->where('user_id', $user_id)
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->orderBy('priority', 'asc')->get();

        foreach($posts as $post)
        {
            $post->user_id = (int)$post->user_id;
            $post->priority = (int)$post->priority;
            $profile = $post->user;
        }

        return response()->json(['posts' => $posts]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => ['required', 'regex:/^(?=.*[a-zA-Z])[^\-_ ][a-zA-Z0-9 \-_]+[^\-_ ]$/'],
            'comment' => ['required', 'regex:/^(?=.*[a-zA-Z])[^\-_ ][a-zA-Z0-9 \-_]+[^\-_ ]$/'],
            'priority' => ['required', 'integer', 'digits_between:1,3']
        ]);

        $user = Auth::user();

        $data = $request->all();

        $title = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $data['title']);
        $comment = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $data['comment']);
        $priority = preg_replace('/[^1-3]/', '', $data['priority']);
        $post = new Post();
        $post->user_id = $user->getAttribute('id');;
        $post->title = $title;
        $post->comment = $comment;
        $post->priority = $priority;

        $post->save();

        return response()->json();
    }

    public function redirectDashboard()
    {
        return redirect()->route('dashboard');
    }

}
