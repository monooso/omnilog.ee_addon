<div class="experienceinternet">
<table class="mainTable padTable" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?php echo lang('thd_demo_title'); ?></th>
            <th><?php echo lang('thd_demo_description'); ?></th>
            <th><?php echo lang('thd_demo_custom_info'); ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>
        <tr class="odd">
            <td><?php echo lang('thd_demo_log_notice'); ?></td>
            <td><?php echo lang('demo_log_notice'); ?></td>
            <td>&nbsp;</td>
            <td style="vertical-align : middle;"><a class="button submit" href="<?php echo $run_demo_url .AMP .'demo=log_notice'; ?>"><?php echo lang('lbl_demo_run'); ?></a></td>
        </tr>

        <tr class="even">
            <td><?php echo lang('thd_demo_log_warning'); ?></td>
            <td><?php echo lang('demo_log_warning'); ?></td>
            <td>&nbsp;</td>
            <td style="vertical-align : middle;"><a class="button submit" href="<?php echo $run_demo_url .AMP .'demo=log_warning'; ?>"><?php echo lang('lbl_demo_run'); ?></a></td>
        </tr>

        <tr class="odd">
            <td><?php echo lang('thd_demo_log_error'); ?></td>
            <td><?php echo lang('demo_log_error'); ?></td>
            <td>&nbsp;</td>
            <td style="vertical-align : middle;"><a class="button submit" href="<?php echo $run_demo_url .AMP .'demo=log_error'; ?>"><?php echo lang('lbl_demo_run'); ?></a></td>
        </tr>

        <tr class="even">
            <td><?php echo lang('thd_demo_notify_standard'); ?></td>
            <td><?php echo sprintf(lang('demo_notify_standard'), $webmaster_email); ?></td>
            <td>&nbsp;</td>
            <td style="vertical-align : middle;"><a class="button submit" href="<?php echo $run_demo_url .AMP .'demo=notify_standard'; ?>"><?php echo lang('lbl_demo_run'); ?></a></td>
        </tr>

        <tr class="odd">
            <td><?php echo lang('thd_demo_notify_custom'); ?></td>
            <td><?php echo lang('demo_notify_custom'); ?></td>
            <td>
                <label><?php echo lang('lbl_demo_email'); ?></label>
                <input name="email" type="text" />
            </td>
            <td style="vertical-align : middle;"><a class="button submit" href="<?php echo $run_demo_url .AMP .'demo=notify_custom' .AMP .'email='; ?>"><?php echo lang('lbl_demo_run'); ?></a></td>
        </tr>
    </tbody>
</table>
</div><!-- /.experienceinternet -->
