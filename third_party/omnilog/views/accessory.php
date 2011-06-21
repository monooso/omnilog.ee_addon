<div id="omnilog_accessory">
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
            <th>&nbsp;</th>
            <th><?php echo lang('thd_date'); ?></th>
            <th><?php echo lang('thd_addon'); ?></th>
            <th><?php echo lang('thd_type'); ?></th>
            <th><?php echo lang('thd_notify_admin'); ?></th>
            <th><?php echo lang('thd_message'); ?></th>
        </tr>
	</thead>

	<tbody>
    <?php
        foreach ($log_entries AS $log_entry):
    ?>
		<tr>
            <td><?php echo $log_entry->get_log_entry_id(); ?></td>
            <td><?php echo date('Y-m-d H:i', $log_entry->get_date()); ?></td>
            <td><?php echo $log_entry->get_addon_name(); ?></td>
            <td><?php echo lang('lbl_type_' .$log_entry->get_type()); ?></td>
            <td><?php echo $log_entry->get_notify_admin() === TRUE ? lang('lbl_yes') : lang('lbl_no'); ?></td>
            <td><?php echo nl2br($log_entry->get_message()); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
</div><!-- /#omnilog_accessory -->
