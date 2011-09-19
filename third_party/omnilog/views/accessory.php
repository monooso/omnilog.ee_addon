<div class="experienceinternet" id="omnilog_accessory">
<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th><?php echo lang('thd_date'); ?></th>
            <th><?php echo lang('thd_addon'); ?></th>
            <th><?php echo lang('thd_type'); ?></th>
            <th><?php echo lang('thd_notify_admin'); ?></th>
            <th><?php echo lang('thd_message'); ?></th>
            <th style="width : 30%"><?php echo lang('thd_extended_data'); ?></th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($log_entries AS $log_entry): ?>
        <tr>
            <td><?php echo $log_entry->get_log_entry_id(); ?></td>
            <td>
                <span style="white-space : nowrap;"><?php echo date('j M, Y', $log_entry->get_date()); ?></span>
                <span style="white-space : nowrap;">at <?php echo date('g:ia', $log_entry->get_date()); ?></span>
            </td>
            <td><?php echo $log_entry->get_addon_name(); ?></td>
            <td><?php echo lang('lbl_type_' .$log_entry->get_type()); ?></td>
            <td><?php
                if ($log_entry->get_notify_admin() !== TRUE):
                    echo lang('lbl_no');
                else:
                    if ($admin_emails = $log_entry->get_admin_emails()):
                        foreach ($admin_emails AS $email):
                            echo $email .'<br />';
                        endforeach;
                    else:
                        echo $webmaster_email .'<br />';
                    endif;
                endif;
            ?></td>
            <td><?php echo nl2br($log_entry->get_message()); ?></td>
            <?php $extended_data = nl2br($log_entry->get_extended_data());
              if ( ! $extended_data): ?>
            <td>&nbsp;</td>
            <?php else: ?>
            <td class="extended_data"><?php echo $extended_data; ?></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</div><!-- /#omnilog_accessory -->
