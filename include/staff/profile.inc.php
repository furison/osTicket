<?php

$avatar = $staff->getAvatar();
$pagelimit = $staff->max_page_size ?: $cfg->getPageSize();



$queues = CustomQueue::queues()
  ->filter(Q::any(array(
      'flags__hasbit' => CustomQueue::FLAG_PUBLIC,
      'staff_id' => $thisstaff->getId(),
  )))
  ->all();

$datetime_format = $staff->datetime_format;
 
$langs = Internationalization::getConfiguredSystemLanguages();

$options['from']=array(
                  'email' => __("Email Address Name"),
                  'dept' => sprintf(__("Department Name (%s)"),
                      __('if public' /* This is used in 'Department's Name (>if public<)' */)),
                  'mine' => __('My Name'),
                  '' => '— '.__('System Default').' —',
                );

$options['thread']=array(
        'desc' => __('Descending'),
        'asc' => __('Ascending'),
        '' => '— '.__('System Default').' —',
);

$options['signature']=array(
          'mine'=>__('My Signature'),
          'dept'=>sprintf(__('Department Signature (%s)'),
                       __('if set' /* This is used in 'Department Signature (>if set<)' */))
);

$options['reply_redir']=array(
        'Queue'=>__('Queue'),
        'Ticket'=>__('Ticket')
);

$options['img_att']=array(
        'download'=>__('Download'),
        'inline'=>__('Inline')
);
