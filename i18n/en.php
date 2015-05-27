<?php


$app['i18n']['en'] = array(
    'layout' => array(
        'title'             => 'Bugmanager',
        'add_tag'       => 'Add/edit release/tag',
        'tags'          => 'Releases/Tags',
        'name'              => 'Name',
        'version'           => 'Release version',
        'description'       => 'Description',
        'manage'            => 'Manage',
        'clear'             => 'Clear',
        'add_project'       => 'Add/edit project',
        'delete'            => 'delete',
        'tag'           => 'Release/Tag',
        'assigned_to_user'  => 'Assigned to user',
        'type'              => 'Type',
        'issue_search_placeholder'  => 'code',
        'save'              => 'Save'
        ),
    'bugmanager' => array(
        'project_saved' => 'Project saved!',
        'project_removed' => 'Project removed!',
        'issue_status_updated' => 'Issue status updated',
        'issue_removed' => 'Issue removed!',
        'issue_saved' => 'Issue saved!',
        'tag_saved' => 'Tag saved!',
    ),
    
    'errors' => array(
        'empty_id_project' => 'ID project not specified',
        'empty_project_name' => 'Project name not specified',
        'empty_issue_status' => 'Issue status not specified',
        'empty_issue_id' => 'Issue ID not specified',
        'empty_id_tag' => 'Tag ID not specified',
        'empty_tag_version' => 'Tag version not specified',
        'cannot_update_issue_status' => 'Cannot update issue status',
    )
);