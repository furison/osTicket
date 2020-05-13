<?php
if (!defined('OSTSCPINC') || !$thisstaff
|| !$thisstaff->hasPerm(Ticket::PERM_CREATE, false))
die('Access Denied');
?>
<form action="tickets.php?a=open" method="post" class="save"  enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="create">
 <input type="hidden" name="a" value="open">
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="pull-left flush-left">
        <h2><?= __('Open a New Ticket');?></h2>
    </div>
</div>
 <table class="form_table fixed" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
    <!-- This looks empty - but beware, with fixed table layout, the user
         agent will usually only consult the cells in the first row to
         construct the column widths of the entire toable. Therefore, the
         first row needs to have two cells -->
        <tr><td style="padding:0;"></td><td style="padding:0;"></td></tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?= __('User and Collaborators'); ?></strong>: </em>
                <div class="error"><?= $errors['user']; ?></div>
            </th>
        </tr>
        <tr>
          <td>
            <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
              <?php
              if ($user) { ?>
                  <tr><td><?= __('User'); ?>:</td><td>
                    <div id="user-info">
                      <input type="hidden" name="uid" id="uid" value="<?= $user->getId(); ?>" />
                      <a href="#" onclick="javascript:
                      $.userLookup('ajax.php/users/<?= $user->getId(); ?>/edit',
                      function (user) {
                        $('#user-name').text(user.name);
                        $('#user-email').text(user.email);
                      });
                      return false;
                      "><i class="icon-user"></i>
                      <span id="user-name"><?= Format::htmlchars($user->getName()); ?></span>
                      &lt;<span id="user-email"><?= $user->getEmail(); ?></span>&gt;
                    </a>
                    <a class="inline button" style="overflow:inherit" href="#"
                    onclick="javascript:
                    $.userLookup('ajax.php/users/select/'+$('input#uid').val(),
                    function(user) {
                      $('input#uid').val(user.id);
                      $('#user-name').text(user.name);
                      $('#user-email').text('<'+user.email+'>');
                    });
                    return false;
                    "><i class="icon-retweet"></i> <?= __('Change'); ?></a>
                  </div>
                </td>
              </tr>
              <?php
            } else { //Fallback: Just ask for email and name
              ?>
              <tr id="userRow">
                <td width="120"><?= __('User'); ?>:</td>
                <td>
                  <span>
                    <select class="userSelection" name="name" id="user-name"
                    data-placeholder="<?= __('Select User'); ?>">
                  </select>
                </span>

                <a class="inline button" style="overflow:inherit" href="#"
                onclick="javascript:
                $.userLookup('ajax.php/users/lookup/form', function (user) {
                  var newUser = new Option(user.email + ' - ' + user.name, user.id, true, true);
                  return $(&quot;#user-name&quot;).append(newUser).trigger('change');
                });
                return false;
                "><i class="icon-plus"></i> <?= __('Add New'); ?></a>

                <span class="error">*</span>
                <br/><span class="error"><?= $errors['name']; ?></span>
              </td>
              <div>
                <input type="hidden" size=45 name="email" id="user-email" class="attached"
                placeholder="<?= __('User Email'); ?>"
                autocomplete="off" autocorrect="off" value="<?= $info['email']; ?>" />
              </div>
            </tr>
            <?php
          } ?>
          <tr id="ccRow">
            <td width="160"><?= __('Cc'); ?>:</td>
            <td>
              <span>
                <select class="collabSelections" name="ccs[]" id="cc_users_open" multiple="multiple"
                ref="tags" data-placeholder="<?= __('Select Contacts'); ?>">
              </select>
            </span>

            <a class="inline button" style="overflow:inherit" href="#"
            onclick="javascript:
            $.userLookup('ajax.php/users/lookup/form', function (user) {
              var newUser = new Option(user.name, user.id, true, true);
              return $(&quot;#cc_users_open&quot;).append(newUser).trigger('change');
            });
            return false;
            "><i class="icon-plus"></i> <?= __('Add New'); ?></a>

            <br/><span class="error"><?= $errors['ccs']; ?></span>
          </td>
        </tr>
        <?php
        if ($cfg->notifyONNewStaffTicket()) {
         ?>
        <tr class="no_border">
          <td>
            <?= __('Ticket Notice');?>:
          </td>
          <td>
            <select id="reply-to" name="reply-to">
              <option value="all"><?= __('Alert All'); ?></option>
              <option value="user"><?= __('Alert to User'); ?></option>
              <option value="none">&mdash; <?= __('Do Not Send Alert'); ?> &mdash;</option>
            </select>
          </td>
        </tr>
      <?php } ?>
    </table>
          </td>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?= __('Ticket Information and Options');?></strong>:</em>
            </th>
        </tr>
        <tr>
            <td width="160" class="required">
                <?= __('Ticket Source');?>:
            </td>
            <td>
                <select name="source">
                    <?php
                    $source = $info['source'] ?: 'Phone';
                    $sources = Ticket::getSources();
                    unset($sources['Web'], $sources['API']);
                    foreach ($sources as $k => $v)
                        echo sprintf('<option value="%s" %s>%s</option>',
                                $k,
                                ($source == $k ) ? 'selected="selected"' : '',
                                $v);
                    ?>
                </select>
                &nbsp;<font class="error"><b>*</b>&nbsp;<?= $errors['source']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160" class="required">
                <?= __('Help Topic'); ?>:
            </td>
            <td>
                <select name="topicId" onchange="javascript:
                        var data = $(':input[name]', '#dynamic-form').serialize();
                        $.ajax(
                          'ajax.php/form/help-topic/' + this.value,
                          {
                            data: data,
                            dataType: 'json',
                            success: function(json) {
                              $('#dynamic-form').empty().append(json.html);
                              $(document.head).append(json.media);
                            }
                          });">
                    <?php
                    if ($topics=Topic::getHelpTopics(false, false, true)) {
                        if (count($topics) == 1)
                            $selected = 'selected="selected"';
                        else { ?>
                        <option value="" selected >&mdash; <?= __('Select Help Topic'); ?> &mdash;</option>
<?php                   }
                        foreach($topics as $id =>$name) {
                            echo sprintf('<option value="%d" %s %s>%s</option>',
                                $id, ($info['topicId']==$id)?'selected="selected"':'',
                                $selected, $name);
                        }
                        if (count($topics) == 1 && !$forms) {
                            if (($T = Topic::lookup($id)))
                                $forms =  $T->getForms();
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error"><b>*</b>&nbsp;<?= $errors['topicId']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160">
                <?= __('Department'); ?>:
            </td>
            <td>
                <select name="deptId">
                    <option value="" selected >&mdash; <?= __('Select Department'); ?>&mdash;</option>
                    <?php
                    if($depts=Dept::getPublicDepartments()) {
                        foreach($depts as $id =>$name) {
                            if (!($role = $thisstaff->getRole($id))
                                || !$role->hasPerm(Ticket::PERM_CREATE)
                            ) {
                                // No access to create tickets in this dept
                                continue;
                            }
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['deptId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error"><?= $errors['deptId']; ?></font>
            </td>
        </tr>

         <tr>
            <td width="160">
                <?= __('SLA Plan');?>:
            </td>
            <td>
                <select name="slaId">
                    <option value="0" selected="selected" >&mdash; <?= __('System Default');?> &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['slaId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error">&nbsp;<?= $errors['slaId']; ?></font>
            </td>
         </tr>

         <tr>
            <td width="160">
                <?= __('Due Date');?>:
            </td>
            <td>
                <?php
                $duedateField = Ticket::duedateField('duedate', $info['duedate']);
                $duedateField->render();
                ?>
                &nbsp;<font class="error">&nbsp;<?= $errors['duedate']; ?> &nbsp; <?= $errors['time']; ?></font>
                <em><?= __('Time is based on your time
                        zone');?>&nbsp;(<?= $cfg->getTimezone($thisstaff); ?>)</em>
            </td>
        </tr>

        <?php
        if($thisstaff->hasPerm(Ticket::PERM_ASSIGN, false)) { ?>
        <tr>
            <td width="160"><?= __('Assign To');?>:</td>
            <td>
                <select id="assignId" name="assignId">
                    <option value="0" selected="selected">&mdash; <?= __('Select an Agent OR a Team');?> &mdash;</option>
                    <?php
                    if(($users=Staff::getAvailableStaffMembers())) {
                        echo '<OPTGROUP label="'.sprintf(__('Agents (%d)'), count($users)).'">';
                        foreach($users as $id => $name) {
                            $k="s$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }

                    if(($teams=Team::getActiveTeams())) {
                        echo '<OPTGROUP label="'.sprintf(__('Teams (%d)'), count($teams)).'">';
                        foreach($teams as $id => $name) {
                            $k="t$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }
                    ?>
                </select>&nbsp;<span class='error'>&nbsp;<?= $errors['assignId']; ?></span>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        <tbody id="dynamic-form">
        <?php
            $options = array('mode' => 'create');
            foreach ($forms as $form) {
                print $form->getForm($_SESSION[':form-data'])->getMedia();
                //include(STAFFINC_DIR .  'templates/dynamic-form.tmpl.php');
                $form->render($options);
            }
        ?>
        </tbody>
        <tbody>
        <?php
        //is the user allowed to post replies??
        if ($thisstaff->getRole()->hasPerm(Ticket::PERM_REPLY)) { ?>
        <tr>
            <th colspan="2">
                <em><strong><?= __('Response');?></strong>: <?= __('Optional response to the above issue.');?></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
            <?php
            if($cfg->isCannedResponseEnabled() && ($cannedResponses=Canned::getCannedResponses())) {
                ?>
                <div style="margin-top:0.3em;margin-bottom:0.5em">
                    <?= __('Canned Response');?>:&nbsp;
                    <select id="cannedResp" name="cannedResp">
                        <option value="0" selected="selected">&mdash; <?= __('Select a canned response');?> &mdash;</option>
                        <?php
                        foreach($cannedResponses as $id =>$title) {
                            echo sprintf('<option value="%d">%s</option>',$id,$title);
                        }
                        ?>
                    </select>
                    &nbsp;&nbsp;
                    <label class="checkbox inline"><input type='checkbox' value='1' name="append" id="append" checked="checked"><?= __('Append');?></label>
                </div>
            <?php
            }
                $signature = '';
                if ($thisstaff->getDefaultSignatureType() == 'mine')
                    $signature = $thisstaff->getSignature(); ?>
                <textarea
                    class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                        ?> draft draft-delete" data-signature="<?php
                        echo Format::htmlchars(Format::viewableImages($signature)); ?>"
                    data-signature-field="signature" data-dept-field="deptId"
                    placeholder="<?= __('Initial response for the ticket'); ?>"
                    name="response" id="response" cols="21" rows="8"
                    style="width:80%;" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.staff.response', false, $info['response']);
    echo $attrs; ?>><?= $_POST ? $info['response'] : $draft;
                ?></textarea>
                    <div class="attachments">
<?php
print $response_form->getField('attachments')->render();
?>
                    </div>

                <table border="0" cellspacing="0" cellpadding="2" width="100%">
            <tr>
                <td width="100"><?= __('Ticket Status');?>:</td>
                <td>
                    <select name="statusId">
                    <?php
                    $statusId = $info['statusId'] ?: $cfg->getDefaultTicketStatusId();
                    $states = array('open');
                    if ($thisstaff->hasPerm(Ticket::PERM_CLOSE, false))
                        $states = array_merge($states, array('closed'));
                    foreach (TicketStatusList::getStatuses(
                                array('states' => $states)) as $s) {
                        if (!$s->isEnabled()) continue;
                        $selected = ($statusId == $s->getId());
                        echo sprintf('<option value="%d" %s>%s</option>',
                                $s->getId(),
                                $selected
                                 ? 'selected="selected"' : '',
                                __($s->getName()));
                    }
                    ?>
                    </select>
                </td>
            </tr>
             <tr>
                <td width="100"><?= __('Signature');?>:</td>
                <td>
                    <?php
                    $info['signature']=$info['signature']?$info['signature']:$thisstaff->getDefaultSignatureType();
                    ?>
                    <label><input type="radio" name="signature" value="none" checked="checked"> <?= __('None');?></label>
                    <?php
                    if($thisstaff->getSignature()) { ?>
                        <label><input type="radio" name="signature" value="mine"
                            <?= ($info['signature']=='mine')?'checked="checked"':''; ?>> <?= __('My Signature');?></label>
                    <?php
                    } ?>
                    <label><input type="radio" name="signature" value="dept"
                        <?= ($info['signature']=='dept')?'checked="checked"':''; ?>> <?= sprintf(__('Department Signature (%s)'), __('if set')); ?></label>
                </td>
             </tr>
            </table>
            </td>
        </tr>
        <?php
        } //end canPostReply
        ?>
        <tr>
            <th colspan="2">
                <em><strong><?= __('Internal Note');?></strong>
                <font class="error">&nbsp;<?= $errors['note']; ?></font></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea
                    class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                        ?> draft draft-delete"
                    placeholder="<?= __('Optional internal note (recommended on assignment)'); ?>"
                    name="note" cols="21" rows="6" style="width:80%;" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.staff.note', false, $info['note']);
    echo $attrs; ?>><?= $_POST ? $info['note'] : $draft;
                ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align:center;">
    <input type="submit" name="submit" value="<?= _P('action-button', 'Open');?>">
    <input type="reset"  name="reset"  value="<?= __('Reset');?>">
    <input type="button" name="cancel" value="<?= __('Cancel');?>" onclick="javascript:
        $(this.form).find('textarea.richtext')
          .redactor('draft.deleteDraft');
        window.location.href='tickets.php'; " />
</p>
</form>
<script type="text/javascript">
$(function() {
    $('input#user-email').typeahead({
        source: function (typeahead, query) {
            $.ajax({
                url: "ajax.php/users?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            $('#uid').val(obj.id);
            $('#user-name').val(obj.name);
            $('#user-email').val(obj.email);
        },
        property: "/bin/true"
    });

   <?php
    // Popup user lookup on the initial page load (not post) if we don't have a
    // user selected
    if (!$_POST && !$user) {?>
    setTimeout(function() {
      $.userLookup('ajax.php/users/lookup/form', function (user) {
        window.location.href = window.location.href+'&uid='+user.id;
      });
    }, 100);
    <?php
    } ?>
});

$(function() {
    $('a#editorg').click( function(e) {
        e.preventDefault();
        $('div#org-profile').hide();
        $('div#org-form').fadeIn();
        return false;
     });

    $(document).on('click', 'form.org input.cancel', function (e) {
        e.preventDefault();
        $('div#org-form').hide();
        $('div#org-profile').fadeIn();
        return false;
    });

    $('.userSelection').select2({
      width: '450px',
      minimumInputLength: 3,
      ajax: {
        url: "ajax.php/users/local",
        dataType: 'json',
        data: function (params) {
          return {
            q: params.term,
          };
        },
        processResults: function (data) {
          return {
            results: $.map(data, function (item) {
              return {
                text: item.email + ' - ' + item.name,
                slug: item.slug,
                email: item.email,
                id: item.id
              }
            })
          };
          $('#user-email').val(item.name);
        }
      }
    });

    $('.userSelection').on('select2:select', function (e) {
      var data = e.params.data;
      $('#user-email').val(data.email);
    });

    $('.userSelection').on("change", function (e) {
      var data = $('.userSelection').select2('data');
      var data = data[0].text;
      var email = data.substr(0,data.indexOf(' '));
      $('#user-email').val(data.substr(0,data.indexOf(' ')));
     });

    $('.collabSelections').select2({
      width: '450px',
      minimumInputLength: 3,
      ajax: {
        url: "ajax.php/users/local",
        dataType: 'json',
        data: function (params) {
          return {
            q: params.term,
          };
        },
        processResults: function (data) {
          return {
            results: $.map(data, function (item) {
              return {
                text: item.name,
                slug: item.slug,
                id: item.id
              }
            })
          };
        }
      }
    });

  });
</script>
