<?php

header('Acces-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__.'/../../config/Database.php';
include_once __DIR__.'/../../models/post.php';

if ('GET' === $_SERVER['REQUEST_METHOD']) {
    header($_SERVER['SERVER_PROTOCOL'].' 200');

    // maak verbinding met de database en creeër post array
    $database = new Database();
    $db = $database->connect();
    $post = new Post($db);

    // query de database voor posts en haal de resultaten op
    $result = $post->read();
    $num = $result->rowCount();

    // controleren of er posts aanwezig zijn in de database
    if ($num > 0) {
        $posts_arr = [];
        $posts_arr['data'] = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $post_item = [
                'id' => $id,
                'title' => $title,
                'body' => html_entity_decode($body),
                'author' => $author,
                'category_id' => $category_id,
                'category_name' => $category_name,
            ];

            // als er posts aanwezig zijn push deze naar array
            array_push($posts_arr['data'], $post_item);
        }

        // vertaal naar json en echo array
        echo json_encode($posts_arr);
    } else {
        // als er geen posts aanwezig zijn geef status code 204 No content
        header($_SERVER['SERVER_PROTOCOL'].' 204');
        echo json_encode(
            ['message' => 'No posts found in database']
        );
    }
}
// als er een andere methode dan GET wordt gebruikt, geef status code 405 Method not allowed!
elseif ('GET' !== $_SERVER['REQUEST_METHOD']) {
    header($_SERVER['SERVER_PROTOCOL'].' 405');
    echo json_encode(
        ['message' => 'Wrong request method, try GET method instead']
    );
}
