<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class BlogRepository extends Repository
{
    public static function model()
    {
        return Blog::class;
    }

    public static function storeByRequest(BlogRequest $request): Blog
    {
        $thumbnail = MediaRepository::storeByRequest($request->file('thumbnail'), 'blogs', 'image');

        $description = ProductRepository::sanitizeUnicode($request->description);
        $description = mb_convert_encoding($description, 'HTML-ENTITIES', 'UTF-8');
        $description = Purifier::clean($description);

        $title = ProductRepository::sanitizeUnicode($request->title);

        $blog = self::create([
            'user_id' => auth()->id(),
            'title' => $title,
            'category_id' => $request->category,
            'description' => $description,
            'media_id' => $thumbnail?->id,
        ]);

        foreach ($request->tags ?? [] as $tag) {
            $tag = Tag::firstOrCreate([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);

            $blog->tags()->attach($tag->id);
        }

        return $blog;
    }

    public static function updateByRequest(BlogRequest $request, Blog $blog): Blog
    {
        $thumbnail = self::thumbnailUpdateOrCreate($blog->media, $request);

        $description = ProductRepository::sanitizeUnicode($request->description);
        $description = mb_convert_encoding($description, 'HTML-ENTITIES', 'UTF-8');

        $title = ProductRepository::sanitizeUnicode($request->title);

        $blog->update([
            'title' => $title,
            'category_id' => $request->category,
            'description' => $description,
            'media_id' => $thumbnail?->id,
        ]);

        $blog->tags()->detach();

        foreach ($request->tags ?? [] as $tag) {
            $tag = Tag::firstOrCreate([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);

            $blog->tags()->attach($tag->id);
        }

        return $blog;
    }

    private static function thumbnailUpdateOrCreate($media, $request)
    {
        $thumbnail = $media;
        if ($request->hasFile('thumbnail') && $media) {
            $thumbnail = MediaRepository::updateByRequest($request->thumbnail, 'blogs', 'image', $media);

            return $thumbnail;
        }

        if ($request->hasFile('thumbnail') && ! $media) {
            $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'blogs', 'image');
        }

        return $thumbnail;
    }
}
