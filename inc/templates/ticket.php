<?php $ticket = get_query_var('ticket');
if (empty($ticket)) : ?>
    <div class="--helpical-alert --helpical-alert-danger --helpical-not-found" role="alert">
        <?php _e("No tickets found", 'helpical') ?>
    </div>
<?php else : ?>
    <div class="--helpical-px-4 --helpical-pt-3">
        <div class="--helpical-ticket-header --helpical-pt-4 --helpical-px-4 --helpical-mx-0">
            <div class="--helpical-Ticket-status">
                <div class="--helpical-d-sm-flex --helpical-d-block --helpical-justify-content-sm-between --helpical-justify-content-center --helpical-align-items-center --helpical-mx-0">
                    <div>
                        <div class="--helpical-text-meta --helpical-my-2 --helpical-text-sm-right --helpical-text-center"><span><?php _e('Ticket ID', 'helpical') ?>: </span><span class="--helpical-font-weight-bold"><?php echo $ticket->id ?></span></div>
                        <div class="--helpical-text-meta --helpical-my-2 --helpical-text-sm-right --helpical-text-center"><span><?php _e('Ticket title', 'helpical') ?>: </span><span class="--helpical-font-weight-bold"><?php echo $ticket->title ?></span></div>
                        <div class="--helpical-text-meta --helpical-my-2 --helpical-text-sm-right --helpical-text-center"><span><?php _e('Ticket status', 'helpical') ?>: </span><span class="--helpical-font-weight-bold"><?php echo getStatus($ticket->status) ?></span></div>
                    </div>
                    <div class="--helpical-row --helpical-justify-content-center --helpical-align-items-center --helpical-mx-0 --helpical-mt-sm-0 --helpical-mt-3">
                        <a href="#" class="--helpical-ml-3 --helpical-btn-back"><?php _e('Back', 'helpical') ?></a>
                        <button class="--helpical-btn --helpical-px-sm-3 --helpical-px-2 --helpical-py-sm-2 --helpical-py-1 --helpical-bg-variable --helpical-text-white --helpical-mx-1 --helpical-btn-shadow --helpical-text-meta --helpical-font-weight-light --helpical-d-flex --helpical-align-items-center --helpical-ticket-send-open"><i class="--helpical-icon --helpical-icon-send --helpical-text-white --helpical-ml-1"></i><?php echo ($ticket->status == 'c') ? __('Reopen ticket', 'helpical') : __('Reply', 'helpical') ?></button>
                        <?php if ($ticket->status != 'c') : ?>
                            <button class="--helpical-btn --helpical-px-sm-3 --helpical-px-2 --helpical-py-sm-2 --helpical-py-1 --helpical-bg-danger --helpical-text-white --helpical-mx-1 --helpical-btn-shadow --helpical-close-ticket --helpical-text-meta --helpical-font-weight-light"><span>&#10008; </span><?php _e('Close', 'helpical') ?></button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="--helpical-container-fluid">
                <div class="--helpical-row --helpical-justify-content-center --helpical-align-items-center --helpical-mx-0 --helpical-mt-4 --helpical-ticket-send">
                    <div class="--helpical-px-sm-5 --helpical-px-4 --helpical-py-5 --helpical-newTicket-form">
                        <div class="--helpical-d-flex --helpical-justify-content-sm-start --helpical-justify-content-center --helpical-align-items-center --helpical-mx-0">
                            <i class="--helpical-icon --helpical-icon-ticketplus --helpical-icon-2x --helpical-text-variable --helpical-p-2 --helpical-bg-gray --helpical-rounded-circle"></i>
                            <h1 class="--helpical-text-variable --helpical-font-weight-bold --helpical-h5 --helpical-mr-3"><?php _e('Reply', 'helpical') ?></h1>
                        </div>
                        <div class="--helpical-reply-ticket-message"></div>
                        <div class="--helpical-row --helpical-justify-content-between --helpical-align-items-center --helpical-mx-0 --helpical-text-right">
                            <div class="--helpical-form-group --helpical-col-12 --helpical-px-sm-2 --helpical-px-0 --helpical-my-3">
                                <label class="--helpical-d-block --helpical-text-right --helpical-text-meta"><?php _e('Title', 'helpical') ?><span class="--helpical-text-danger"> *</span></label>
                                <input type="text" class="--helpical-form-control --helpical-newTicket-form-input --helpical-text-meta" value="<?php echo __('Reply to', 'helpical') . ': ' . $ticket->title ?>" placeholder="" disabled>
                            </div>
                            <div class="--helpical-form-group --helpical-col-12 --helpical-px-sm-2 --helpical-px-0 --helpical-mb-3">
                                <label class="--helpical-d-block --helpical-text-right --helpical-text-meta"><?php _e('Message', 'helpical') ?><span class="text-danger"> *</span></label>
                                <textarea rows="3" class="--helpical-form-control --helpical-reply-message-input --helpical-newTicket-form-input"></textarea>
                            </div>
                            <div class="--helpical-col-12 --helpical-mx-auto --helpical-mb-2 --helpical-px-sm-2 --helpical-px-0">
                                <label class="--helpical-d-block --helpical-text-right --helpical-text-meta --helpical-col-12 --helpical-px-0"><?php _e('Attachments', 'helpical') ?></label>
                                <div class="--helpical-input-group">
                                    <div class="--helpical-input-group-prepend">
                                        <span class="--helpical-input-group-text --helpical-px-sm-4 --helpical-px-3 --helpical-reply-attachments-btn --helpical-text-meta --helpical-bg-variable --helpical-border-0 --helpical-text-white --helpical-user-select-none"><i class="--helpical-icon --helpical-icon-upload --helpical-ml-2"></i><?php _e('Choose file', 'helpical') ?></span>
                                    </div>
                                    <div class="--helpical-form-control --helpical-p-0 --helpical-position-relative --helpical-overflow-hidden">
                                        <input type="file" multiple="true" placeholder=" " accept=".<?php echo helpical_get_base(',.')['allowed_attachment_formats_srting'] ?>" class="--helpical-form-control --helpical-reply-attachments-input --helpical-position-absolute --helpical-w-100" style="opacity: 0; z-index: 3;">
                                        <p class="--helpical-text-right --helpical-form-control --helpical-reply-attachments-name --helpical-pt-2 --helpical-form-file-label --helpical-text-meta --helpical-text-variable --helpical-white-space --helpical-m-0 --helpical-position-absolute --helpical-w-100"><?php _e('Attachments', 'helpical') ?></p>
                                    </div>
                                </div>
                                <?php $base = helpical_get_base(); ?>
                                <p class="--helpical-text-meta --helpical-text-muted --helpical-attachments-note --helpical-col-12 --helpical-px-0 --helpical-mt-1 --helpical-font-weight-light">* <?php printf(__('Attachments limits: maximum file size %s - allowed formats : %s', 'helpical'), $base['allowed_attachment_file_size'], $base['allowed_attachment_formats_srting']) ?></p>
                            </div>
                            <div class="--helpical-text-center --helpical-col-12 --helpical-mt-3">
                                <button class="--helpical-btn --helpical-px-sm-4 --helpical-reply-ticket --helpical-px-3 --helpical-py-sm-2 --helpical-py-2 --helpical-text-meta --helpical-bg-variable --helpical-text-white"><i class="--helpical-icon --helpical-icon-send --helpical-icon-lg --helpical-ml-2"></i><?php _e('Reply ticket', 'helpical') ?></button>
                                <br>
                                <button class="--helpical-btn --helpical-text-danger --helpical-mt-2 --helpical-text-meta --helpical-ticket-send-cancel"><?php _e('Cancel', 'helpical') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="--helpical-form-close-ticket-message"></div>
            <br>
            <?php if ($ticket->status == 'c') : ?>
                <div class="--helpical-row --helpical-justify-content-between --helpical-satisfaction-container --helpical-align-items-center --helpical-mx-0">
                    <p class="--helpical-mb-0"><?php _e('Satisfaction', 'helpical') ?></p>
                    <div class="--helpical-d-flex --helpical-align-items-center">
                        <p class="--helpical-satisfaction-text --helpical-d-none --helpical-d-sm-block --helpical-text-meta --helpical-mb-0"><?php echo helpical_satisfactio_text($ticket->satisfaction) ?></p>
                        <div class="--helpical-satisfaction --helpical-mr-2<?php echo ($ticket->satisfaction == 1) ? ' active' : '' ?>" data-value="1"><img width="32" height="32" src="<?php echo plugins_url('assets/images/sad.png', HELPICAL_FILE) ?>"></div>
                        <div class="--helpical-satisfaction --helpical-mr-2<?php echo ($ticket->satisfaction == 2) ? ' active' : '' ?>" data-value="2"><img width="32" height="32" src="<?php echo plugins_url('assets/images/normal.png', HELPICAL_FILE) ?>"></div>
                        <div class="--helpical-satisfaction --helpical-mr-2<?php echo ($ticket->satisfaction == 3) ? ' active' : '' ?>" data-value="3"><img width="32" height="32" src="<?php echo plugins_url('assets/images/happy.png', HELPICAL_FILE) ?>"></div>
                    </div>
                </div>
                <br>
            <?php endif; ?>
            <div class="--helpical-pb-3">
                <hr class="--helpical-hr">
                <br>
                <?php if (is_array($ticket->contents) && !empty($ticket->contents)) :
                    foreach ($ticket->contents as $content) :
                        if ($content->private == 0) :
                            $ticket_class = '';
                            if ($content->system_ticket == 1)
                                $ticket_class = ' --helpical-system-ticket';
                            else if ($content->writer_department_id == 3)
                                $ticket_class = ' --helpical-customer-ticket'; ?>
                            <div class="--helpical-col-md-9 --helpical-col-sm-10 --helpical-col-12 --helpical-ticket-comment<?php echo $ticket_class ?> --helpical-mb-5 --helpical-px-0">
                                <div class="--helpical-ticket-comment-inner --helpical-py-3 --helpical-px-4">
                                    <div class="--helpical-ticket-comment-inner-identify --helpical-position-relative --helpical-d-flex --helpical-justify-content-start --helpical-align-items-center --helpical-mx-0 --helpical-text-dark --helpical-text-meta --helpical-font-weight-light --helpical-pt-sm-0 --helpical-pt-3">
                                        <img src="<?php echo $content->writer_photo_url ?>" width="50" height="50" class="--helpical-rounded-circle --helpical-ticket-comment-inner-identify-img --helpical-ml-2 --helpical-d-sm-block --helpical-d-none">
                                        <div class="--helpical-d-sm-flex --helpical-d-block --helpical-justify-content-start --helpical-align-items-cneter --helpical-mx-0">
                                            <div><?php echo $content->writer_user_name ?></div>
                                            <div class="--helpical-ml-1 --helpical-mr-sm-1">(<?php echo jdate('H:i | Y/n/j', strtotime($content->update_date_time)) ?>)</div>
                                        </div>
                                    </div>
                                    <div class="--helpical-text-meta --helpical-mt-4 --helpical-line-height"><?php echo $content->message ?></div>
                                    <hr class="--helpical-mb-2 --helpical-hr">
                                    <div class="--helpical-text-meta --helpical-font-weight-light"><?php echo __('Status', 'helpical') . ': ' . getStatus($content->status) ?></div>
                                </div>
                                <?php if (is_array($content->attachments) && !empty($content->attachments)) :
                                    $i = 0 ?>
                                    <div class="--helpical-mt-3 --helpical-text-meta">
                                        <?php foreach ($content->attachments as $attachment) :
                                            $i++; ?>
                                            <div><a href="<?php echo $attachment ?>"><?php echo __('Download atachemnt', 'helpical') . ' ' . $i ?></a></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                <?php endif;
                    endforeach;
                endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>