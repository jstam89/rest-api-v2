<?php

header('Acces-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../config/Database.php';
include_once __DIR__ . '/../../models/post.php';

if ('POST' !== $_SERVER['REQUEST_METHOD']) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 405');
    echo json_encode(
        ['message' => 'Wrong request method, try GET method instead']
    );
} elseif ('POST' === $_SERVER['REQUEST_METHOD']) {
    // maak verbinding met de database en creeër post array
    $database = new Database();
    $db = $database->connect();
    $post = new Post($db);

    // verkrijg de raw geposte data
    $data = json_decode(file_get_contents('php://input'));

    // check of er data aanwezig is, zo niet geef een status code 406
    if (!$data) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 406');
        echo json_encode(
            ['message' => 'Check your syntax for errors']
        );

        return;
    }

    // definieer variabelen
    $post->title = $data->title;
    $post->body = $data->body;
    $post->author = $data->author;
    $post->category_id = $data->category_id;

    //check of alle variabelen zijn ingevuld !!!

    // maak de post en query deze in de database
    if ($post->create()) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 201');
        echo json_encode(
            ['message' => 'Post succesfull created']
        );
    } //wat als post niet create
}
