<?php
global $cfg;

if (!$info['title'])
    $info['title'] = sprintf(__('%s Task #%s'),
            __('Edit'), $task->getNumber()
            );

$action = $info['action'] ?: ('#tasks/'.$task->getId().'/edit');

$namespace = sprintf('task.%d.edit', $task->getId());
