<?php

namespace App\Services\Posts;


use App\Interfaces\Posts\PostServiceInterface;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Share;
use App\Models\Shift;
use App\Repository\Posts\PostRepository;
use App\Statuses\UserTypes;
use Illuminate\Support\Facades\Auth;

class PostService implements PostServiceInterface
{

    public function __construct(private PostRepository $postRepository)
    {
    }

    public function create_post($data)
    {
        return $this->postRepository->create_post($data);
    }
    public function getPosts()
    {
        $posts = Post::query()->where('company_id', auth()->user()->company_id);
        return  $posts->with(['user', 'comments.user', 'comments.post', 'likes.user', 'shares.user'])->paginate();
    }
    public function getMyPosts()
    {
        return  $this->postRepository->getMyPosts();
    }


    public function show($id)
    {
        $post = $this->postRepository->getById($id)->load(['user', 'comments.user', 'comments.post', 'likes.user', 'shares.user']);
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR  || auth()->user()->type == UserTypes::EMPLOYEE && auth()->user()->company_id == $post->company_id) {
            return ['success' => true, 'data' => $post];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function create_comment($data)
    {

        return $this->postRepository->create_comment($data);
    }

    public function add_like($data)
    {
        return $this->postRepository->add_like($data);
    }
    public function add_like_comment($data)
    {
        return $this->postRepository->add_like_comment($data);
    }



    public function share_post($data)
    {
        return $this->postRepository->share_post($data);
    }



    public static function increasePostCommentsCount($id)
    {
        $post = Post::find($id);
        $post->comments_count += 1;
        $post->save();
    }


    public static function increasePostLikesCount($id)
    {
        $post = Post::find($id);
        $post->likes_count += 1;
        $post->save();
    }
    public static function increaseCommentLikesCount($id)
    {
        $comment = Comment::find($id);
        $comment->likes_count += 1;
        $comment->save();
    }

    public static function decreasePostLikesCount($id)
    {
        $post = Post::find($id);
        $post->likes_count -= 1;
        $post->save();
    }

    public static function decreaseCommentLikesCount($id)
    {
        $comment = Comment::find($id);
        $comment->likes_count -= 1;
        $comment->save();
    }



    public static function increasePostSharesCount($id)
    {
        $post = Post::find($id);
        $post->shares_count += 1;
        $post->save();
    }

    public static function decreasePostSharesCount($id)
    {
        $post = Post::find($id);
        $post->shares_count -= 1;
        $post->save();
    }

    public function deletePost(int $id)

    {
        $post = $this->postRepository->getById($id);
        if ($post->user_id == auth()->user()->id || auth()->user()->type == UserTypes::ADMIN) {
            return $this->postRepository->deleteById($id);
        } else {
            return "Unauthorized";
        }
    }

    public function deleteComment(int $id)

    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id == auth()->user()->id || auth()->user()->type == UserTypes::ADMIN) {

            $post = Post::where('id', $comment->post_id)->first();
            $post->comments_count -= 1;
            $post->save();
            return  Comment::destroy($id);
        } else {
            return "Unauthorized";
        }
    }


    public static function isLiked($post_id)
    {
        $post = Post::find($post_id);
        $like = Like::where('post_id', $post->id)->where('user_id', auth()->user()->id)->first();
        if ($like) {
            return true;
        } else {
            return false;
        }
    }
    public static function isLikedComment($comment_id)
    {
        $comment = Comment::find($comment_id);
        $like = Like::where('comment_id', $comment->id)->where('user_id', auth()->user()->id)->first();
        if ($like) {
            return true;
        } else {
            return false;
        }
    }
}
