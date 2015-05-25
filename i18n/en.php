<?php


$app['i18n']['en'] = array(
    'layout' => array(
        'title'             => 'Bugmanager',
        'name'              => 'Name',
        'description'              => 'Description',
        'manage'            => 'Manage',
        'clear'             => 'Clear',
        'add_project'       => 'Add/edit project',
        'delete'            => 'delete',
        'release'  => 'Release',
        'assigned_to_user'  => 'Assigned to user',
        'type'  => 'Type',
        'issue_search_placeholder'  => 'code',
        'save'              => 'Save'
        ),
    'bugmanager' => array(
        'project_saved' => 'Project saved!',
        'project_removed' => 'Project removed!',
        'issue_status_updated' => 'Issue status updated',
        'issue_removed' => 'Issue removed!',
        'issue_saved' => 'Issue saved!',
    ),
    
    'errors' => array(
        'empty_id_project' => 'ID project not specified',
        'empty_project_name' => 'Project name not specified',
        'empty_issue_status' => 'Issue status not specified',
        'empty_issue_id' => 'Issue ID not specified',
        'cannot_update_issue_status' => 'Cannot update issue status',
    )
);