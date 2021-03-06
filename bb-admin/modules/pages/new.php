<?php
if(!defined('BB-ADMIN')) die('This file cannot be accessed directly.');

// load the database library
lib('database');

$message = null;

if($_POST) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $errors = array();

    if(empty($title)) {
        $errors[] = __('admin.pages.errors.title');
    }

    if(empty($content)) {
        $errors[] = __('admin.pages.errors.content');
    }

    if(count($errors) == 0) {
        $data = array(
            'title' => $title,
            'content' => $content,
            'created_at' => time(),
            'updated_at' => time(),
        );
        $db = new Database();
        $db->table('pages')->insert($data);
        $message = __('admin.pages.new_success');
    }
}

tpl(__DIR__ . '/tpl/new.tpl.php', array(
    'message' => $message,
));
