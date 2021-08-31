    <div class="helpical-1" style="display: none;"></div>

    <div class="helpical-2" style="display: none;">
        <div class="--helpical-row --helpical-justify-content-center --helpical-align-items-center --helpical-mx-0 --helpical-newTicket">
            <div class="--helpical-px-sm-5 --helpical-px-4 --helpical-py-4 --helpical-newTicket-form">
                <div class="--helpical-d-flex --helpical-justify-content-sm-start --helpical-justify-content-center --helpical-align-items-center --helpical-mx-0">
                    <i class="--helpical-icon --helpical-icon-ticketplus --helpical-icon-2x --helpical-text-variable --helpical-p-2 --helpical-bg-gray --helpical-rounded-circle"></i>
                    <h1 class="--helpical-text-variable --helpical-h1 --helpical-font-weight-bold --helpical-h5 --helpical-mr-3"><?php _e('New ticket Form', 'helpical') ?></h1>
                </div>
                <div class="--helpical-form-new-ticket-message"></div>
                <div class="--helpical-mt-5 --helpical-row --helpical-justify-content-between --helpical-align-items-center --helpical-mx-0">
                    <div class="--helpical-col-sm-6 --helpical-col-12 --helpical-px-sm-2 --helpical-px-0">
                        <label class="--helpical-d-block --helpical-text-meta"><?php _e('Department', 'helpical') ?><span class="--helpical-text-danger"> *</span></label>
                        <select class="--helpical-ticket-departement --helpical-text-meta --helpical-text-info --helpical-w-100">
                            <?php $departments = get_option('helpical_categories', []);
                            $flag = true; ?>
                            <?php foreach ($departments as $department) : ?>
                                <option value="<?php echo $department['id'] ?>" data-categories="<?php echo str_replace('"', "'", json_encode($department['categories'], JSON_UNESCAPED_UNICODE)) ?>" <?php echo ($flag) ? 'selected' : '' ?>><?php echo $department['title'] ?></option>
                            <?php $flag = false;
                            endforeach; ?>
                        </select>
                    </div>
                    <div class="--helpical-col-sm-6 --helpical-col-12 --helpical-px-sm-2 --helpical-px-0 --helpical-mt-sm-0 --helpical-mt-3">
                        <label class="--helpical-d-block --helpical-text-meta"><?php _e('Category', 'helpical') ?><span class="--helpical-text-danger"> *</span></label>
                        <select class="--helpical-ticket-category --helpical-text-meta --helpical-text-info --helpical-w-100" data-others="<?php _e('Others', 'helpical') ?>">
                        </select>
                    </div>
                    <div class="--helpical-form-group --helpical-col-12 --helpical-px-sm-2 --helpical-px-0 --helpical-my-3">
                        <label class="--helpical-d-block --helpical-text-meta"><?php _e('Title', 'helpical') ?><span class="--helpical-text-danger"> *</span></label>
                        <input type="text" class="--helpical-form-control --helpical-ticket-title-input --helpical-newTicket-form-input --helpical-text-meta" placeholder="<?php _e('Title', 'helpical') ?>">
                    </div>
                    <div class="--helpical-form-group --helpical-col-12 --helpical-px-sm-2 --helpical-px-0 --helpical-mb-3">
                        <label class="--helpical-d-block --helpical-text-meta"><?php _e('Message', 'helpical') ?><span class="--helpical-text-danger"> *</span></label>
                        <textarea rows="3" class="--helpical-form-control --helpical-ticket-message-input --helpical-newTicket-form-input"></textarea>
                    </div>
                    <div class="--helpical-col-12 --helpical-mx-auto --helpical-mb-2 --helpical-px-sm-2 --helpical-px-0">
                        <label class="--helpical-d-block --helpical-text-meta --helpical-col-12 --helpical-px-0"><?php _e('Attachments', 'helpical') ?></label>
                        <div class="--helpical-input-group">
                            <div class="--helpical-input-group-prepend">
                                <span class="--helpical-input-group-text --helpical-ticket-attachments-btn --helpical-px-sm-4 --helpical-px-3 --helpical-text-meta --helpical-bg-variable --helpical-border-0 --helpical-text-white --helpical-user-select-none"><i class="--helpical-icon --helpical-icon-upload --helpical-ml-2"></i><?php _e('Choose file', 'helpical') ?></span>
                            </div>
                            <div class="--helpical-form-control --helpical-p-0 --helpical-position-relative --helpical-overflow-hidden">
                                <input type="file" multiple="true" placeholder=" " accept=".<?php echo helpical_get_base(',.')['allowed_attachment_formats_srting'] ?>" class="--helpical-form-control --helpical-ticket-attachments-input --helpical-position-absolute --helpical-w-100" style="opacity: 0; z-index: 2;">
                                <p class="--helpical-form-control --helpical-ticket-attachments-name --helpical-pt-2 --helpical-form-file-label --helpical-text-meta --helpical-white-space --helpical-m-0 --helpical-position-absolute --helpical-w-100"><?php _e('Attachments', 'helpical') ?></p>
                            </div>
                        </div>
                        <?php $base = helpical_get_base(); ?>
                        <p class="--helpical-text-meta --helpical-text-muted --helpical-attachments-note --helpical-col-12 --helpical-px-0 --helpical-mt-1 --helpical-font-weight-light">* <?php printf(__('Attachments limits: maximum file size %s - allowed formats : %s', 'helpical'), $base['allowed_attachment_file_size'], $base['allowed_attachment_formats_srting']) ?></p>
                    </div>
                    <div class="--helpical-col-12 --helpical-row --helpical-justify-content-center --helpical-align-items-center --helpical-mx-0 --helpical-mt-3 --helpical-px-0">
                        <div class="--helpical-col-xl-3 --helpical-col-lg-4 --helpical-col-md-6 --helpical-col-sm-8 --helpical-col-12 --helpical-row --helpical-justify-content-between --helpical-align-items-center --helpical-mx-0 --helpical-bg-gray --helpical-rounded --helpical-px-3 --helpical-py-3">
                            <div class="--helpical-text-meta"><?php _e('Importance', 'helpical') ?>:</div>
                            <div class="--helpical-d-none">
                                <input type="radio" name="importance" class="--helpical-importance-radio" value="l" checked>
                                <input type="radio" name="importance" class="--helpical-importance-radio" value="n">
                                <input type="radio" name="importance" class="--helpical-importance-radio" value="h">
                                <input type="radio" name="importance" class="--helpical-importance-radio" value="c">
                            </div>
                            <div class="--helpical-row --helpical-justify-content-end --helpical-align-items-center --helpical-mx-0">
                                <i class="--helpical-icon --helpical-icon-level1 --helpical-icon-lg --helpical-mx-1 --helpical-newTicket-important-icon active" data-value="l"><span class="path1"></span><span class="path2"></span></i>
                                <i class="--helpical-icon --helpical-icon-level2 --helpical-icon-lg --helpical-mx-1 --helpical-newTicket-important-icon" data-value="n"><span class="path1"></span><span class="path2"></span></i>
                                <i class="--helpical-icon --helpical-icon-level3 --helpical-icon-lg --helpical-mx-1 --helpical-newTicket-important-icon" data-value="h"><span class="path1"></span><span class="path2"></span></i>
                                <i class="--helpical-icon --helpical-icon-level4 --helpical-icon-lg --helpical-mx-1 --helpical-newTicket-important-icon" data-value="c"></i>
                            </div>
                        </div>
                    </div>
                    <div class="--helpical-text-center --helpical-col-12 --helpical-mt-3">
                        <button class="--helpical-btn --helpical-send-ticket --helpical-px-sm-5 --helpical-px-3 --helpical-py-sm-2 --helpical-py-2 --helpical-text-meta --helpical-bg-variable --helpical-text-white"><i class="--helpical-icon --helpical-icon-send --helpical-icon-lg --helpical-ml-2"></i><?php _e('Send', 'helpical') ?></button>
                        <br>
                        <button class="--helpical-btn --helpical-text-variable --helpical-mt-2 --helpical-text-meta --helpical-btn-arshive"><?php _e('Back to archive', 'helpical') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="helpical-3" style="display: none;"></div>