
<script src="../../../js/tinymce.js"></script>
<script src="../../../js/showFileInput.js"></script>

<?php

echo $templateAdmin->getActionTemplate();
?>


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

        $('.tinymce').each((index, element)=> {

           element.innerHTML = element.getAttribute('value');
        })
    });
</script>
