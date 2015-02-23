<?php

namespace Backend\Modules\Blog\DataFixtures;

class LoadBlogPosts
{
    public function load(\SpoonDatabase $database)
    {
        $metaId = $database->insert(
            'meta',
            array(
                'keywords' => 'Lorem ipsum',
                'description' => 'Lorem ipsum',
                'title' => 'Lorem ipsum',
                'url' => 'lorem-ipsum',
            )
        );

        $categoryId = $database->getVar(
            'SELECT id
             FROM blog_categories
             WHERE title = :title AND language = :language
             LIMIT 1',
            array(
                'title' => 'Default',
                'language' => 'en',
            )
        );

        $database->insert(
            'blog_posts',
            array(
                'meta_id' => $metaId,
                'category_id' => $categoryId,
                'user_id' => 1,
                'language' => 'en',
                'title' => 'Lorem ipsum',
                'introduction' => '<p>Lorem ipsum dolor sit amet</p>',
                'text' => '<p>Lorem ipsum dolor sit amet</p>',
                'status' => 'active',
                'publish_on' => '2015-02-23 00:00:00',
                'created_on' => '2015-02-23 00:00:00',
                'edited_on' => '2015-02-23 00:00:00',
            )
        );
    }
}
