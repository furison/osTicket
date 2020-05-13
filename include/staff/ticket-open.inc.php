<?php

$info=array();
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

if ($_SESSION[':form-data'] && !$_GET['tid'])
  unset($_SESSION[':form-data']);

//  Use thread entry to seed the ticket
if (!$user && $_GET['tid'] && ($entry = ThreadEntry::lookup($_GET['tid']))) {
    if ($entry->getThread()->getObjectType() == 'T')
      $oldTicketId = $entry->getThread()->getObjectId();
    if ($entry->getThread()->getObjectType() == 'A')
      $oldTaskId = $entry->getThread()->getObjectId();

    $_SESSION[':form-data']['message'] = Format::htmlchars($entry->getBody());
    $_SESSION[':form-data']['ticketId'] = $oldTicketId;
    $_SESSION[':form-data']['taskId'] = $oldTaskId;
    $_SESSION[':form-data']['eid'] = $entry->getId();
    $_SESSION[':form-data']['timestamp'] = $entry->getCreateDate();

    if ($entry->user_id)
       $user = User::lookup($entry->user_id);

     if (($m= TicketForm::getInstance()->getField('message'))) {
         $k = 'attach:'.$m->getId();
         unset($_SESSION[':form-data'][$k]);
        foreach ($entry->getAttachments() as $a) {
          if (!$a->inline && $a->file) {
            $_SESSION[':form-data'][$k][$a->file->getId()] = $a->getFilename();
            $_SESSION[':uploadedFiles'][$a->file->getId()] = $a->getFilename();
          }
        }
     }
}

if (!$info['topicId'])
    $info['topicId'] = $cfg->getDefaultTopicId();

$forms = array();
if ($info['topicId'] && ($topic=Topic::lookup($info['topicId']))) {
    foreach ($topic->getForms() as $F) {
        if (!$F->hasAnyVisibleFields())
            continue;
        if ($_POST) {
            $F = $F->instanciate();
            $F->isValidForClient();
        }
        $forms[] = $F;
    }
}

if ($_POST)
    $info['duedate'] = Format::date(strtotime($info['duedate']), false, false, 'UTC');
?>