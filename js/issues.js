

var issues = {
    save: function(code) {

        var data = $('#translationForm').serialize();

        sendRequest('translation/save', {form:data}, function(response){
            statusField.render(response.status);
            translation.render(code);
        });

    },

    render: function() {

        sendRequest('issue/getall', {id_project: idSelectedProject}, function(response){
            
            response.data.id_project = idSelectedProject;

            var template = $('#issuesTemplate').html();
            var rendered = Mustache.render(template, response.data);

            $('#issuesBlock').html(rendered);
        });

    },
    setIssueStatus: function(idIssue, status) {

        sendRequest('issue/setstatus', {id_issue: idIssue, status: status}, function(response){
            statusField.render(response.status);

            if(response.status.state === 'Ok'){
                issues.render();
            }
        });
    },
    deleteIssue: function(idIssue) {

        sendRequest('issue/delete', {id_issue: idIssue}, function(response){
            statusField.render(response.status);

            if(response.status.state === 'Ok'){
                issues.render();
            }
        });
    },
    selectIssue: function(idIssue) {

        $('.issue_block').removeClass('success');
        $('#issue_block_' + idIssue).addClass('success');

        issues.IssueForm.render(idIssue);

    },
    IssueForm: {
        save: function(){
            var data = $('#issueForm').serialize();

            sendRequest('issue/save', {form: data, id_project: idSelectedProject}, function(response){

                statusField.render(response.status);

                if(response.status.state === 'Ok'){
                    issues.reload();

                }

            });
        },

        render: function(idIssue) {

            var template = $('#issueFormTemplate').html();

            if(idIssue === undefined) {

                var rendered = Mustache.render(template);
                $('#issueFormBlock').html(rendered);

            } else {

                sendRequest('issue/getone',{id_project: idSelectedProject, id_issue: idIssue}, function(response){


                    response.data.types = [
                            { name: 'bug' },
                            { name: 'feature' }
                        ];
                        
                    for (var i in response.data.types) {
                        
                        if(response.data.types[i].name === response.data.issue.type) {
                            response.data.types[i].selected = true;
                        }
                    }

                    var rendered = Mustache.render(template, response.data);
                    $('#issueFormBlock').html(rendered);

                });
            }
        }
    }
};