
var tags = {

    deleteTag: function(idTag) {
        sendRequest('tag/delete', {id_tag:idTag, id_project: idSelectedProject}, function(response){

            //statusField.render(response.status);

        });

    },

    selectTag: function(idTag, el) {
        $('.tag_block').removeClass('success');
        el.addClass('success');
        
        tags.TagForm.render(idTag);

    },

    TagForm: {
        save:   function() {

            var data = $('#tagForm').serialize();
            sendRequest('tag/save', {form: data, id_project: idSelectedProject}, function(response) {
                statusField.render(response.status);
                tags.reload();
                
            });
        },
        render: function(idTag) {

            var template = $('#tagFormTemplate').html();

            if(idTag === undefined) {

                var rendered = Mustache.render(template);
                $('#tagFormBlock').html(rendered);

            } else {

                sendRequest('tag/getone',{id_tag:idTag}, function(response){

                    var rendered = Mustache.render(template, response.data.tag);
                    $('#tagFormBlock').html(rendered);
                });
            }
        },
        hide:   function() {
            $('#tagFormBlock').html('');
        }
    },
    setTagStatus: function(idTag, status) {

        sendRequest('tag/setstatus', {id_tag: idTag, status: status}, function(response){
            statusField.render(response.status);

            if(response.status.state === 'Ok'){
                tags.reload();
            }
        });
    },
    reload: function() {
        sendRequest('tag/getall',{ id_project: idSelectedProject}, function(response){

            response.data.i18n = i18n;
            var template = $('#tagsTemplate').html();
            var rendered = Mustache.render(template, response.data);

            $('#tagsBlock').html(rendered);
        });

        tags.TagForm.render();
    }
};