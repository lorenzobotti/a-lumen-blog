SELECT posts.title, posts.content
FROM posts
JOIN posts_categories ON posts_categories.post_id = posts.id
JOIN favorite_categories ON posts_categories.category_id = favorite_categories.category_id
WHERE favorite_categories.user_id = $utenteLoggato