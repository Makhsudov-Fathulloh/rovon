document.addEventListener("DOMContentLoaded", function () {
    const button = document.querySelector('.open-file-manager');
    const realFileInput = document.getElementById('real-file');
    const textInput = document.getElementById('thumbnail_1');
    const preview = document.getElementById('holder_1');

    button.addEventListener('click', function () {
        realFileInput.click(); // File input
    });

    realFileInput.addEventListener('change', function () {
        const files = Array.from(realFileInput.files);
        if (files.length > 0) {
            // 1. Fayllar nomini inputga yozamiz (vergul bilan ajratib)
            textInput.value = files.map(file => file.name).join(', ');

            // 2. Preview ichini tozalab olamiz
            preview.innerHTML = '';

            // 3. Har bir faylni ko‘rsatamiz (faqat rasm bo‘lsa)
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxHeight = '100px';
                        img.style.marginRight = '10px';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
});
