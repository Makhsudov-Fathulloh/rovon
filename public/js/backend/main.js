ClassicEditor.create(
    document.querySelector('#description'))
    .catch(error => {
        console.error(error);
    });

// // 1 234.567" yoki "1 234,567" yoki "1 234"
// function getNumericValue(elOrSelector) {
//     let raw;
//     if (typeof elOrSelector === 'string') {
//         raw = $(elOrSelector).val() || '';
//     } else if (!elOrSelector) {
//         return 0;
//     } else {
//         raw = elOrSelector.value || '';
//     }
//
//     raw = raw.toString().trim().replace(/\s/g, '').replace(',', '.');
//
//     const num = parseFloat(raw);
//     return isNaN(num) ? 0 : num;
// }

// Custom number format
function formatNumberCustom(number, typeCount = 1) {
    if (number === null || number === undefined || number === '') return '0';
    const num = parseFloat(number);
    if (isNaN(num)) return number;
    const fixed = num.toFixed(typeCount == 1 ? 0 : 3);
    const [intPart, decimalPart] = fixed.split('.');
    const formattedInt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return decimalPart ? `${formattedInt}.${decimalPart}` : formattedInt;
}

// Alert (success, error, warning, info)
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.flash-alert');

    alerts.forEach((alert, index) => {
        // kirish animatsiyasi
        setTimeout(() => {
            alert.style.transition = 'transform 0.6s ease, opacity 0.6s ease';
            alert.style.transform = 'translateX(0)';
        }, index * 200);

        // avtomatik yopilish
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(120%)';
            setTimeout(() => alert.remove(), 600);
        }, 3500 + index * 200); // 350000 ms juda uzun, 3500 ms ga o'zgartirdim
    });

    // close tugmasi ishlashi
    const closeButtons = document.querySelectorAll('.flash-alert .btn-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const alert = this.closest('.flash-alert');
            alert.remove();
        });
    });
});

// Inputmask
$(document).ready(function () {
    $("#phone").inputmask({
        mask: "+\\9\\98 (99) 999 99 99",
        greedy: false,
        clearIncomplete: true,
        placeholder: "_",
        showMaskOnHover: true,
        showMaskOnFocus: false
    });

    // Form yuborishdan oldin faqat raqamlarni olish
    $("form").on("submit", function () {
        let phone = $("#phone").inputmask("unmaskedvalue"); // Masalan: 901234567
        $("#phone").val(phone); // Formga toza raqam yozib yuboramiz
    });


    // input integer, 1000000 -> 1 000 0000
    $(".filter-numeric").inputmask({
        alias: "integer",   // faqat butun son
        groupSeparator: " ", // minglik ajratgich (1 000 000)
        placeholder: "",
        autoGroup: true,     // avtomatik guruhlash
        rightAlign: false,   // chapdan yoziladi
        allowMinus: false,   // manfiy son kiritilmaydi
        showMaskOnHover: false,
    });

    $(".filter-numeric-decimal").inputmask({
        alias: "decimal",       // kasr sonlar uchun
        groupSeparator: " ",    // minglik ajratgich (1 000 000.55)
        placeholder: "",
        autoGroup: true,
        rightAlign: false,
        allowMinus: false,
        digits: 3,              // nechta kasr joyi bo‘lishini belgilang (masalan 3 -> 0.055)
        digitsOptional: true,   // kasr qismi ixtiyoriy
        showMaskOnHover: false,
    });

    $('.filter-select2').select2({
        placeholder: $(this).data('placeholder') || "Барчаси",
        allowClear: true,
        minimumInputLength: 2,
        language: {
            inputTooShort: function (args) {
                return "Камида 2 белги киритинг";
            },
            noResults: function () {
                return "Ҳеч қандай натижа топилмади";
            }
        },
        width: '100%'
    });


    // Alert delete
    function deleteButton(url, id) {
        if (!confirm('Ҳақиқатан ҳам ўчирмоқчимисиз?')) return;

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                showFlashAlert('success', data.message); // alertni chaqiramiz
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                }
            })
            .catch(err => {
                showFlashAlert('error', 'Хатолик юз берди!');
                console.error(err);
            });
    }
});

// filter date
const from = document.querySelector('input[name="filters[created_from]"]');
const to   = document.querySelector('input[name="filters[created_to]"]');

function updateToMin() {
    if (from?.value) {
        to.min = from.value;

        // agar oldingi qiymat min dan kichik bo‘lsa, to.value ni tozalash
        if (to.value && to.value < from.value) {
            to.value = '';
        }
    } else {
        to.min = '';
    }
}

// page load’da min ni set qilamiz
updateToMin();

// from o‘zgarganda min ni yangilash
from?.addEventListener('change', updateToMin);
