<?php
/**
 * Shared blog helper functions.
 * Included by both frontend (blogs.php, blog-details.php)
 * and admin (add-blog.php, edit-blog.php).
 */

/**
 * Turn a title into a clean, unique, URL/SEO friendly slug.
 * e.g. "Top 10 Treks in Nepal!" -> "top-10-treks-in-nepal"
 */
function generateSlug($string, $conn, $ignoreId = null)
{
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    if ($slug === '') {
        $slug = 'post';
    }

    $baseSlug = $slug;
    $i = 1;

    while (true) {
        $sql = "SELECT id FROM blogs WHERE slug = ?" . ($ignoreId ? " AND id != ?" : "");
        $stmt = $conn->prepare($sql);

        if ($ignoreId) {
            $stmt->bind_param("si", $slug, $ignoreId);
        } else {
            $stmt->bind_param("s", $slug);
        }

        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            break;
        }

        $slug = $baseSlug . '-' . $i;
        $i++;
    }

    return $slug;
}

/**
 * Approximate reading time based on word count (avg 200 wpm).
 */
function readingTime($content)
{
    $words = str_word_count(strip_tags($content));
    $minutes = max(1, ceil($words / 200));
    return $minutes . ' min read';
}

/**
 * Build a plain-text excerpt from HTML content if no manual excerpt is set.
 */
function autoExcerpt($content, $length = 160)
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($content)));
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '...';
}
