<?php

echo $templateAdmin->getActionTemplate();


?>
<script src="../../../js/jquery.js"></script>
<script src="../../../js/algolia.js"></script>
<script src="../../../js/tinymce.js"></script>
<script src="../../../js/showFileInput.js"></script>
<script>
    $(document).ready(() => {
        bsCustomFileInput.init();

        tinymce.init({
            selector: '.tinymce',
            height: 500,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });



        $('#user_id').autocomplete({hint: false}, [
            {
                source: function(query, cb) {
                    let queryData = {
                      param: query
                    };

                    $.ajax({
                        url: `${ window.location.protocol + "//" + window.location.host}/admin/ajaxAdminAdminEntity`,
                        method: 'GET',
                        data: queryData,
                        success: function (data) {
                            let responseData = [];

                            let newData = JSON.parse(data);

                            if (newData[0] == 'Nimic' || newData[0] == undefined) {
                                    responseData.push({value : 'Niciun rezultat gasit'});
                            } else {

                                for (let i = 0; i < newData.length; i++) {
                                    responseData.push({value : newData[i]})
                                }
                            }
                            cb(responseData);
                        }
                    });
                }
            }
        ]);
    });
</script>
