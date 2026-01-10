<style>
    @media (max-width: 767px) {
        .btn-group {
            width: 100%;
        }
    }

    .custom-actions .btn {
        min-width: 38px;
        border: 1px solid #ddd;
    }
</style>

@props([
    'route' => null,
    'id' => null,

    'subRoute' => null,
    'variation' => null,

    'addCountTitle' => null,
    'listRoute' => null,
    'createTitle' => null,
    'listTitle' => null,

    'back' => false,

    'add' => false,
    'addCount' => false,
    'list' => false,
    'view' => false,
    'edit' => false,
    'delete' => false,

    'report' => false,
    'isOpenLabel' => 'Кассани очиш',
    'isCloseLabel' => 'Кассани ёпиш',
    'isOpenTextLabel' => 'Бугунги ҳисоботни очишни истайсизми?',
    'isCloseTextLabel' => 'Бугунги ҳисоботни ёпишни истайсизми?',
    'todayReport' => null,

    'editLabel' => null,
    'deleteLabel' => null,
    'viewClass' => 'btn btn-info btn-sm',
    'editClass' => 'btn btn-warning btn-sm',

    'addEditDel' => ['Admin', 'Manager', 'Developer'],
])

<div class="btn-group custom-actions">

    @if($addCount && in_array(auth()->user()->role->title, $addEditDel))
        <button type="button"
                class="btn btn-sm btn-success add-count-btn"
                data-id="{{ $attributes->get('data-id', $variation->id) }}"
                data-title="{{ $attributes->get('data-title', $variation->title) }}"
                data-count="{{ $attributes->get('data-count', $variation->count) }}"
                data-unit="{{ $attributes->get('data-unit', $variation->unit) }}"
                @if($attributes->has('data-model'))
                    data-model="{{ $attributes->get('data-model') }}"
                @endif
                title="{{ $addCountTitle }}">
            <i class="fa fa-plus"></i>
        </button>
    @endif

    {{--    @if($addCount)--}}
    {{--        <button type="button" class="btn btn-sm btn-success add-count-btn"--}}
    {{--                data-id="{{ $variation->id }}"--}}
    {{--                data-title="{{ $variation->title }}"--}}
    {{--                data-count="{{ $variation->count }}"--}}
    {{--                title="{{ $addCountTitle }}">--}}
    {{--            <i class="fa fa-plus"></i>--}}
    {{--        </button>--}}
    {{--    @endif--}}

    @if($back)
        <a href="{{ url()->previous() }}" class="btn btn-dark"><i class="fa fa-arrow-left"></i> Орқага</a>
    @endif

    @if($add && in_array(auth()->user()->role->title, $addEditDel))
        <a href="{{ url("admin/$route/$id/$subRoute/create") }}"
           class="btn btn-info btn-sm">
            <i class="fa fa-plus" title="{{ $createTitle }}"></i>
        </a>
    @endif

    @if($list)
        <a href="{{ route($listRoute . '.list', $id) }}"
           class="btn btn-info btn-sm">
            <i class="fa fa-eye" title="{{ $listTitle }}"></i>
        </a>
    @endif

    @if($view)
        <a href="{{ route($route . '.show', $id) }}"
           class="{{ $viewClass }}" title="Кўриш">
            <i class="fa fa-eye"></i>
        </a>
    @endif

    @if($edit && in_array(auth()->user()->role->title, $addEditDel))
        <a href="{{ route($route . '.edit', $id) }}"
           class="{{ $editClass }}" title="Таҳрирлаш">
            @if($editLabel)
                {{ $editLabel }}
            @else
                <i class="fa fa-edit"></i>
            @endif
        </a>
    @endif

    @if($delete && in_array(auth()->user()->role->title, $addEditDel))
        <button type="button" class="btn btn-danger btn-sm"
                onclick="deleteButton('{{ route($route . '.destroy', $id) }}', {{ $id }})"
                title="Ўчириш">
            @if($deleteLabel)
                {{ $deleteLabel }}
            @else
                <i class="fa fa-trash"></i>
            @endif
        </button>
    @endif

    @if($report)
        @if(!$todayReport || $todayReport->isClose())
            <form action="{{ route($route . '.open') }}" method="POST" class="confirmable-form"
                  data-message="{{ $isOpenTextLabel }}">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-unlock me-1"></i> {{ $isOpenLabel }}
                </button>
            </form>
        @elseif($todayReport->isOpen())
            <form action="{{ route($route . '.close') }}" method="POST" class="confirmable-form"
                  data-message="{{ $isCloseTextLabel }}">
                @csrf
                <button type="submit" class="btn btn-dark">
                    <i class="fa fa-lock me-1"></i> {{ $isCloseLabel }}
                </button>
            </form>
        @endif
    @endif
