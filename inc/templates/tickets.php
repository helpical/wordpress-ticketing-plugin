<div class="--helpical-px-md-4 --helpical-px-sm-2 --helpical-px-1 --helpical-py-4">
    <div class="--helpical-text-center --helpical-text-variable">
        <i class="--helpical-icon --helpical-icon-ticket --helpical-icon-2x --helpical-bg-gray --helpical-p-2 --helpical-rounded-circle"></i>
        <h4 class="--helpical-font-weight-bold --helpical-h4 --helpical-mt-3"><?php _e('Tickets', 'helpical') ?></h4>
    </div>
    <div class="--helpical-my-4 --helpical-row --helpical-justify-content-between --helpical-mx-0">
        <button class="--helpical-btn --helpical-bg-variable --helpical-text-white --helpical-btn-new-ticket --helpical-px-3 --helpical-py-2 --helpical-d-flex --helpical-align-items-center"><i class="--helpical-icon --helpical-icon-plus --helpical-text-white --helpical-ml-2"></i><?php _e('New ticket', 'helpical') ?></button>
        <div class="--helpical-refresh --helpical-cursor">
            <svg fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30px" height="30px">
                <path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z" />
            </svg>
        </div>
    </div>
    <div class="--helpical-overflowX">
        <?php
        $tickets = get_query_var('tickets');
        $user_id = get_query_var('user_id');
        if (count($tickets) == 0) :
        ?>
            <div class="--helpical-alert --helpical-alert-danger --helpical-not-found" role="alert">
                <?php _e("No tickets found", 'helpical') ?>
            </div>
        <?php else : ?>
            <table class="--helpical-w-100 --helpical-border-collapse --helpical-text-meta --helpical-overflow-hidden --helpical-ticket-table">
                <tr>
                    <th></th>
                    <th><?php _e('Ticket number', 'helpical') ?></th>
                    <th><?php _e('Category/Title', 'helpical') ?></th>
                    <th><?php _e('Department', 'helpical') ?></th>
                    <th><?php _e('Status', 'helpical') ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <?php
                $i = 0;
                foreach ($tickets as $ticket) :
                    $i++;
                    $status = getStatus($ticket->status);
                    $outgoing = ($user_id == $ticket->owner_user_id);
                    switch ($ticket->importance) {
                        case 'l':
                            $importance = __('Low', 'helpical');
                            $importanceIcon = 'level1';
                            break;
                        case 'n':
                            $importance = __('Normal', 'helpical');
                            $importanceIcon = 'level2';
                            break;
                        case 'h':
                            $importance = __('High', 'helpical');
                            $importanceIcon = 'level3';
                            break;
                        default:
                            $importance = __('Critical', 'helpical');
                            $importanceIcon = 'level4';
                    }
                ?>
                    <tr data-id="<?php echo $i - 1 ?>">
                        <td><i class="--helpical-icon --helpical-icon-<?php echo ($ticket->seen == '1') ? 'read --helpical-icon-lg --helpical-text-primary' : 'unread --helpical-p-2' ?> --helpical-bg-gray --helpical-p-1 --helpical-rounded-circle"></i></td>
                        <td><?php echo $ticket->ticket_id ?></td>
                        <td><a class="--helpical-show-ticket" href="#"><?php echo $ticket->title ?></a></td>
                        <td><i class="--helpical-icon --helpical-icon-<?php echo ($outgoing) ? 'outgoing' : 'incoming' ?> --helpical-icon-lg --helpical-text-success --helpical-ml-1"></i><?php echo ($outgoing) ? $ticket->target_department_name : $ticket->owner_department_name ?> (<?php echo ($outgoing) ? $ticket->target_user_name : $ticket->owner_user_name ?>)</td>
                        <td><?php echo $status ?></td>
                        <td class="--helpical-tooltip-owner --helpical-position-relative" data-tooltip="<?php echo __('Created at', 'helpical') . ' ' . jdate('H:i | Y/n/j', strtotime($ticket->create_date_time)) ?>"><i class="--helpical-icon --helpical-icon-timeplus --helpical-icon-lg --helpical-text-variable"></i></td>
                        <td class="--helpical-tooltip-owner --helpical-position-relative" data-tooltip="<?php echo __('Updated at', 'helpical') . ' ' . jdate('H:i | Y/n/j', strtotime($ticket->update_date_time)) ?>"><i class="--helpical-icon --helpical-icon-timeupdate --helpical-icon-lg --helpical-text-variable"></i></td>
                        <td class="--helpical-tooltip-owner --helpical-position-relative" data-tooltip="<?php echo __('Importance', 'helpical') . ' ' . $importance ?>"><i class="--helpical-icon --helpical-icon-<?php echo $importanceIcon ?> --helpical-icon-lg"><span class="path1"></span><span class="path2"></span></i></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>