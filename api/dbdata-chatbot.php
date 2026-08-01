<?php
function getTrips($conn)
{

    $result = mysqli_query($conn, "
SELECT *
FROM tours
WHERE status=1
");

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

function getFlights($conn)
{

    $result = mysqli_query($conn, "
SELECT *
FROM flights
WHERE status=1
");

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

function getBuses($conn)
{

    $result = mysqli_query($conn, "
SELECT *
FROM buses
WHERE status=1
");

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

/**
 * Blog articles - only title/excerpt/category/tags/slug are included, NOT
 * the full article `content`. Blog posts can be long, and sending every
 * full article on every single chatbot message would bloat the prompt
 * (slower responses, higher API cost) for very little benefit - a
 * paraphrased summary is enough for the bot to recommend a relevant post
 * and point the user to it; the full article is one click away on the
 * actual blog page.
 */
function getBlogs($conn)
{
    $result = mysqli_query($conn, "
        SELECT b.title, b.slug, b.excerpt, b.tags, bc.name AS category
        FROM blogs b
        LEFT JOIN blog_categories bc ON b.category_id = bc.id
        WHERE b.status = 1
        ORDER BY b.created_at DESC
    ");

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

/**
 * Static site content (About, Services, Contact, Privacy Policy, Terms)
 * pulled from the site_content table instead of being hardcoded here, so
 * it can be edited later (e.g. from an admin panel page) without touching
 * this file or redeploying code.
 */
function getSiteContent($conn)
{
    $result = mysqli_query($conn, "
        SELECT content_key, title, content
        FROM site_content
        WHERE status = 1
    ");

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['content_key']] = [
            'title' => $row['title'],
            'content' => $row['content'],
        ];
    }

    return $data;
}

$websiteData = [

    "tours" => getTrips($conn),

    "flights" => getFlights($conn),

    "buses" => getBuses($conn),

    "blogs" => getBlogs($conn),

    "site_content" => getSiteContent($conn),

];


$context = json_encode($websiteData);
