<?php

namespace App\Repositories\Admin\Posts\Repository;

use App\Models\CategoryPost;
use App\Models\Image;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use App\Models\Tag;
use App\Repositories\Admin\Posts\Interface\PostInterface;
use Illuminate\Support\Str;

class PostRepository implements PostInterface
{
    protected $post;
    protected $postCategory;

    protected $Images;

    protected $tag;
    protected $postTag;
    protected $categoryPost;

    protected $image;

    const PAGINATION = 10;

    public function __construct(
        Post                $post,
        PostCategory        $postCategory,
        Image               $Images,
        Tag                 $tag,
        PostTag             $postTag,
        CategoryPost        $categoryPost,
        Image               $image
    ) {
        $this->post = $post;

        $this->postCategory = $postCategory;

        $this->Images = $Images;

        $this->tag = $tag;

        $this->postTag = $postTag;

        $this->categoryPost = $categoryPost;

        $this->image = $image;
    }

    public function allFillter($request)
    {
        $query = $this->post->newQuery();
        $categories = $request->categories ?? [];
        $hot = null;
        $active = null;
        $name = null;
        $typeOrder = null;
        $postIds = [];

        // Lọc theo tên bài viết
        if ($request->has('name') && $request->input('name') !== null) {
            $name = $request->input('name');
            $query->where('name', 'like', '%' . $name . '%');
        }
        if (!empty($categories)) {
            $postIds = $this->postCategory
                ->whereIn('category_id', $categories)
                ->groupBy('post_id')
                ->havingRaw('COUNT(DISTINCT category_id) = ?', [count($categories)])
                ->pluck('post_id')
                ->toArray();
            $query->whereIn('id', $postIds);
        }

        // Lọc theo bài viết nổi bật
        switch ($request->fill_action) {
            case 'hot':
                $query->where('hot', 1);
                $hot = 1;
                break;
            case 'noHot':
                $hot = 0;
                $query->where('hot', 0);
                break;
            case 'active':
                $active = 1;
                $query->where('active', 1);
                break;
            case 'noActive':
                $active = 0;
                $query->where('active', 0);
                break;
            default:
                $query->orderBy('order');
                break;
        }
        // Sắp xếp bài viết theo tiêu chí
        $typeOrder = $request->order_with;
        if (!empty($typeOrder)) {
            switch ($typeOrder) {
                case 'dateASC':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'dateDESC':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'viewASC':
                    $query->orderBy('view', 'asc');
                    break;
                case 'viewDESC':
                    $query->orderBy('view', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // Sắp xếp mặc định nếu không có order_with
            $query->orderBy('created_at', 'desc');
        }

        // Lấy dữ liệu sau khi lọc và sắp xếp
        $data = $query->paginate(self::PAGINATION);

        $data = $data->appends([
            'name' => $name,
            'typeOrder' => $request->order_with,
            'categories' => $categories,
        ]);

        // Xử lý các điều kiện fill_action - order_with với switch case - phân trang
        switch (true) {
            case $hot === 1:
                $data = $data->appends('fill_action', 'hot');
                break;
            case $hot === 0:
                $data = $data->appends('fill_action', 'noHot');
                break;
            case $active === 1:
                $data = $data->appends('fill_action', 'active');
                break;
            case $active === 0:
                $data = $data->appends('fill_action', 'noActive');
                break;
            case $typeOrder == 'dateASC':
                $data = $data->appends('order_with', 'dateASC');
                break;
            case $typeOrder == 'dateDESC':
                $data = $data->appends('order_with', 'dateDESC');
                break;
            case $typeOrder == 'viewASC':
                $data = $data->appends('order_with', 'viewASC');
                break;
            case $typeOrder == 'viewDESC':
                $data = $data->appends('order_with', 'viewDESC');
                break;
        }
        return [
            'data' => $data,
            'hot' => $hot,
            'active' => $active,
            'name' => $name,
            'typeOrder' => $typeOrder,
            'categories' => $categories
        ];
    }

    public function getListCategoryPost()
    {
        $categoryPost = $this->categoryPost
            ->where('parent_id', 0)
            ->get();

        return $categoryPost;
    }

    public function listTags()
    {
        $tags = $this->tag->select(
            'id',
            'name',
        )->orderBy('created_at', 'desc')->get();
        return $tags;
    }

    public function createPost($data)
    {
        return $this->post->create($data);
    }

    public function checkExitsTags($tagName)
    {
        return $this->tag->firstOrCreate(
            ['slug' => Str::slug($tagName)], // Điều kiện kiểm tra trùng lặp
            ['name' => $tagName], // Dữ liệu sẽ được lưu nếu không trùng lặp
        );
    }

    public function deleteRecordPostTagByPost($postId, $tagsToRemove)
    {
        return $this->postTag->where('post_id', $postId)
            ->whereIn('tag_id', $tagsToRemove)
            ->delete();
    }

    public function attachTagIfNotExists($record, $tagId)
    {
        return $record->tags()->attach($tagId, [
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function updatePost($data, $id)
    {
        $post = $this->post->find($id);

        $post->update($data);

        return $post;
    }

    public function updateAllRecordPostTagByPost($tagId)
    {
        return $this->postTag->where('tag_id', $tagId)
            ->onlyTrashed()
            ->update(['deleted_at' => NULL]);
    }

    public function getAllRecordBySoftDeleted($postId, $tagIds)
    {
        $postTag = $this->postTag
            ->where('post_id', $postId)
            ->whereIn('tag_id', $tagIds)
            ->onlyTrashed()
            ->get();
        return $postTag;
    }

    public function getPostById($id)
    {
        $postEdit = $this->post->find($id);

        return $postEdit;
    }

    public function categoryOfPost($id)
    {
        $data = [];

        $cate = $this->post->find($id);

        $data = $cate->postCategories->pluck('category.id')->all();

        return $data;
    }

    public function tagsSelected($id)
    {
        $tags = $this->postTag
            ->where('post_id', $id)
            ->whereNull('deleted_at')
            ->pluck('tag_id'); // Lấy các tag chưa bị xóa

        return $tags;
    }

    // Hàm xóa các danh mục theo bài viết không còn trong request
    public function deleteCategoryPostByPost($record, $existingCategoryId)
    {
        return $record->postCategories()->where('category_id', $existingCategoryId)->delete();
    }

    public function checkExitsCategoryPost($record, $categoryId)
    {
        return $record->postCategories()
            ->withTrashed()
            ->where('category_id', $categoryId)
            ->first();
    }

    public function createCategoryPost($record, $data)
    {
        return $record->postCategories()->create($data);
    }

    public function createRelatedPhotoPost($record, $data)
    {
        return $record->images()->create($data);
    }

    public function getImageRelatedPhotoById($id)
    {
        return $this->image->find($id);
    }

    public function delete($id)
    {
        $post = $this->post->find($id);

        if (!$post) {
            return redirect()->route('admin.posts.index')->with([
                'status_failed' => "Không tìm thấy bài viết"
            ]);
        }

        if ($post) {

            $post->postCategories()->delete();
            // xóa ảnh liên quan
            foreach ($post->images as $image) {
                $image->delete();
            }
            $tags = $this->postTag->where('post_id', $post->id);

            $tags->delete();
            $post->delete();
        }

        return true;
    }
}
