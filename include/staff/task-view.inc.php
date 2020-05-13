<?php
global $cfg;

$id = $task->getId();
$dept = $task->getDept();
$thread = $task->getThread();

$iscloseable = $task->isCloseable();
$canClose = ($role->hasPerm(TaskModel::PERM_CLOSE) && $iscloseable === true);
$actions = array();
$object = $task->ticket;

if ($task->isOpen() && $role->hasPerm(Task::PERM_ASSIGN)) {

    if ($task->getStaffId() != $thisstaff->getId()
            && (!$dept->assignMembersOnly()
                || $dept->isMember($thisstaff))) {
        $actions += array(
                'claim' => array(
                    'href' => sprintf('#tasks/%d/claim', $task->getId()),
                    'icon' => 'icon-user',
                    'label' => __('Claim'),
                    'redirect' => 'tasks.php'
                ));
    }

    $actions += array(
            'assign/agents' => array(
                'href' => sprintf('#tasks/%d/assign/agents', $task->getId()),
                'icon' => 'icon-user',
                'label' => __('Assign to Agent'),
                'redirect' => 'tasks.php'
            ));

    $actions += array(
            'assign/teams' => array(
                'href' => sprintf('#tasks/%d/assign/teams', $task->getId()),
                'icon' => 'icon-user',
                'label' => __('Assign to Team'),
                'redirect' => 'tasks.php'
            ));
}

if ($role->hasPerm(Task::PERM_TRANSFER)) {
    $actions += array(
            'transfer' => array(
                'href' => sprintf('#tasks/%d/transfer', $task->getId()),
                'icon' => 'icon-share',
                'label' => __('Transfer'),
                'redirect' => 'tasks.php'
            ));
}

$actions += array(
        'print' => array(
            'href' => sprintf('tasks.php?id=%d&a=print', $task->getId()),
            'class' => 'no-pjax',
            'icon' => 'icon-print',
            'label' => __('Print')
        ));

if ($role->hasPerm(Task::PERM_EDIT)) {
    $actions += array(
            'edit' => array(
                'href' => sprintf('#tasks/%d/edit', $task->getId()),
                'icon' => 'icon-edit',
                'dialog' => '{"size":"large"}',
                'label' => __('Edit')
            ));
}

if ($role->hasPerm(Task::PERM_DELETE)) {
    $actions += array(
            'delete' => array(
                'href' => sprintf('#tasks/%d/delete', $task->getId()),
                'icon' => 'icon-trash',
                'class' => (strpos($_SERVER['REQUEST_URI'], 'tickets.php') !== false) ? 'danger' : 'red button',
                'label' => __('Delete'),
                'redirect' => 'tasks.php'
            ));
}

$info=($_POST && $errors)?Format::input($_POST):array();
$type = array('type' => 'viewed');
Signal::send('object.view', $task, $type);

if ($task->isOverdue())
    $warn.='&nbsp;&nbsp;<span class="Icon overdueTicket">'.__('Marked overdue!').'</span>';


$title = TaskForm::getInstance()->getField('title');
?>