</div>

<!-- Flash va confirm containerlari -->
<div id="flash-messages" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
<div id="custom-confirm-container"></div>

<script>
    // DELETE tugmasi funksiyasi
    function deleteButton(url, id) {
        showCustomConfirm('Ҳақиқатан ҳам ўчирмоқчимисиз?', () => {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    showFlashAlert(data.type || 'success', data.message);
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
        });
    }

    // Custom confirm yaratish
    function showCustomConfirm(message, onConfirm) {
        const container = document.getElementById('custom-confirm-container');
        const confirmBox = document.createElement('div');

        // 🔹 Ekran kengligini aniqlash
        const isMobile = window.innerWidth <= 768;

        confirmBox.className = 'custom-confirm';
        confirmBox.style.cssText = `
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
              align-items: ${isMobile ? 'flex-start' : 'center'};
            ${isMobile ? 'padding-top: 60px;' : ''}
            z-index: 9999;
        `;

        confirmBox.innerHTML = `
            <div style="
                background: #fff;
                padding: 20px 30px;
                border-radius: 12px;
                max-width: 400px;
                width: 100%;
                text-align: center;
                box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            ">
                <p style="margin-bottom: 20px; font-size: 1.1rem;">${message}</p>
                <button id="confirm-yes" style="
                    background: linear-gradient(135deg, #38b000, #70e000);
                    color: #fff;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 6px;
                    margin-right: 10px;
                    cursor: pointer;
                ">Ҳа</button>
                <button id="confirm-no" style="
                    background: linear-gradient(135deg, #ff3c38, #ff7b6a);
                    color: #fff;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                ">Йўқ</button>
            </div>
        `;

        container.appendChild(confirmBox);

        confirmBox.querySelector('#confirm-yes').addEventListener('click', () => {
            onConfirm();
            confirmBox.remove();
        });

        confirmBox.querySelector('#confirm-no').addEventListener('click', () => {
            confirmBox.remove();
        });
    }

    // 🔹 Cash open, close
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.confirmable-form');
        if (!form) return; // faqat confirmable-form bo'lsa ishlaydi

        e.preventDefault(); // formani to‘xtatamiz

        const message = form.dataset.message || 'Тасдиқлайсизми?';
        showCustomConfirm(message, () => form.submit());
    });

    // Flash alert yaratish funksiyasi
    function showFlashAlert(type, message) {
        const container = document.getElementById('flash-messages');

        const alert = document.createElement('div');
        alert.className = 'flash-alert d-flex justify-content-between align-items-center';
        alert.style.cssText = `
            min-width: 200px;
            max-width: 400px;
            padding: 0.75rem 1rem 0.75rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            transform: translateX(120%);
            color: #fff;
            font-weight: 500;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            background: ${getAlertGradient(type)};
        `;

        alert.innerHTML = `
            <span style="flex: 1; font-size: 1rem;">${message}</span>
            <button type="button" class="btn-close" aria-label="Close"
                style="width: 28px; height: 28px; opacity: 0.8; transition: all 0.3s ease;"
                onmouseover="this.style.opacity='1'; this.style.transform='scale(1.2)';"
                onmouseout="this.style.opacity='0.8'; this.style.transform='scale(1)';">
            </button>
        `;

        container.appendChild(alert);

        // animatsiya
        setTimeout(() => {
            alert.style.transition = 'transform 0.6s ease, opacity 0.6s ease';
            alert.style.transform = 'translateX(0)';
        }, 125);

        // avtomatik yopilish
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(120%)';
            setTimeout(() => alert.remove(), 500);
        }, 2500);

        // close tugmasi
        alert.querySelector('.btn-close').addEventListener('click', () => alert.remove());
    }

    // Gradientni aniqlash
    function getAlertGradient(type) {
        switch (type) {
            case 'success':
                return 'linear-gradient(135deg, #38b000, #70e000)';
            case 'error':
                return 'linear-gradient(135deg, #ff3c38, #f5656c)';
            case 'warning':
                return 'linear-gradient(135deg, #ffb703, #ffd60a)';
            case 'info':
                return 'linear-gradient(135deg, #0096c7, #00b4d8)';
            case 'delete':
                return 'linear-gradient(135deg, #ff3c38, #ff7b6a)';
            default:
                return '#333';
        }
    }
</script>
