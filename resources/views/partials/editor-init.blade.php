<script src="{{ asset('tinymce/tinymce.js') }}"></script>
<script>
    tinymce.init({
        selector: 'textarea.source',
        theme: 'silver',
        menubar: false,
        plugins: ["lists link image media autolink imagetools preview code fullscreen"],
        toolbar: "h4 h5 h6 | bold italic underline strikethrough | bullist numlist | link image media | preview code fullscreen"
    });
</script>
