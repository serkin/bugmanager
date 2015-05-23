

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
    }
};