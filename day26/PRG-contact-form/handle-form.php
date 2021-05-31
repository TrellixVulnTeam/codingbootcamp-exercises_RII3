<?php

// make sure we have functions for database access
require_once 'DBBlackboxV2.php';

// start the session (so that we can use $_SESSION)
session_start();

// determine if we are creating a new contact message
// or editing an existing one
if (isset($_GET['id'])) {
    // we are editing
    $contact_message_id = $_GET['id'];
    $is_edit = true;
} else {
    // we are creating
    $contact_message_id = null;
    $is_edit = false;
}

if ($is_edit) {
    // we want to work with data from the database

    $contact_message = find($contact_message_id);

} else {
    // we want to work with new, empty, default data

    // new, empty, default contact message:
    $contact_message = [
        'subject' => null,
        'text' => null,
        'is_robot' => 1,
        'topic' => null,
        'newsletter' => 0,
        'response' => 'none'
    ];
}

// validate the incoming data
$valid = true;
$error_messages = [];

if (empty($_POST['subject'])) {
    $valid = false;
    $error_messages[] = 'The subject field is mandatory';
}

if (strlen($_POST['subject']) < 5) {
    $valid = false;
    $error_messages[] = 'The subject is too short';
}

if (false !== strpos($_POST['subject'], 'damn')) {
    $valid = false;
    $error_messages[] = 'No swearing in the subject field!';
}

if (!$valid) {

    // pass the error messages on to the next request
    $_SESSION['error_messages'] = $error_messages;

    // pass the submitted data on to the next request
    // so that I can show the user his errors
    $_SESSION['flashed_data'] = $_POST;

    header('Location: display-form.php' . ($is_edit ? '?id='.$contact_message_id : '') );

    exit();
}


// update it with whatever came in the request: $_POST
$contact_message['subject']     = $_POST['subject'] ?? $contact_message['subject'];
$contact_message['text']        = $_POST['text'] ?? $contact_message['text'];
$contact_message['is_robot']    = $_POST['is_robot'] ?? $contact_message['is_robot'];
$contact_message['topic']       = $_POST['topic'] ?? $contact_message['topic'];
$contact_message['newsletter']  = $_POST['newsletter'] ?? $contact_message['newsletter'];
$contact_message['response']    = $_POST['response'] ?? $contact_message['response'];

// same as one of the lines above
if (isset($_POST['subject'])) {
    $contact_message['subject'] = $_POST['subject'];
} else {
    $contact_message['subject'] = $contact_message['subject'];
}

// save the contact message into the database
if ($is_edit) {
    // updating an existing record
    update($contact_message_id, $contact_message);

} else {
    // creating a new record
    $contact_message_id = insert($contact_message);

}

// $contact_message_id contains the id of the current message
// no matter if it was created or updated


$success_message = 'The contact message was successfully saved';
$_SESSION['success_message'] = $success_message;
// the message will stay in $_SESSION['success_message'] until deleted (or until session expires)

// redirect to somewhere else: e.g. display-form.php?id=3
header('Location: display-form.php?id='.$contact_message_id);

// no HTML code anywhere