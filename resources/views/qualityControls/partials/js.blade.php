@push('scripts')
    @vite(['resources/js/plugins/Select2.min.js'])
    <script type="module">
        // Form Select Area
        $(".select2").select2({
            placeholder: "Select an Option",
        });

        $("#limitedSelect").select2({
            placeholder: "Select an Option",
            maximumSelectionLength: 2,
        });
    </script>
@endpush
