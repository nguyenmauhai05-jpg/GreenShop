document.addEventListener(
    'DOMContentLoaded',
    function () {

        const input =
            document.getElementById(
                'anh_dai_dien'
            );

        const image =
            document.getElementById(
                'plantPreviewImage'
            );

        const placeholder =
            document.getElementById(
                'plantUploadPlaceholder'
            );


        if (!input) {
            return;
        }


        input.addEventListener(
            'change',
            function (event) {

                const file =
                    event.target.files[0];

                if (!file) {
                    return;
                }


                const reader =
                    new FileReader();


                reader.onload =
                    function (e) {

                        image.src =
                            e.target.result;

                        image.style.display =
                            'block';

                        if (placeholder) {
                            placeholder.style.display =
                                'none';
                        }

                    };


                reader.readAsDataURL(
                    file
                );

            }
        );

    }
);
