<script type="text/javascript" src="js/raphael-min.js?f1e9e88"></script>
<script type="text/javascript" src="js/g.raphael.js?f1e9e88"></script>
<script type="text/javascript" src="js/g.line-min.js?f1e9e88"></script>
<script type="text/javascript" src="js/g.dot-min.js?f1e9e88"></script>
<script type="text/javascript" src="js/dashboard.inc.js?f1e9e88"></script>

<link rel="stylesheet" type="text/css" href="css/dashboard.css?f1e9e88"/>

<form method="post" action="dashboard.php">
<div id="basic_search">
    <div style="min-height:25px;">
        <!--<p><?php //echo __('Select the starting time and period for the system activity graph');?></p>-->
            <?= csrf_token(); ?>
            <label>
                <?= __( 'Report timeframe'); ?>:
                <input type="text" class="dp input-medium search-query"
                    name="start" placeholder="<?= __('Last month');?>"
                    value="<?php
                        echo Format::htmlchars($report->getStartDate());
                    ?>" />
            </label>
            <label>
                <?= __( 'period');?>:
                <select name="period">
                    <option value="now" selected="selected">
                        <?= __( 'Up to today');?>
                    </option>
                    <option value="+7 days">
                        <?= __( 'One Week');?>
                    </option>
                    <option value="+14 days">
                        <?= __( 'Two Weeks');?>
                    </option>
                    <option value="+1 month">
                        <?= __( 'One Month');?>
                    </option>
                    <option value="+3 months">
                        <?= __( 'One Quarter');?>
                    </option>
                </select>
            </label>
            <button class="green button action-button muted" type="submit">
                <?= __( 'Refresh');?>
            </button>
            <i class="help-tip icon-question-sign" href="#report_timeframe"></i>
    </div>
</div>
<div class="clear"></div>
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="pull-left flush-left">
        <h2><?= __('Ticket Activity');
            ?>&nbsp;<i class="help-tip icon-question-sign" href="#ticket_activity"></i></h2>
    </div>
</div>
<div class="clear"></div>
<!-- Create a graph and fetch some data to create pretty dashboard -->
<div style="position:relative">
    <div id="line-chart-here" style="height:300px"></div>
    <div style="position:absolute;right:0;top:0" id="line-chart-legend"></div>
</div>

<hr/>
<h2><?= __('Statistics'); ?>&nbsp;<i class="help-tip icon-question-sign" href="#statistics"></i></h2>
<p><?= __('Statistics of tickets organized by department, help topic, and agent.');?></p>
<p><b><?= __('Range: '); ?></b>
  <?= __($range[0] . ' - ' . $range[1] .  ' (' . Format::timezone($timezone) . ')');?>
</p>

<ul class="clean tabs">
<?php
$first = true;
foreach ($groups as $g=>$desc) { ?>
    <li class="<?= $first ? 'active' : ''; ?>"><a href="#<?= Format::slugify($g); ?>"
        ><?= Format::htmlchars($desc); ?></a></li>
<?php
    $first = false;
} ?>
</ul>

<?php
$first = true;
foreach ($groups as $g=>$desc) {
    $data = $report->getTabularData($g); ?>
    <div class="tab_content <?= (!$first) ? 'hidden' : ''; ?>" id="<?= Format::slugify($g); ?>">
    <table class="dashboard-stats table"><tbody><tr>
<?php
    foreach ($data['columns'] as $j=>$c) {
      ?>
        <th <?php if ($j === 0) echo 'width="30%" class="flush-left"'; ?>><?= Format::htmlchars($c);
        switch ($c) {
          case 'Opened':
            ?>
              <i class="help-tip icon-question-sign" href="#opened"></i>
            <?php
            break;
          case 'Assigned':
            ?>
              <i class="help-tip icon-question-sign" href="#assigned"></i>
            <?php
            break;
            case 'Overdue':
              ?>
                <i class="help-tip icon-question-sign" href="#overdue"></i>
              <?php
              break;
            case 'Closed':
              ?>
                <i class="help-tip icon-question-sign" href="#closed"></i>
              <?php
              break;
            case 'Reopened':
              ?>
                <i class="help-tip icon-question-sign" href="#reopened"></i>
              <?php
              break;
            case 'Deleted':
              ?>
                <i class="help-tip icon-question-sign" href="#deleted"></i>
              <?php
              break;
            case 'Service Time':
              ?>
                <i class="help-tip icon-question-sign" href="#service_time"></i>
              <?php
              break;
            case 'Response Time':
              ?>
                <i class="help-tip icon-question-sign" href="#response_time"></i>
              <?php
              break;
        }
        ?></th>
<?php
    } ?>
    </tr></tbody>
    <tbody>
<?php
    foreach ($data['data'] as $i=>$row) {
        echo '<tr>';
        foreach ($row as $j=>$td) {
            if ($j === 0) { ?>
                <th class="flush-left"><?= Format::htmlchars($td); ?></th>
<?php       }
            else { ?>
                <td><?= Format::htmlchars($td);
                if ($td) { // TODO Add head map
                }
                echo '</td>';
            }
        }
        echo '</tr>';
    }
    $first = false; ?>
    </tbody></table>
    <div style="margin-top: 5px"><button type="submit" class="link button" name="export"
        value="<?= Format::htmlchars($g); ?>">
        <i class="icon-download"></i>
        <?= __('Export'); ?></a></div>
    </div>
<?php
}
?>
</form>
<script>
    $.drawPlots(<?= JsonDataEncoder::encode($report->getPlotData()); ?>);
    // Set Selected Period For Dashboard Stats and Export
    <?php if ($report && $report->end) { ?>
        $("div#basic_search select option").each(function(){
            // Remove default selection
            if ($(this)[0].selected)
                $(this).removeAttr('selected');
            // Set the selected period by the option's value (periods equal
            // option's values)
            if ($(this).val() == "<?= $report->end; ?>")
                $(this).attr("selected","selected");
        });
    <?php } ?>
</script>
